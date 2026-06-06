<?php
/**
 * Seed unificado: admin + pasajero + conductor + empresa/flota demo.
 * Contraseña común: Taxpiya2026! (o TAXPIYA_DEMO_PASSWORD)
 *
 * Uso: php scripts/seed-all-demo.php
 */

$password = getenv('TAXPIYA_DEMO_PASSWORD') ?: 'Taxpiya2026!';
$dbPath = __DIR__ . '/../database/taxpiya.sqlite';

echo "=== TaxPiya — seed demo completo ===\n";
echo "Contraseña unificada: {$password}\n\n";

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA busy_timeout = 15000');
$pdo->exec('PRAGMA journal_mode = WAL');
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

// --- Admin con contraseña conocida ---
$adminEmail = 'admin.demo@taxpiya.com';
$adminPhone = '3001001001';

$stmt = $pdo->prepare('SELECT id FROM users WHERE user_role_id = 1 ORDER BY id LIMIT 1');
$stmt->execute();
$adminId = $stmt->fetchColumn();

if ($adminId) {
    $pdo->prepare('UPDATE users SET name=?, email=?, telefono=?, password=?, user_role_id=1, estado=1 WHERE id=?')
        ->execute(['Admin TaxPiya Demo', $adminEmail, $adminPhone, $hash, $adminId]);
    echo "Admin actualizado id={$adminId}\n";
} else {
    $pdo->prepare('INSERT INTO users (name,password,email,telefono,estado,user_role_id) VALUES (?,?,?,?,1,1)')
        ->execute(['Admin TaxPiya Demo', $hash, $adminEmail, $adminPhone]);
    echo "Admin creado id=" . $pdo->lastInsertId() . "\n";
}

$pdo->prepare('UPDATE users SET password=?, estado=1 WHERE email = ? AND user_role_id = 1')
    ->execute([$hash, 'soporte@taxpiya.com']);

$pdo = null;

// --- Pasajeros, conductores, empresa ---
$scripts = ['seed-demo-users.php', 'seed-demo-empresa.php'];
foreach ($scripts as $script) {
    $path = __DIR__ . '/' . $script;
    if (!is_file($path)) {
        echo "SKIP {$script} (no existe)\n";
        continue;
    }
    echo "\n>>> Ejecutando {$script}...\n";
    passthru('php ' . escapeshellarg($path), $code);
    if ($code !== 0) {
        echo "AVISO: {$script} terminó con código {$code}\n";
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           CREDENCIALES DEMO — TaxPiya                        ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ Contraseña para TODAS las cuentas: {$password}              ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ ADMIN          /index/login                                  ║\n";
echo "║   3001001001 | admin.demo@taxpiya.com | soporte@taxpiya.com ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ PASAJERO       /pasajero/login                               ║\n";
echo "║   3009001001 | pasajero.demo1@taxpiya.com                    ║\n";
echo "║   3009001002 | pasajero.demo2@taxpiya.com                    ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ CONDUCTOR      /conductor/login                              ║\n";
echo "║   3109001001 | conductor.demo1@taxpiya.com                   ║\n";
echo "║   3109001002 | conductor.demo2@taxpiya.com                   ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ EMPRESA/FLOTA  /empresa/login                                ║\n";
echo "║   3209002001 | empresa.demo@taxpiya.com                      ║\n";
echo "║   Empresa: TaxPiya Flota Demo (activa)                       ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ CONDUCTORES FLOTA (login conductor)                          ║\n";
echo "║   3219002001 | FLD001 | flota.conductor1@taxpiya.com         ║\n";
echo "║   3219002002 | FLD002 | flota.conductor2@taxpiya.com         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "OK\n";
