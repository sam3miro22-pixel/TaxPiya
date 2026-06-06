<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/../database/taxpiya.sqlite');
$h = $pdo->query('SELECT password FROM users WHERE id=2')->fetchColumn();
$candidates = ['Taxpiya2026!', 'admin', 'Admin123', 'taxpiya', '123456', 'Taxpiya2025!', 'soporte123'];
foreach ($candidates as $p) {
    if (password_verify($p, $h)) {
        echo "ADMIN PASSWORD: {$p}\n";
        exit(0);
    }
}
echo "Admin password not in candidate list\n";
