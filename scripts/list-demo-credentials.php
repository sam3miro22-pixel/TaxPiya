<?php
$dbPath = __DIR__ . '/../database/taxpiya.sqlite';
$pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = "SELECT u.id, u.name, u.email, u.telefono, r.role_name, u.estado,
        e.nombre_comercial, e.estado as empresa_estado
        FROM users u
        LEFT JOIN roles r ON r.role_id = u.user_role_id
        LEFT JOIN empresas e ON e.user_id = u.id
        WHERE u.email LIKE '%demo%' OR u.email LIKE '%taxpiya.com%'
           OR u.telefono IN ('3009001001','3009001002','3109001001','3109001002','3209002001','3219002001','3219002002','300100100')
           OR r.role_name = 'Admin'
        ORDER BY r.role_id, u.id";

foreach ($pdo->query($sql) as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}
