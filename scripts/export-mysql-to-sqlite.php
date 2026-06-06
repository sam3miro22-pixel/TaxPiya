<?php
/**
 * Exporta MySQL (local) a SQLite para deploy sin hosting MySQL.
 * Uso: php scripts/export-mysql-to-sqlite.php
 */

$mysqlHost = getenv('MYSQL_HOST') ?: '127.0.0.1';
$mysqlPort = getenv('MYSQL_PORT') ?: '3307';
$mysqlDb   = getenv('MYSQL_DATABASE') ?: 'taxpiya48_718txps7';
$mysqlUser = getenv('MYSQL_USER') ?: 'root';
$mysqlPass = getenv('MYSQL_PASSWORD') ?: '';

$outFile = __DIR__ . '/../database/taxpiya.sqlite';

if (file_exists($outFile)) {
    unlink($outFile);
}

$mysql = new PDO(
    "mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb};charset=utf8mb4",
    $mysqlUser,
    $mysqlPass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sqlite = new PDO('sqlite:' . $outFile, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$sqlite->exec('PRAGMA foreign_keys = OFF;');

$skipColumns = ['origen_ubicacion', 'destino_ubicacion', 'ubicacion'];

$tables = $mysql->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "Table: {$table}\n";

    $create = $mysql->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
    $ddl = $create['Create Table'] ?? '';
    if ($ddl === '') {
        continue;
    }

    $sqliteDdl = mysqlCreateToSqlite($ddl, $skipColumns);
    if ($sqliteDdl === null) {
        echo "  skip (unsupported)\n";
        continue;
    }

    $sqlite->exec("DROP TABLE IF EXISTS `{$table}`;");
    try {
        $sqlite->exec($sqliteDdl);
    } catch (Throwable $e) {
        echo "  skip ddl: {$e->getMessage()}\n";
        continue;
    }

    $cols = array_column(
        $mysql->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC),
        'Field'
    );
    $cols = array_values(array_filter($cols, fn ($c) => !in_array($c, $skipColumns, true)));
    if ($cols === []) {
        continue;
    }

    $colList = implode(',', array_map(fn ($c) => "`{$c}`", $cols));
    $rows = $mysql->query("SELECT {$colList} FROM `{$table}`");
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $insert = $sqlite->prepare("INSERT INTO `{$table}` ({$colList}) VALUES ({$placeholders})");

    $count = 0;
    while ($row = $rows->fetch(PDO::FETCH_NUM)) {
        $insert->execute($row);
        $count++;
    }
    echo "  rows: {$count}\n";
}

$sqlite->exec('PRAGMA foreign_keys = ON;');
echo "OK: {$outFile}\n";

function mysqlCreateToSqlite(string $ddl, array $skipColumns): ?string
{
    $ddl = preg_replace('/\/\*.*?\*\//s', '', $ddl);
    $ddl = preg_replace('/ENGINE=\w+[^;]*/i', '', $ddl);
    $ddl = preg_replace('/DEFAULT CHARSET=\w+[^;]*/i', '', $ddl);
    $ddl = preg_replace('/COLLATE=\w+[^,\)]*/i', '', $ddl);
    $ddl = preg_replace('/AUTO_INCREMENT=\d+/i', '', $ddl);
    $ddl = preg_replace('/\s+ON UPDATE CURRENT_TIMESTAMP/i', '', $ddl);

    $lines = [];
    if (!preg_match('/CREATE TABLE `([^`]+)` \((.*)\)/s', $ddl, $m)) {
        return null;
    }
    $table = $m[1];
    $body = $m[2];

    foreach (preg_split('/,\s*\n/', $body) as $part) {
        $part = trim($part);
        if ($part === '' || preg_match('/^(PRIMARY KEY|UNIQUE KEY|KEY |CONSTRAINT|FULLTEXT)/i', $part)) {
            continue;
        }
        if (!preg_match('/^`([^`]+)`\s+(.+)$/', $part, $colMatch)) {
            continue;
        }
        $col = $colMatch[1];
        if (in_array($col, $skipColumns, true)) {
            continue;
        }
        $type = $colMatch[2];
        $type = preg_replace("/\s+COMMENT\s+'[^']*'/i", '', $type);
        $type = preg_replace('/\bbigint\(\d+\)/i', 'INTEGER', $type);
        $type = preg_replace('/\bint\(\d+\)/i', 'INTEGER', $type);
        $type = preg_replace('/\bsmallint\(\d+\)/i', 'INTEGER', $type);
        $type = preg_replace('/\btinyint\(\d+\)/i', 'INTEGER', $type);
        $type = preg_replace('/\bdouble\b/i', 'REAL', $type);
        $type = preg_replace('/\bdecimal\([^)]+\)/i', 'REAL', $type);
        $type = preg_replace('/\bdatetime\b/i', 'TEXT', $type);
        $type = preg_replace('/\btimestamp\b/i', 'TEXT', $type);
        $type = preg_replace('/\bdate\b/i', 'TEXT', $type);
        $type = preg_replace('/\blongtext\b/i', 'TEXT', $type);
        $type = preg_replace('/\bmediumtext\b/i', 'TEXT', $type);
        $type = preg_replace('/\btext\b/i', 'TEXT', $type);
        $type = preg_replace('/\bvarbinary\(\d+\)/i', 'BLOB', $type);
        $type = preg_replace('/\bblob\b/i', 'BLOB', $type);
        $type = preg_replace('/\bvarchar\(\d+\)/i', 'TEXT', $type);
        $type = preg_replace('/\bchar\(\d+\)/i', 'TEXT', $type);
        $type = preg_replace('/\benum\([^)]+\)/i', 'TEXT', $type);
        $type = preg_replace('/\bpoint\b/i', 'TEXT', $type);
        $type = preg_replace('/\bunsigned\b/i', '', $type);
        $type = preg_replace('/\bNOT NULL\b/i', 'NOT NULL', $type);
        $type = preg_replace('/\s+DEFAULT\s+current_timestamp\(\)/i', '', $type);
        $type = preg_replace('/\s+DEFAULT\s+CURRENT_TIMESTAMP/i', '', $type);
        $type = preg_replace('/\s+DEFAULT\s+\'[^\']*\'/i', '', $type);
        $type = preg_replace('/\s+DEFAULT\s+[0-9.]+/i', '', $type);
        $type = preg_replace('/\s+DEFAULT NULL/i', '', $type);
        $type = preg_replace('/\s+AUTO_INCREMENT/i', ' PRIMARY KEY AUTOINCREMENT', $type);
        $type = preg_replace('/\s+ON\s+UPDATE\s+CURRENT_TIMESTAMP/i', '', $type);
        $type = preg_replace('/\(\)/', '', $type);
        $type = preg_replace('/,\s*$/', '', trim($type));
        $lines[] = "`{$col}` {$type}";
    }

    if ($lines === []) {
        return null;
    }

    return 'CREATE TABLE `' . $table . '` (' . implode(', ', $lines) . ');';
}
