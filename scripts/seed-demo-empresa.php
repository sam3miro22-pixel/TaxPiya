<?php
/**
 * Crea empresa demo en SQLite (1 cuenta por rol).
 * Uso: php scripts/seed-demo-empresa.php
 */

$dbPath = __DIR__ . '/../database/taxpiya.sqlite';
$password = getenv('TAXPIYA_DEMO_PASSWORD') ?: 'Taxpiya2026!';
$now = date('Y-m-d H:i:s');

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA busy_timeout = 15000');
$pdo->exec('PRAGMA journal_mode = WAL');
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

function ensureSchema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS empresas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL UNIQUE,
        nombre_comercial TEXT NOT NULL,
        razon_social TEXT,
        nit TEXT,
        telefono TEXT,
        email TEXT,
        ciudad TEXT DEFAULT 'Medellín',
        direccion TEXT,
        estado TEXT DEFAULT 'pendiente',
        verificacion_estado TEXT DEFAULT 'pendiente',
        notas TEXT,
        created_at TEXT,
        updated_at TEXT
    )");

    $role = $pdo->query("SELECT role_id FROM roles WHERE role_name = 'Empresa' LIMIT 1")->fetchColumn();
    if (!$role) {
        $pdo->prepare("INSERT INTO roles (role_id, role_name) VALUES (4, 'Empresa')")->execute();
    }
}

function upsertUser(PDO $pdo, array $u, string $hash, string $now): int
{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR telefono = ? LIMIT 1');
    $stmt->execute([$u['email'], $u['telefono']]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        $pdo->prepare('UPDATE users SET name=?, password=?, user_role_id=?, estado=1 WHERE id=?')
            ->execute([$u['name'], $hash, $u['role_id'], $existing]);
        return (int) $existing;
    }

    $pdo->prepare('INSERT INTO users (name,password,email,telefono,estado,user_role_id) VALUES (?,?,?,?,1,?)')
        ->execute([$u['name'], $hash, $u['email'], $u['telefono'], $u['role_id']]);
    return (int) $pdo->lastInsertId();
}

ensureSchema($pdo);

$roleEmpresa = (int) ($pdo->query("SELECT role_id FROM roles WHERE role_name = 'Empresa' LIMIT 1")->fetchColumn() ?: 4);

echo "=== Empresa demo ===\n";

$owner = [
    'name'     => 'Empresa Demo TaxPiya',
    'email'    => 'empresa.demo@taxpiya.com',
    'telefono' => '3209002001',
    'role_id'  => $roleEmpresa,
];
$userId = upsertUser($pdo, $owner, $hash, $now);
echo "Usuario empresa id={$userId}\n";

$stmt = $pdo->prepare('SELECT id FROM empresas WHERE user_id = ? LIMIT 1');
$stmt->execute([$userId]);
$empresaId = $stmt->fetchColumn();

if (!$empresaId) {
    $pdo->prepare('INSERT INTO empresas (user_id,nombre_comercial,razon_social,nit,telefono,email,ciudad,direccion,estado,verificacion_estado,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([
            $userId,
            'TaxPiya Empresa Demo',
            'Cooperativa Taxis Medellín Demo SAS',
            '900.200.001-1',
            '3209002001',
            'empresa.demo@taxpiya.com',
            'Medellín',
            'Calle 50 # 45-20',
            'activa',
            'verificado',
            $now,
            $now,
        ]);
    $empresaId = (int) $pdo->lastInsertId();
} else {
    $pdo->prepare('UPDATE empresas SET nombre_comercial=?, estado=?, verificacion_estado=?, updated_at=? WHERE id=?')
        ->execute(['TaxPiya Empresa Demo', 'activa', 'verificado', $now, $empresaId]);
    $empresaId = (int) $empresaId;
}
echo "Empresa id={$empresaId}\n";

echo "\n=== CREDENCIALES DEMO EMPRESA ===\n";
echo "Login empresa: 3209002001 o empresa.demo@taxpiya.com\n";
echo "Contraseña: {$password}\n";
echo "URL: /empresa/login\n";
echo "\nOK\n";
