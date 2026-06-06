<?php
/**
 * Prueba local: disponibilidad conductores, expiración de solicitudes y calificación en estado.
 * Uso: php scripts/test-trip-matching.php
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\TripMatching;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$fail = 0;
function ok(string $msg): void { echo "OK  {$msg}\n"; }
function bad(string $msg): void { global $fail; $fail++; echo "FAIL {$msg}\n"; }

echo "=== test-trip-matching ===\n\n";

$cutoffPos = TripMatching::driverPositionCutoff();
$cutoffTrip = TripMatching::tripSearchCutoff();
ok("driverPositionCutoff={$cutoffPos}");
ok("tripSearchCutoff={$cutoffTrip}");

$expired = TripMatching::expireStaleSearchingTrips();
ok("expireStaleSearchingTrips count={$expired}");

if (!Schema::hasTable('viajes')) {
    bad('tabla viajes no existe');
    exit($fail ? 1 : 0);
}

$pasajeroId = (int) DB::table('users')->orderBy('id')->value('id');
if (!$pasajeroId) {
    bad('no hay usuarios');
    exit(1);
}

$oldId = DB::table('viajes')->insertGetId([
    'pasajero_id'       => $pasajeroId,
    'conductor_id'      => null,
    'origen_lat'        => 4.711,
    'origen_lng'        => -74.0721,
    'origen_texto'      => 'Test viejo',
    'estado'            => 'buscando',
    'metodo_asignacion' => 'manual',
    'created_at'        => now()->subMinutes(30)->format('Y-m-d H:i:s'),
    'updated_at'        => now()->subMinutes(30)->format('Y-m-d H:i:s'),
    'tarifa_aplicada'   => 10000,
    'moneda'            => 'COP',
    'pago_registrado'   => 0,
]);

TripMatching::expireStaleSearchingTrips();
$oldEstado = DB::table('viajes')->where('id', $oldId)->value('estado');
if ($oldEstado === 'cancelado_sistema') {
    ok('viaje antiguo buscando expirado');
} else {
    bad("viaje antiguo sigue en estado {$oldEstado}");
}

$openId = DB::table('viajes')->insertGetId([
    'pasajero_id'       => $pasajeroId,
    'conductor_id'      => null,
    'origen_lat'        => 4.711,
    'origen_lng'        => -74.0721,
    'origen_texto'      => 'Test abierto',
    'estado'            => 'buscando',
    'metodo_asignacion' => 'manual',
    'created_at'        => now()->format('Y-m-d H:i:s'),
    'updated_at'        => now()->format('Y-m-d H:i:s'),
    'tarifa_aplicada'   => 10000,
    'moneda'            => 'COP',
    'pago_registrado'   => 0,
]);

$cancelled = TripMatching::cancelPassengerOpenSearches($pasajeroId);
if ($cancelled >= 1) {
    ok("cancelPassengerOpenSearches={$cancelled}");
} else {
    bad('no canceló solicitudes abiertas del pasajero');
}

$openEstado = DB::table('viajes')->where('id', $openId)->value('estado');
if ($openEstado === 'cancelado_pasajero') {
    ok('solicitud abierta cancelada al crear otra');
} else {
    bad("solicitud abierta quedó en {$openEstado}");
}

$conductor = DB::table('conductores')->first();
if ($conductor && Schema::hasTable('conductor_posicion_actual')) {
    DB::table('conductores')->where('id', $conductor->id)->update(['disponible' => 1]);
    DB::table('conductor_posicion_actual')->updateOrInsert(
        ['conductor_id' => $conductor->id],
        [
            'lat' => 4.711,
            'lng' => -74.0721,
            'actualizada_at' => now()->subMinutes(20)->format('Y-m-d H:i:s'),
        ]
    );

    $freshCount = DB::table('conductores as c')
        ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
        ->where('c.id', $conductor->id)
        ->where('c.disponible', 1);
    TripMatching::applyFreshDriverPositionFilter($freshCount, 'p');
    if ($freshCount->count() === 0) {
        ok('posición antigua excluida del mapa');
    } else {
        bad('posición antigua aún cuenta como fresca');
    }

    DB::table('conductores')->where('id', $conductor->id)->update(['disponible' => 0]);
}

echo "\n" . ($fail ? "FAILED ({$fail})\n" : "ALL PASSED\n");
exit($fail ? 1 : 0);
