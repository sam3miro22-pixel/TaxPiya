<?php
/**
 * Elimina cuentas no demo (SQLite + Firebase).
 * Uso: php artisan taxpiya:purge-non-demo --force --reseed
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exit = $app->make(\Illuminate\Contracts\Console\Kernel::class)->call('taxpiya:purge-non-demo', [
    '--force'   => true,
    '--reseed'  => true,
]);

exit($exit);
