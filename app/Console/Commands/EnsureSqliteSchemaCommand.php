<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureSqliteSchemaCommand extends Command
{
    protected $signature = 'taxpiya:ensure-schema';

    protected $description = 'Crea tablas/columnas críticas faltantes en SQLite (Render)';

    public function handle(): int
    {
        if (config('database.default') !== 'sqlite') {
            $this->line('Omitido: no es SQLite.');
            return self::SUCCESS;
        }

        $pdo = DB::connection()->getPdo();
        $pdo->exec('PRAGMA busy_timeout = 15000');
        $pdo->exec('PRAGMA journal_mode = WAL');

        $this->ensureTable($pdo, 'conductor_posicion_actual', <<<'SQL'
CREATE TABLE IF NOT EXISTS conductor_posicion_actual (
    conductor_id INTEGER PRIMARY KEY,
    viaje_id INTEGER NULL,
    lat REAL NULL,
    lng REAL NULL,
    precision_m REAL NULL,
    velocidad_kmh REAL NULL,
    heading REAL NULL,
    origen TEXT NULL,
    provider TEXT NULL,
    bateria REAL NULL,
    app_estado TEXT NULL,
    created_at TEXT NULL,
    actualizada_at TEXT NULL
)
SQL);

        $this->ensureTable($pdo, 'wallet_saldos', <<<'SQL'
CREATE TABLE IF NOT EXISTS wallet_saldos (
    conductor_id INTEGER PRIMARY KEY,
    saldo_actual REAL NOT NULL DEFAULT 0,
    saldo_reservado REAL NOT NULL DEFAULT 0,
    min_operativo REAL NOT NULL DEFAULT 5000,
    moneda TEXT NOT NULL DEFAULT 'COP',
    last_movimiento_id INTEGER NULL,
    last_movimiento_at TEXT NULL,
    bloqueado INTEGER NOT NULL DEFAULT 0,
    motivo_bloqueo TEXT NULL,
    created_at TEXT NULL,
    updated_at TEXT NULL
)
SQL);

        $this->ensureTable($pdo, 'wallet_movimientos', <<<'SQL'
CREATE TABLE IF NOT EXISTS wallet_movimientos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    conductor_id INTEGER NOT NULL,
    viaje_id INTEGER NULL,
    admin_user_id INTEGER NULL,
    sentido TEXT NOT NULL,
    motivo TEXT NOT NULL,
    monto REAL NOT NULL,
    moneda TEXT NOT NULL DEFAULT 'COP',
    saldo_antes REAL NULL,
    saldo_despues REAL NULL,
    descripcion TEXT NULL,
    referencia_externa TEXT NULL,
    idempotencia TEXT NULL,
    anulado INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NULL
)
SQL);

        $this->ensureTable($pdo, 'empresas', <<<'SQL'
CREATE TABLE IF NOT EXISTS empresas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE,
    nombre_comercial TEXT NOT NULL,
    razon_social TEXT NULL,
    nit TEXT NULL,
    telefono TEXT NULL,
    email TEXT NULL,
    ciudad TEXT DEFAULT 'Medellín',
    direccion TEXT NULL,
    estado TEXT DEFAULT 'pendiente',
    verificacion_estado TEXT DEFAULT 'pendiente',
    notas TEXT NULL,
    created_at TEXT NULL,
    updated_at TEXT NULL
)
SQL);

        $this->ensureColumn($pdo, 'conductores', 'empresa_id', 'INTEGER NULL');
        $this->ensureColumn($pdo, 'conductores', 'disponible', 'INTEGER NOT NULL DEFAULT 0');
        $this->ensureColumn($pdo, 'conductores', 'estado_operitivo', 'INTEGER NOT NULL DEFAULT 0');

        if (Schema::hasTable('roles') && !DB::table('roles')->where('role_name', 'Empresa')->exists()) {
            $roleId = (int) (DB::table('roles')->max('role_id') ?? 0) + 1;
            DB::table('roles')->insert(['role_id' => $roleId, 'role_name' => 'Empresa']);
        }

        $this->ensureEmpresasPermissions();

        $this->info('Schema SQLite verificado.');
        return self::SUCCESS;
    }

    private function ensureTable(\PDO $pdo, string $table, string $sql): void
    {
        if ($this->tableExists($pdo, $table)) {
            $this->line("  OK tabla {$table}");
            return;
        }
        $pdo->exec($sql);
        $this->warn("  Creada tabla {$table}");
    }

    private function ensureColumn(\PDO $pdo, string $table, string $column, string $definition): void
    {
        if (!$this->tableExists($pdo, $table)) {
            return;
        }
        if ($this->columnExists($pdo, $table, $column)) {
            return;
        }
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        $this->warn("  Columna {$table}.{$column} creada");
    }

    private function ensureEmpresasPermissions(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $paths = ['empresas/index', 'empresas/view', 'empresas/edit'];
        $adminRoleId = DB::table('roles')->where('role_name', 'Admin')->value('role_id') ?: 1;

        foreach ($paths as $path) {
            $exists = DB::table('permissions')
                ->where('permission', $path)
                ->where('role_id', $adminRoleId)
                ->exists();
            if (!$exists) {
                DB::table('permissions')->insert([
                    'permission' => $path,
                    'role_id'    => $adminRoleId,
                ]);
                $this->warn("  Permiso admin: {$path}");
            }
        }
    }

    private function tableExists(\PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ? LIMIT 1");
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    }

    private function columnExists(\PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->query("PRAGMA table_info({$table})");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (($row['name'] ?? '') === $column) {
                return true;
            }
        }
        return false;
    }
}
