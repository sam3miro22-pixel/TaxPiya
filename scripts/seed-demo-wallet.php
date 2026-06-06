<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\WalletService;

$ws = app(WalletService::class);
$userId = DB::table('users')->where('telefono', '3109001001')->value('id');
$cid = DB::table('conductores')->where('user_id', $userId)->value('id');

if (!$cid) {
    echo "No conductor demo found\n";
    exit(1);
}

$ws->ensureSaldoRow((int) $cid);
$saldo = $ws->getSaldo((int) $cid);
if ((float) ($saldo->saldo_actual ?? 0) <= 0) {
    $ws->recargaInicial((int) $cid, (float) config('taxpiya.wallet.demo_initial_balance', 100000));
}
$saldo = $ws->getSaldo((int) $cid);
echo "OK conductor={$cid} saldo={$saldo->saldo_actual}\n";
