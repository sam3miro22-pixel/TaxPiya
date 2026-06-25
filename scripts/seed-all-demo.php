<?php
/**
 * Seed unificado: 1 cuenta demo por rol (admin, pasajero, conductor, empresa).
 * Contraseña común: Taxpiya2026! (o TAXPIYA_DEMO_PASSWORD)
 *
 * Uso: php scripts/seed-all-demo.php
 */

$password = getenv('TAXPIYA_DEMO_PASSWORD') ?: 'Taxpiya2026!';
$dbPath = __DIR__ . '/../database/taxpiya.sqlite';

echo "=== TaxPiya — seed demo (1 por rol) ===\n";
echo "Contraseña unificada: {$password}\n\n";

$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('PRAGMA busy_timeout = 15000');
$pdo->exec('PRAGMA journal_mode = WAL');
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

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

$pdo = null;

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
echo "║           CREDENCIALES DEMO — TaxPiya (1 por rol)            ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ Contraseña para TODAS las cuentas: {$password}              ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ ADMIN          /index/login                                  ║\n";
echo "║   3001001001 | admin.demo@taxpiya.com                        ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ PASAJERO       /pasajero/login (Firebase)                    ║\n";
echo "║   3009001001 | pasajero.demo1@taxpiya.com                    ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ CONDUCTOR      /conductor/login (Firebase)                   ║\n";
echo "║   3109001001 | conductor.demo1@taxpiya.com                   ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ EMPRESA        /empresa/login                                ║\n";
echo "║   3209002001 | empresa.demo@taxpiya.com                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "OK\n";
