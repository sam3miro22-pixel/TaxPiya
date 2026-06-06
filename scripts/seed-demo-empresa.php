<?php
/**
 * Crea empresa demo + flota de 2 taxis en SQLite.
 * Uso: php scripts/seed-demo-empresa.php
 */

$dbPath = __DIR__ . '/../database/taxpiya.sqlite';
$password = getenv('TAXPIYA_DEMO_PASSWORD') ?: 'Taxpiya2026!';
$now = date('Y-m-d H:i:s');

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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

    $cols = $pdo->query("PRAGMA table_info(conductores)")->fetchAll(PDO::FETCH_ASSOC);
    $hasEmpresa = false;
    foreach ($cols as $c) {
        if ($c['name'] === 'empresa_id') {
            $hasEmpresa = true;
            break;
        }
    }
    if (!$hasEmpresa) {
        $pdo->exec('ALTER TABLE conductores ADD COLUMN empresa_id INTEGER NULL');
    }

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

function seedWallet(PDO $pdo, int $conductorId, float $balance = 80000): void
{
    $minOp = 5000;
    $idempotencia = "recarga_inicial_{$conductorId}";
    $stmt = $pdo->prepare('SELECT id FROM wallet_movimientos WHERE idempotencia = ? AND anulado = 0 LIMIT 1');
    $stmt->execute([$idempotencia]);
    if ($stmt->fetchColumn()) {
        return;
    }

    $now = date('Y-m-d H:i:s');
    $pdo->prepare('INSERT OR IGNORE INTO wallet_saldos (conductor_id,saldo_actual,saldo_reservado,min_operativo,moneda,bloqueado,created_at,updated_at) VALUES (?,0,0,?,?,0,?,?)')
        ->execute([$conductorId, $minOp, 'COP', $now, $now]);

    $pdo->prepare('INSERT INTO wallet_movimientos (conductor_id,sentido,motivo,monto,moneda,saldo_antes,saldo_despues,descripcion,idempotencia,anulado,created_at) VALUES (?,?,?,?,?,?,?,?,?,0,?)')
        ->execute([$conductorId, 'credito', 'recarga', $balance, 'COP', 0, $balance, 'Saldo inicial flota demo', $idempotencia, $now]);

    $movId = (int) $pdo->lastInsertId();
    $pdo->prepare('UPDATE wallet_saldos SET saldo_actual=?, last_movimiento_id=?, last_movimiento_at=?, updated_at=? WHERE conductor_id=?')
        ->execute([$balance, $movId, $now, $now, $conductorId]);
}

ensureSchema($pdo);

$roleEmpresa = (int) ($pdo->query("SELECT role_id FROM roles WHERE role_name = 'Empresa' LIMIT 1")->fetchColumn() ?: 4);
$roleConductor = (int) ($pdo->query("SELECT role_id FROM roles WHERE role_name = 'Conductor' LIMIT 1")->fetchColumn() ?: 3);

echo "=== Empresa demo ===\n";

$owner = [
    'name'     => 'Admin Flota Demo',
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
            'TaxPiya Flota Demo',
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
        ->execute(['TaxPiya Flota Demo', 'activa', 'verificado', $now, $empresaId]);
    $empresaId = (int) $empresaId;
}
echo "Empresa id={$empresaId}\n";

$taxis = [
    [
        'name' => 'Conductor Flota 1',
        'email' => 'flota.conductor1@taxpiya.com',
        'telefono' => '3219002001',
        'placa' => 'FLD001',
        'marca' => 'Chevrolet',
        'linea' => 'Spark GT',
        'lat' => 6.2442,
        'lng' => -75.5812,
    ],
    [
        'name' => 'Conductor Flota 2',
        'email' => 'flota.conductor2@taxpiya.com',
        'telefono' => '3219002002',
        'placa' => 'FLD002',
        'marca' => 'Hyundai',
        'linea' => 'Grand i10',
        'lat' => 6.2476,
        'lng' => -75.5658,
    ],
];

foreach ($taxis as $t) {
    echo "\n--- Taxi {$t['placa']} ---\n";
    $driverUserId = upsertUser($pdo, [
        'name' => $t['name'],
        'email' => $t['email'],
        'telefono' => $t['telefono'],
        'role_id' => $roleConductor,
    ], $hash, $now);

    $stmt = $pdo->prepare('SELECT id FROM conductores WHERE user_id = ? LIMIT 1');
    $stmt->execute([$driverUserId]);
    $conductorId = $stmt->fetchColumn();

    if (!$conductorId) {
        $pdo->prepare('INSERT INTO conductores (user_id,empresa_id,estado_operitivo,disponible,total_viajes,verificacion_estado,verificacion_nivel,created_at,updated_at) VALUES (?,?,1,1,0,?,0,?,?)')
            ->execute([$driverUserId, $empresaId, 'verificado', $now, $now]);
        $conductorId = (int) $pdo->lastInsertId();
    } else {
        $pdo->prepare('UPDATE conductores SET empresa_id=?, estado_operitivo=1, disponible=1, verificacion_estado=?, updated_at=? WHERE id=?')
            ->execute([$empresaId, 'verificado', $now, $conductorId]);
        $conductorId = (int) $conductorId;
    }

    $stmt = $pdo->prepare('SELECT id FROM vehiculos WHERE conductor_id = ? LIMIT 1');
    $stmt->execute([$conductorId]);
    if (!$stmt->fetchColumn()) {
        $pdo->prepare('INSERT INTO vehiculos (conductor_id,placa,marca,linea,modelo_anio,color,categoria,asientos,estado_vehiculo,verificacion_estado,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$conductorId, $t['placa'], $t['marca'], $t['linea'], 2022, 'Amarillo', 'taxi', 4, 'activo', 'verificado', $now, $now]);
    }

    $pdo->prepare('INSERT OR REPLACE INTO conductor_posicion_actual (conductor_id,lat,lng,created_at,actualizada_at) VALUES (?,?,?,?,?)')
        ->execute([$conductorId, $t['lat'], $t['lng'], $now, $now]);

    seedWallet($pdo, $conductorId);
    echo "Conductor id={$conductorId} placa={$t['placa']}\n";
}

echo "\n=== CREDENCIALES DEMO EMPRESA ===\n";
echo "Login empresa: 3209002001 o empresa.demo@taxpiya.com\n";
echo "Contraseña: {$password}\n";
echo "URL: /empresa/login\n";
echo "\nConductores de la flota (login conductor app):\n";
echo "  3219002001 / {$password}\n";
echo "  3219002002 / {$password}\n";
echo "\nOK\n";
