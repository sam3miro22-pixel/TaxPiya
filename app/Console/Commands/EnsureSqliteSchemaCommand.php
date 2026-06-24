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
        $this->ensureColumn($pdo, 'users', 'firebase_uid', 'TEXT NULL');
        $this->ensureColumn($pdo, 'users', 'codigo_referido', 'TEXT NULL');
        $this->ensureColumn($pdo, 'empresas', 'codigo_referido', 'TEXT NULL');

        $this->ensureTable($pdo, 'wallet_cuentas', <<<'SQL'
CREATE TABLE IF NOT EXISTS wallet_cuentas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT NOT NULL,
    user_id INTEGER NULL,
    conductor_id INTEGER NULL,
    empresa_id INTEGER NULL,
    saldo_actual REAL NOT NULL DEFAULT 0,
    saldo_reservado REAL NOT NULL DEFAULT 0,
    min_operativo REAL NOT NULL DEFAULT 0,
    moneda TEXT NOT NULL DEFAULT 'COP',
    bloqueado INTEGER NOT NULL DEFAULT 0,
    motivo_bloqueo TEXT NULL,
    puede_depositar INTEGER NOT NULL DEFAULT 1,
    puede_retirar INTEGER NOT NULL DEFAULT 0,
    solo_lectura INTEGER NOT NULL DEFAULT 0,
    last_movimiento_id INTEGER NULL,
    last_movimiento_at TEXT NULL,
    created_at TEXT NULL,
    updated_at TEXT NULL
)
SQL);

        $this->ensureColumn($pdo, 'wallet_cuentas', 'motivo_bloqueo', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'puede_depositar', 'INTEGER NOT NULL DEFAULT 1');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'puede_retirar', 'INTEGER NOT NULL DEFAULT 0');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'solo_lectura', 'INTEGER NOT NULL DEFAULT 0');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'last_movimiento_id', 'INTEGER NULL');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'last_movimiento_at', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'created_at', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_cuentas', 'updated_at', 'TEXT NULL');

        $this->ensureTable($pdo, 'wallet_solicitudes', <<<'SQL'
CREATE TABLE IF NOT EXISTS wallet_solicitudes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cuenta_id INTEGER NOT NULL,
    operacion TEXT NOT NULL,
    monto REAL NOT NULL,
    moneda TEXT NOT NULL DEFAULT 'COP',
    estado TEXT NOT NULL DEFAULT 'pendiente',
    metodo_pago TEXT NULL,
    referencia_pago TEXT NULL,
    notas TEXT NULL,
    procesado_por INTEGER NULL,
    movimiento_id INTEGER NULL,
    created_at TEXT NULL,
    updated_at TEXT NULL
)
SQL);

        $this->ensureColumn($pdo, 'wallet_solicitudes', 'comprobante_path', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_solicitudes', 'solicitante_user_id', 'INTEGER NULL');

        $this->ensureColumn($pdo, 'wallet_movimientos', 'cuenta_id', 'INTEGER NULL');
        $this->ensureColumn($pdo, 'wallet_movimientos', 'tipo_operacion', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_movimientos', 'estado', "TEXT NOT NULL DEFAULT 'completado'");
        $this->ensureColumn($pdo, 'wallet_movimientos', 'metodo_pago', 'TEXT NULL');
        $this->ensureColumn($pdo, 'wallet_movimientos', 'empresa_id', 'INTEGER NULL');
        $this->ensureColumn($pdo, 'wallet_movimientos', 'user_id', 'INTEGER NULL');

        $this->ensureColumn($pdo, 'referidos', 'bonus_monto', 'REAL NULL');
        $this->ensureColumn($pdo, 'referidos', 'bonus_paid_at', 'TEXT NULL');

        $this->ensureColumn($pdo, 'viajes', 'codigo_llegada', 'TEXT NULL');

        $this->ensureTable($pdo, 'password_reset_tokens', <<<'SQL'
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email TEXT PRIMARY KEY,
    token TEXT NOT NULL,
    created_at TEXT NULL
)
SQL);

        $this->ensureTable($pdo, 'vehiculo_conductores', <<<'SQL'
CREATE TABLE IF NOT EXISTS vehiculo_conductores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vehiculo_id INTEGER NOT NULL,
    conductor_id INTEGER NOT NULL,
    activo INTEGER NOT NULL DEFAULT 1,
    es_titular INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NULL,
    updated_at TEXT NULL,
    UNIQUE(vehiculo_id, conductor_id)
)
SQL);
        $pdo->exec('CREATE INDEX IF NOT EXISTS vehiculo_conductores_conductor_idx ON vehiculo_conductores(conductor_id)');

        $this->ensureTable($pdo, 'sos_incidentes', <<<'SQL'
CREATE TABLE IF NOT EXISTS sos_incidentes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    viaje_id INTEGER NULL,
    actor_tipo TEXT NOT NULL DEFAULT 'pasajero',
    actor_user_id INTEGER NULL,
    conductor_id INTEGER NULL,
    categoria TEXT DEFAULT 'seguridad',
    severidad TEXT NOT NULL DEFAULT 'alta',
    estado TEXT NOT NULL DEFAULT 'abierto',
    descripcion TEXT NULL,
    telefono_contacto TEXT NULL,
    lat REAL NULL,
    lng REAL NULL,
    operador_id INTEGER NULL,
    created_at TEXT NULL
)
SQL);

        app(\App\Services\VehiculoConductorService::class)->backfillFromLegacy();

        $this->ensureTable($pdo, 'sessions', <<<'SQL'
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INTEGER NULL,
    ip_address VARCHAR(64) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
)
SQL);
        $pdo->exec('CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions(user_id)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions(last_activity)');
        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS referidos_referred_user_unique ON referidos(referred_user_id)');
        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS users_codigo_referido_unique ON users(codigo_referido) WHERE codigo_referido IS NOT NULL AND codigo_referido != ""');

        $this->ensureTable($pdo, 'referidos', <<<'SQL'
CREATE TABLE IF NOT EXISTS referidos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    codigo_usado TEXT NOT NULL,
    referrer_tipo TEXT NOT NULL,
    referrer_user_id INTEGER NULL,
    referrer_empresa_id INTEGER NULL,
    referred_user_id INTEGER NOT NULL,
    tipo_referido TEXT NOT NULL,
    estado TEXT NOT NULL DEFAULT 'registrado',
    bonus_monto REAL NULL,
    bonus_paid_at TEXT NULL,
    notas TEXT NULL,
    created_at TEXT NULL,
    updated_at TEXT NULL
)
SQL);

        $this->ensureDefaultRoles();

        $this->ensureEmpresasPermissions();
        $this->ensureReferidosPermissions();
        app(\App\Services\ReferralService::class)->backfillCodes();
        $this->backfillWalletCuentas();

        $repair = app(\App\Services\UserAccountService::class)->repairDuplicateAccounts();
        if (($repair['merged'] ?? 0) > 0) {
            $this->line("  Cuentas duplicadas fusionadas: {$repair['merged']} ({$repair['groups']} grupos)");
        }

        $bonos = app(\App\Services\ReferralService::class)->backfillAllUnpaidBonuses();
        if ($bonos > 0) {
            $this->line("  Bonos referidos acreditados: {$bonos}");
        }

        $this->info('Schema SQLite verificado.');
        return self::SUCCESS;
    }

    private function ensureDefaultRoles(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        $defaults = [
            1 => 'Admin',
            2 => 'Pasajero',
            3 => 'Conductor',
            4 => 'Empresa',
        ];

        foreach ($defaults as $roleId => $roleName) {
            $exists = DB::table('roles')->where('role_id', $roleId)->orWhere('role_name', $roleName)->exists();
            if (!$exists) {
                DB::table('roles')->insert(['role_id' => $roleId, 'role_name' => $roleName]);
                $this->warn("  Rol creado: {$roleName}");
            }
        }
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

    private function repairIndependentConductors(\PDO $pdo): void
    {
        if (!Schema::hasTable('conductores') || !Schema::hasColumn('conductores', 'empresa_id')) {
            return;
        }

        $independentPhones = ['3109001001', '3109001002'];
        foreach ($independentPhones as $phone) {
            $stmt = $pdo->prepare('SELECT c.id FROM conductores c JOIN users u ON u.id = c.user_id WHERE u.telefono = ? LIMIT 1');
            $stmt->execute([$phone]);
            $cid = $stmt->fetchColumn();
            if ($cid) {
                $pdo->prepare('UPDATE conductores SET empresa_id = NULL WHERE id = ?')->execute([(int) $cid]);
            }
        }

        $pdo->exec('UPDATE conductores SET empresa_id = NULL WHERE empresa_id IS NOT NULL AND empresa_id = 0');
    }

    private function backfillWalletCuentas(): void
    {
        if (!Schema::hasTable('wallet_cuentas')) {
            return;
        }

        $ledger = app(\App\Services\WalletLedgerService::class);

        $pasajeros = DB::table('users')->where('user_role_id', 2)->pluck('id');
        foreach ($pasajeros as $uid) {
            $ledger->ensureCuenta('pasajero', (int) $uid);
        }

        $this->repairIndependentConductors($pdo);

        $conductores = DB::table('conductores')->pluck('id');
        foreach ($conductores as $cid) {
            $ledger->ensureCuenta('conductor', (int) $cid);
            $ledger->syncConductorPermissions((int) $cid);
        }

        $empresas = DB::table('empresas')->pluck('id');
        foreach ($empresas as $eid) {
            $ledger->ensureCuenta('empresa', (int) $eid);
        }

        if (Schema::hasColumn('wallet_movimientos', 'cuenta_id')) {
            $sinCuenta = DB::table('wallet_movimientos')
                ->whereNull('cuenta_id')
                ->whereNotNull('conductor_id')
                ->select('conductor_id')
                ->distinct()
                ->pluck('conductor_id');

            foreach ($sinCuenta as $cid) {
                $cuenta = $ledger->ensureCuenta('conductor', (int) $cid);
                if ($cuenta) {
                    DB::table('wallet_movimientos')
                        ->where('conductor_id', $cid)
                        ->whereNull('cuenta_id')
                        ->update(['cuenta_id' => $cuenta->id]);
                }
            }
        }
    }

    private function ensureReferidosPermissions(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $adminRoleId = DB::table('roles')->where('role_name', 'Admin')->value('role_id') ?: 1;
        foreach (['referidos/index', 'walletsolicitudes/index', 'walletsolicitudes/view', 'walletsolicitudes/aprobar', 'walletsolicitudes/rechazar', 'walletmovimientos/index', 'walletmovimientos/view', 'walletsaldos/index', 'walletsaldos/view', 'api/admin'] as $path) {
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
