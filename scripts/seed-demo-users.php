<?php
/**
 * Crea usuarios demo en SQLite + Firebase Auth.
 * Uso: php scripts/seed-demo-users.php
 */

$apiKey = getenv('FIREBASE_API_KEY') ?: 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk';
$dbPath = __DIR__ . '/../database/taxpiya.sqlite';
$password = 'Taxpiya2026!';

$users = [
    [
        'name'     => 'Pasajero Demo 1',
        'email'    => 'pasajero.demo1@taxpiya.com',
        'telefono' => '3009001001',
        'role_id'  => 2,
        'conductor'=> false,
        'lat'      => null,
        'lng'      => null,
    ],
    [
        'name'     => 'Pasajero Demo 2',
        'email'    => 'pasajero.demo2@taxpiya.com',
        'telefono' => '3009001002',
        'role_id'  => 2,
        'conductor'=> false,
        'lat'      => null,
        'lng'      => null,
    ],
    [
        'name'     => 'Conductor Demo 1',
        'email'    => 'conductor.demo1@taxpiya.com',
        'telefono' => '3109001001',
        'role_id'  => 3,
        'conductor'=> true,
        'lat'      => 6.2476,
        'lng'      => -75.5658,
    ],
    [
        'name'     => 'Conductor Demo 2',
        'email'    => 'conductor.demo2@taxpiya.com',
        'telefono' => '3109001002',
        'role_id'  => 3,
        'conductor'=> true,
        'lat'      => 6.2510,
        'lng'      => -75.5600,
    ],
];

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA busy_timeout = 15000');
$pdo->exec('PRAGMA journal_mode = WAL');
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
$now = date('Y-m-d H:i:s');

foreach ($users as $u) {
    echo "\n=== {$u['email']} ===\n";

    $firebaseUid = firebaseSignUp($apiKey, $u['email'], $password);
    echo "Firebase UID: {$firebaseUid}\n";

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR telefono = ? LIMIT 1');
    $stmt->execute([$u['email'], $u['telefono']]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        $pdo->prepare('UPDATE users SET firebase_uid=?, name=?, password=?, user_role_id=?, estado=1 WHERE id=?')
            ->execute([$firebaseUid, $u['name'], $hash, $u['role_id'], $existing]);
        $userId = (int) $existing;
        echo "Usuario SQLite actualizado id={$userId}\n";
    } else {
        $pdo->prepare('INSERT INTO users (firebase_uid,name,password,email,telefono,estado,user_role_id) VALUES (?,?,?,?,?,1,?)')
            ->execute([$firebaseUid, $u['name'], $hash, $u['email'], $u['telefono'], $u['role_id']]);
        $userId = (int) $pdo->lastInsertId();
        echo "Usuario SQLite creado id={$userId}\n";
    }

    if ($u['conductor']) {
        $stmt = $pdo->prepare('SELECT id FROM conductores WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $conductorId = $stmt->fetchColumn();

        if (!$conductorId) {
            $pdo->prepare('INSERT INTO conductores (user_id,estado_operitivo,disponible,total_viajes,verificacion_estado,verificacion_nivel,created_at,updated_at) VALUES (?,1,1,0,?,0,?,?)')
                ->execute([$userId, 'verificado', $now, $now]);
            $conductorId = (int) $pdo->lastInsertId();
            echo "Conductor creado id={$conductorId}\n";
        } else {
            $pdo->prepare('UPDATE conductores SET estado_operitivo=1, disponible=1, verificacion_estado=?, updated_at=? WHERE id=?')
                ->execute(['verificado', $now, $conductorId]);
            echo "Conductor actualizado id={$conductorId}\n";
        }

        $pdo->prepare('INSERT OR REPLACE INTO conductor_posicion_actual (conductor_id,lat,lng,created_at,actualizada_at) VALUES (?,?,?,?,?)')
            ->execute([(int) $conductorId, $u['lat'], $u['lng'], $now, $now]);
        echo "Posición GPS registrada\n";

        seedWallet($pdo, (int) $conductorId);
        seedVehiculo($pdo, (int) $conductorId, $u['telefono']);
    }
}

echo "\nOK — contraseña para todos: {$password}\n";

function seedVehiculo(PDO $pdo, int $conductorId, string $telefono): void
{
    $stmt = $pdo->prepare('SELECT id FROM vehiculos WHERE conductor_id = ? LIMIT 1');
    $stmt->execute([$conductorId]);
    if ($stmt->fetchColumn()) {
        return;
    }
    $suffix = substr(preg_replace('/\D/', '', $telefono), -3);
    $now = date('Y-m-d H:i:s');
    $pdo->prepare('INSERT INTO vehiculos (conductor_id,placa,marca,linea,modelo_anio,color,categoria,asientos,estado_vehiculo,verificacion_estado,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$conductorId, 'TXD' . $suffix, 'Chevrolet', 'Spark', 2020, 'Amarillo', 'taxi', 4, 'activo', 'verificado', $now, $now]);
    echo "Vehículo demo registrado\n";
}

function seedWallet(PDO $pdo, int $conductorId): void
{
    $balance = (float) (getenv('TAXPIYA_WALLET_DEMO_BALANCE') ?: 100000);
    $minOp = (float) (getenv('TAXPIYA_WALLET_MIN_OPERATIVO') ?: 5000);

    $stmt = $pdo->prepare('SELECT saldo_actual FROM wallet_saldos WHERE conductor_id = ? LIMIT 1');
    $stmt->execute([$conductorId]);
    $existing = $stmt->fetchColumn();

    if ($existing !== false && (float) $existing > 0) {
        echo "Wallet ya tiene saldo ({$existing})\n";
        return;
    }

    $idempotencia = "recarga_inicial_{$conductorId}";
    $stmt = $pdo->prepare('SELECT id FROM wallet_movimientos WHERE idempotencia = ? AND anulado = 0 LIMIT 1');
    $stmt->execute([$idempotencia]);
    if ($stmt->fetchColumn()) {
        echo "Wallet recarga inicial ya registrada\n";
        return;
    }

    $pdo->prepare('INSERT OR IGNORE INTO wallet_saldos (conductor_id,saldo_actual,saldo_reservado,min_operativo,moneda,bloqueado,created_at,updated_at) VALUES (?,0,0,?,?,0,?,?)')
        ->execute([$conductorId, $minOp, 'COP', $now = date('Y-m-d H:i:s'), $now]);

    $pdo->prepare('INSERT INTO wallet_movimientos (conductor_id,sentido,motivo,monto,moneda,saldo_antes,saldo_despues,descripcion,idempotencia,anulado,created_at) VALUES (?,?,?,?,?,?,?,?,?,0,?)')
        ->execute([$conductorId, 'credito', 'recarga', $balance, 'COP', 0, $balance, 'Saldo inicial demo TaxPiya', $idempotencia, $now]);

    $movId = (int) $pdo->lastInsertId();
    $pdo->prepare('UPDATE wallet_saldos SET saldo_actual=?, last_movimiento_id=?, last_movimiento_at=?, updated_at=? WHERE conductor_id=?')
        ->execute([$balance, $movId, $now, $now, $conductorId]);

    echo "Wallet demo: \${$balance} COP (min operativo \${$minOp})\n";
}

function firebaseSignUp(string $apiKey, string $email, string $password): string
{
    $url = 'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . urlencode($apiKey);
    $payload = json_encode([
        'email'             => $email,
        'password'          => $password,
        'returnSecureToken' => true,
    ]);

    $response = httpPost($url, $payload);
    $data = json_decode($response, true);

    if (!empty($data['localId'])) {
        return $data['localId'];
    }

    if (($data['error']['message'] ?? '') === 'EMAIL_EXISTS') {
        $signInUrl = 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=' . urlencode($apiKey);
        $signIn = json_decode(httpPost($signInUrl, json_encode([
            'email'             => $email,
            'password'          => $password,
            'returnSecureToken' => true,
        ])), true);
        if (!empty($signIn['localId'])) {
            return $signIn['localId'];
        }
        throw new RuntimeException("Email existe pero login falló: {$email}");
    }

    throw new RuntimeException('Firebase error: ' . ($data['error']['message'] ?? $response));
}

function httpPost(string $url, string $body): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $out = curl_exec($ch);
    if ($out === false) {
        throw new RuntimeException('curl: ' . curl_error($ch));
    }
    curl_close($ch);
    return $out;
}
