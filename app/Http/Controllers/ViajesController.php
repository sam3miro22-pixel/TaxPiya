<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ResolvesTripParticipants;
use App\Http\Requests\ViajesAddRequest;
use App\Http\Requests\ViajesEditRequest;
use App\Models\Viajes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use \PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ViajesListExport;
use App\Exports\ViajesViewExport;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Support\ActiveTripResolver;
use App\Support\ColombiaGeo;
use App\Support\DatabaseGeometry;
use App\Support\GeoDistance;
use App\Support\TripMatching;
use App\Services\TariffCalculator;
use App\Services\TripPaymentService;
use App\Services\WalletLedgerService;
use App\Services\Firebase\FirestoreTripSyncService;
use App\Services\ChatBotService;
use App\Services\TripGeoService;
class ViajesController extends Controller
{
    use ResolvesTripParticipants;

	public function solicitar(Request $req)
{
    try {
    $pasajeroId = auth()->id();
    if (!$pasajeroId) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $req->validate([
        'o_lat' => 'required|numeric',
        'o_lng' => 'required|numeric',
        'o_txt' => 'nullable|string|max:255',
        'd_lat' => 'nullable|numeric',
        'd_lng' => 'nullable|numeric',
        'd_txt' => 'nullable|string|max:255',
        'categoria' => 'nullable|in:taxi,taxi_electrico,taxi_van,taxi_plus,movilidad_reducida',
        'ciudad' => 'nullable|string|max:80',
    ]);

    $categoria = $req->input('categoria', 'taxi');
    $ciudad    = $req->input('ciudad') ?: config('taxpiya.default_city');
    $radioKm   = (float) config('taxpiya.search_radius_km', 8);

    $tarifa = TariffCalculator::findActiveTariff($categoria, $ciudad);

    if (!$tarifa) {
        return response()->json(['ok'=>false,'message'=>'No hay tarifa activa'], 404);
    }

    $oLat = (float)$req->input('o_lat');
    $oLng = (float)$req->input('o_lng');
    $dLat = $req->filled('d_lat') ? (float)$req->input('d_lat') : null;
    $dLng = $req->filled('d_lng') ? (float)$req->input('d_lng') : null;

    if (!ColombiaGeo::contains($oLat, $oLng)) {
        return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 422);
    }
    if ($dLat !== null && $dLng !== null && !ColombiaGeo::contains($dLat, $dLng)) {
        return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 422);
    }

    $porKm = (float) ($tarifa->tarifa_por_km ?? 0);
    if ($porKm > 0 && ($dLat === null || $dLng === null)) {
        return response()->json(['ok' => false, 'message' => 'Indica un destino para calcular la tarifa por distancia'], 422);
    }

    $fare = TariffCalculator::calculate($tarifa, $oLat, $oLng, $dLat, $dLng);
    $tarifaMonto = $fare['monto'];

    $ledger = app(WalletLedgerService::class);
    $ledger->ensureCuenta('pasajero', (int) $pasajeroId);
    // Viaje gratis: no se requiere saldo en la billetera para viajar
    /*
    $saldoPasajero = $ledger->getSaldoPasajero((int) $pasajeroId);
    if ($saldoPasajero < $tarifaMonto) {
        return response()->json([
            'ok'        => false,
            'message'   => 'Saldo insuficiente en billetera. Recarga para solicitar el viaje.',
            'saldo'     => $saldoPasajero,
            'requerido' => $tarifaMonto,
        ], 402);
    }
    */

    TripMatching::expireStaleSearchingTrips();
    TripMatching::cancelPassengerOpenSearches((int) $pasajeroId);

    $oPoint = DatabaseGeometry::pointRaw($oLng, $oLat);
    $dPoint = ($dLat !== null && $dLng !== null) ? DatabaseGeometry::pointRaw($dLng, $dLat) : null;

    $insert = DatabaseGeometry::stripNullGeometry([
        'pasajero_id'       => (int) $pasajeroId,
        'conductor_id'      => null,
        'vehiculo_id'       => null,
        'origen_lat'        => $oLat,
        'origen_lng'        => $oLng,
        'origen_ubicacion'  => $oPoint,
        'origen_texto'      => $req->input('o_txt'),
        'destino_lat'       => $dLat,
        'destino_lng'       => $dLng,
        'destino_ubicacion' => $dPoint,
        'destino_texto'     => $req->input('d_txt'),
        'estado'            => 'buscando',
        'created_at'        => now()->format('Y-m-d H:i:s'),
        'tarifa_id'         => (int)$tarifa->id,
        'moneda'            => $tarifa->moneda,
        'tarifa_aplicada'   => $tarifaMonto,
        'valor_pagado'      => null,
        'pago_registrado'   => 0,
        'metodo_asignacion' => 'auto',
        'radio_busqueda_m'  => (int) round($radioKm * 1000),
    ]);

    if (Schema::hasColumn('viajes', 'distancia_km_estimada')) {
        $insert['distancia_km_estimada'] = $fare['km'];
    }

    if (Schema::hasColumn('viajes', 'updated_at')) {
        $insert['updated_at'] = now()->format('Y-m-d H:i:s');
    }

    $viajeId = DB::table('viajes')->insertGetId($insert);

    DB::table('viaje_estados_log')->insert([
        'viaje_id'     => $viajeId,
        'from_estado'  => null,
        'to_estado'    => 'buscando',
        'actor_tipo'   => 'pasajero',
        'actor_id'     => (int) $pasajeroId,
        'motivo_codigo'=> 'flujo',
        'motivo_texto' => 'Solicitud de viaje creada',
        'app_origen'   => 'app_pasajero',
        'ip'           => $req->ip(),
        'created_at'   => now()->format('Y-m-d H:i:s'),
    ]);


try {
    $candidatos = [];
    if (GeoDistance::usesSqlite()) {
        [$minLat, $maxLat, $minLng, $maxLng] = GeoDistance::boundingBox($oLat, $oLng, $radioKm);
        $rowsQuery = DB::table('conductores as c')
            ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
            ->where('c.estado_operitivo', 1)
            ->where('c.disponible', 1)
            ->whereBetween('p.lat', [$minLat, $maxLat])
            ->whereBetween('p.lng', [$minLng, $maxLng]);

        TripMatching::applyFreshDriverPositionFilter($rowsQuery, 'p');

        $rows = $rowsQuery
            ->select('c.user_id', 'p.lat', 'p.lng')
            ->limit(60)
            ->get();

        foreach ($rows as $row) {
            $dist = GeoDistance::km($oLat, $oLng, (float) $row->lat, (float) $row->lng);
            if ($dist <= $radioKm) {
                $candidatos[] = (int) $row->user_id;
            }
        }
        $candidatos = array_slice(array_values(array_unique($candidatos)), 0, 30);
    } else {
        $candidatosQuery = DB::table('conductores as c')
            ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
            ->where('c.estado_operitivo', 1)
            ->where('c.disponible', 1);

        TripMatching::applyFreshDriverPositionFilter($candidatosQuery, 'p');

        $candidatos = $candidatosQuery
            ->select('c.user_id')
            ->selectRaw(
                '(6371 * acos( cos(radians(?)) * cos(radians(p.lat)) * cos(radians(p.lng) - radians(?)) + sin(radians(?)) * sin(radians(p.lat)) ) ) as dist_km',
                [$oLat, $oLng, $oLat]
            )
            ->having('dist_km', '<=', $radioKm)
            ->orderBy('dist_km')
            ->limit(30)
            ->pluck('c.user_id')
            ->all();
    }

    if ($candidatos !== []) {
        app(\App\Services\PushService::class)->notifyUsers(
            $candidatos,
            'Nueva solicitud de Viaje',
            $req->input('o_txt', 'Solicitud cerca de ti'),
            [
                't'        => 'offer',
                'viaje_id' => (string) $viajeId,
                'o_lat'    => (string) $oLat,
                'o_lng'    => (string) $oLng,
            ]
        );
    }
} catch (\Throwable $e) {
    \Log::warning('FCM nueva solicitud: fallo', [
        'viaje_id' => $viajeId,
        'err'      => $e->getMessage(),
    ]);
}

    app(FirestoreTripSyncService::class)->syncTrip((int) $viajeId);

    return response()->json([
        'ok'         => true,
        'viaje_id'   => (int)$viajeId,
        'tarifa_id'  => (int)$tarifa->id,
        'monto'      => (float)$tarifa->monto_fijo,
        'moneda'     => $tarifa->moneda,
        'estado'     => 'buscando',
    ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Throwable $e) {
        report($e);
        return response()->json(['ok' => false, 'message' => 'No se pudo crear la solicitud. Intenta de nuevo.'], 500);
    }
}


public function activo(Request $req)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $viaje = null;
    if ($user->hasRole('pasajero')) {
        $viaje = ActiveTripResolver::forPassenger((int) $user->id);
    } elseif ($user->hasRole('conductor')) {
        $viaje = ActiveTripResolver::forConductor((int) $user->id);
    }

    return response()->json([
        'ok'   => true,
        'trip' => ActiveTripResolver::bootstrapPayload($viaje),
    ]);
}


public function estado($id)
{
    $viaje = DB::table('viajes as v')
        ->leftJoin('conductores as c', 'c.id', '=', 'v.conductor_id')
        ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
        ->leftJoin('vehiculos as vh', 'vh.id', '=', 'v.vehiculo_id')
        ->leftJoin('vehiculos as vh2', function($j){
            $j->on('vh2.conductor_id', '=', 'v.conductor_id')->whereNull('v.vehiculo_id');
        })
        ->leftJoin('conductor_posicion_actual as cpa', 'cpa.conductor_id', '=', 'v.conductor_id')
        ->where('v.id', $id)
        ->selectRaw("
    v.id as viaje_id, v.estado, v.conductor_id,
    COALESCE(vh.marca, vh2.marca)  as veh_marca,
    COALESCE(vh.linea, vh2.linea)  as veh_linea,
    COALESCE(vh.placa, vh2.placa)  as veh_placa,
    u.name as conductor_nombre,
    u.telefono as conductor_tel,
    cpa.lat as driver_lat, cpa.lng as driver_lng,
    v.tarifa_aplicada, v.moneda
")

        ->first();

    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

    $rawViaje = DB::table('viajes')->where('id', $id)->first();
    if ($rawViaje && !$this->userCanAccessTrip($rawViaje, auth()->id())) {
        return response()->json(['ok' => false, 'message' => 'No autorizado'], 403);
    }

    $calificacion = null;
    if (Schema::hasTable('calificaciones')) {
        $cal = DB::table('calificaciones')
            ->where('viaje_id', (int) $id)
            ->where('rater_rol', 'pasajero')
            ->orderByDesc('id')
            ->first();

        if ($cal) {
            $calificacion = [
                'puntuacion' => (int) $cal->puntuacion,
                'comentario' => $cal->comentario,
                'created_at' => $cal->created_at,
            ];
        }
    }

    $uid = auth()->id();
    [$isPasajero, $isConductor] = $this->resolveTripRoles($rawViaje, $uid);

    return response()->json([
        'ok' => true,
        'viaje_id' => (int)$viaje->viaje_id,
        'estado' => $viaje->estado,
        'conductor_id' => $viaje->conductor_id ? (int)$viaje->conductor_id : null,
        'conductor' => $viaje->conductor_id ? [
            'nombre' => $viaje->conductor_nombre,
			'telefono' => $viaje->conductor_tel,
        ] : null,
        'vehiculo' => $viaje->conductor_id ? [
            'marca' => $viaje->veh_marca,
            'linea' => $viaje->veh_linea,
            'placa' => $viaje->veh_placa,
        ] : null,
        'driver_pos' => ($viaje->driver_lat && $viaje->driver_lng) ? [
            'lat' => (float)$viaje->driver_lat,
            'lng' => (float)$viaje->driver_lng,
        ] : null,

        'monto'  => $viaje->tarifa_aplicada !== null ? (float)$viaje->tarifa_aplicada : null,
        'moneda' => $viaje->moneda,
        'calificacion' => $calificacion,
    ]);
}

public function cancelar(Request $req)
{
    $req->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
        'motivo'   => 'nullable|string|max:255',
    ]);

    $viajeId = (int) $req->input('viaje_id');
    $motivo  = trim((string) $req->input('motivo'));
    $userId  = auth()->id();

    if (!$userId) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $result = [
        'ok'      => false,
        'estado'  => null,
        'message' => null,
    ];

    DB::transaction(function () use (&$result, $viajeId, $motivo, $userId, $req) {
        $viaje = DB::table('viajes')
            ->where('id', $viajeId)
            ->lockForUpdate()
            ->first();

        if (!$viaje) {
            $result['ok']      = false;
            $result['message'] = 'Viaje no encontrado';
            return;
        }

        
        if (in_array($viaje->estado, ['terminado', 'cancelado_pasajero', 'cancelado_conductor', 'no_show'], true)) {
            $result['ok']     = true;
            $result['estado'] = $viaje->estado;
            return;
        }

        [$isPasajero, $isConductor] = $this->resolveTripRoles($viaje, $userId);
        if (!$isPasajero && !$isConductor) {
            $result['message'] = 'No autorizado';
            return;
        }

        if ($isConductor && in_array($viaje->estado, ['asignado', 'en_camino'], true)) {
            $result['message'] = 'No puedes cancelar hasta llegar al pasajero (GPS o código de llegada).';
            return;
        }

        $now = now();
        $cancelEstado = $isConductor ? 'cancelado_conductor' : 'cancelado_pasajero';
        $cancelPor    = $isConductor ? 'conductor' : 'pasajero';

        DB::table('viajes')
            ->where('id', $viajeId)
            ->update([
                'estado'             => $cancelEstado,
                'cancelado_at'       => $now,
                'cancelado_por'      => $cancelPor,
                'cancelacion_motivo' => $motivo ?: null,
                'updated_at'         => $now,
            ]);

        
        if (!empty($viaje->conductor_id)) {
            DB::table('conductores')
                ->where('id', (int) $viaje->conductor_id)
                ->update([
                    'disponible'     => 1,
                    'last_online_at' => $now,
                ]);
        }

       
        if (\Illuminate\Support\Facades\Schema::hasTable('viaje_estados_log')) {
            DB::table('viaje_estados_log')->insert([
                'viaje_id'      => $viajeId,
                'from_estado'   => $viaje->estado,
                'to_estado'     => $cancelEstado,
                'actor_tipo'    => $cancelPor,
                'actor_id'      => $userId,
                'motivo_codigo' => $cancelEstado,
                'motivo_texto'  => $motivo ?: ('Cancelado por ' . $cancelPor),
                'app_origen'    => 'app_pasajero',
                'ip'            => $req->ip(),
                'created_at'    => $now,
            ]);
        }

        $result['ok']     = true;
        $result['estado'] = $cancelEstado;
    });

    if (!$result['ok'] && $result['message'] === 'No autorizado') {
        return response()->json(['ok' => false, 'message' => 'No autorizado'], 403);
    }

    if (!$result['ok'] && $result['message'] === 'Viaje no encontrado') {
        return response()->json([
            'ok'      => false,
            'message' => 'Viaje no encontrado',
        ], 404);
    }

    if (!$result['ok']) {
        
        return response()->json([
            'ok'      => false,
            'message' => $result['message'] ?? 'No se pudo cancelar el viaje',
        ], 400);
    }

    return response()->json([
        'ok'     => true,
        'estado' => $result['estado'],
    ]);
}


public function pasajeroAbordo(Request $req)
{
    $req->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
    ]);

    $userId = auth()->id();
    $viaje = DB::table('viajes')->where('id', $req->integer('viaje_id'))->first();

    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

    
    if ($userId && isset($viaje->pasajero_id) && (int)$viaje->pasajero_id !== (int)$userId) {
        return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
    }

    
    if (in_array($viaje->estado, [
        'terminado','cancelado_pasajero','cancelado_conductor','cancelado_sistema','no_show','fallo_localizacion'
    ], true)) {
        return response()->json(['ok'=>false,'message'=>'Viaje no activo'], 409);
    }

    
    if ($viaje->estado === 'iniciado') {
        return response()->json(['ok'=>true, 'estado'=>'iniciado']);
    }

   
    if (!in_array($viaje->estado, ['llego','asignado','en_camino'], true)) {
        return response()->json(['ok'=>false,'message'=>'Estado no válido para iniciar'], 422);
    }

    try {
        DB::transaction(function() use ($req, $viaje, $userId) {
            
            DB::table('viajes')->where('id', $viaje->id)->update([
                'estado'     => 'iniciado',
                'updated_at' => now(),
            ]);

            
            if (Schema::hasTable('viaje_estados_log')) {
                DB::table('viaje_estados_log')->insert([
                    'viaje_id'      => $viaje->id,
                    'from_estado'   => $viaje->estado,
                    'to_estado'     => 'iniciado',
                    'actor_tipo'    => 'pasajero',
                    'actor_id'      => $userId ?? ($viaje->pasajero_id ?? null),
                    'motivo_codigo' => 'inicio',
                    'motivo_texto'  => 'Pasajero confirmó que abordó',
                    'app_origen'    => 'app_pasajero',
                    'ip'            => $req->ip(),
                    'created_at'    => now(),
                ]);
            }
        });

        return response()->json(['ok'=>true, 'estado'=>'iniciado']);
    } catch (\Throwable $e) {
        \Log::error('pasajeroAbordo error', [
            'viaje_id' => $viaje->id ?? null,
            'err'      => $e->getMessage(),
        ]);
       
        return response()->json(['ok'=>false,'message'=>'No se pudo marcar abordo'], 500);
    }
}


public function calificar(Request $req)
{
    $req->validate([
        'viaje_id'   => 'required|integer|exists:viajes,id',
        'puntuacion' => 'required|integer|min:1|max:5',
        'comentario' => 'nullable|string|max:500',
    ]);

    $viaje = DB::table('viajes')->where('id', $req->integer('viaje_id'))->first();
    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

   
    $uid = auth()->id();
    if ($uid && (int)$viaje->pasajero_id !== (int)$uid) {
        return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
    }

   
    if ($viaje->estado !== 'terminado') {
        return response()->json(['ok'=>false,'message'=>'El viaje no está terminado'], 409);
    }

    
    $ya = DB::table('calificaciones')
        ->where('viaje_id', $viaje->id)
        ->where('rater_rol', 'pasajero')
        ->exists();
    if ($ya) {
        return response()->json(['ok'=>false,'message'=>'Este viaje ya fue calificado por el pasajero'], 409);
    }

    
    $rateeUserId = null;
    if (!empty($viaje->conductor_id)) {
        $rateeUserId = DB::table('conductores')
            ->where('id', $viaje->conductor_id)
            ->value('user_id');
    }

    if (!$rateeUserId) {
        
        return response()->json([
            'ok' => false,
            'message' => 'El conductor no tiene un usuario asociado. No se puede registrar la calificación.'
        ], 409);
    }

    DB::table('calificaciones')->insert([
        'viaje_id'         => (int)$viaje->id,
        'rater_id'         => $uid ?: (int)$viaje->pasajero_id, 
        'rater_rol'        => 'pasajero',
        'ratee_id'         => (int)$rateeUserId,                
        'ratee_rol'        => 'conductor',
        'puntuacion'       => (int)$req->input('puntuacion'),
        'comentario'       => $req->input('comentario'),
        'etiquetas_json'   => null,  
        'visible'          => 1,
        'moderado'         => 0,
        'moderado_motivo'  => null,
        'ip'               => $req->ip(),
        'created_at'       => now(),
    ]);

    if (!empty($viaje->conductor_id) && Schema::hasColumn('conductores', 'rating_promedio')) {
        $avg = DB::table('calificaciones')
            ->where('ratee_id', (int) $rateeUserId)
            ->where('ratee_rol', 'conductor')
            ->where('visible', 1)
            ->avg('puntuacion');

        if ($avg !== null) {
            DB::table('conductores')
                ->where('id', (int) $viaje->conductor_id)
                ->update(['rating_promedio' => round((float) $avg, 2)]);
        }
    }

    return response()->json(['ok'=>true]);
}


public function chatList(Request $req, $id)
{
    $viaje = DB::table('viajes')->where('id', $id)->first();
    if (!$viaje) {
        return response()->json(['ok' => false, 'message' => 'Viaje no encontrado'], 404);
    }

    
    $uid = auth()->id();
    [$isPasajero, $isConductor] = $this->resolveTripRoles($viaje, $uid);
    if (!$isPasajero && !$isConductor) {
        return response()->json(['ok' => false, 'message' => 'No autorizado'], 403);
    }

    $q = DB::table('chat_mensajes')->where('viaje_id', $id);
    if ($req->filled('since_id')) {
        $q->where('id', '>', (int)$req->query('since_id'));
    }
    $items = $q->orderBy('id', 'asc')->limit(200)->get();

  
    $shouldMarkRead = filter_var($req->query('mark_read', '1'), FILTER_VALIDATE_BOOLEAN);
    if ($shouldMarkRead && $items->count()) {
        if ($isConductor) {
           
            $ids = $items->where('remitente_rol', 'pasajero')
                         ->whereNull('leido_por_conductor_at')
                         ->pluck('id')->all();
            if (!empty($ids)) {
                DB::table('chat_mensajes')
                    ->whereIn('id', $ids)
                    ->update(['leido_por_conductor_at' => now()]);
            }
        } else {
            
            $ids = $items->where('remitente_rol', 'conductor')
                         ->whereNull('leido_por_pasajero_at')
                         ->pluck('id')->all();
            if (!empty($ids)) {
                DB::table('chat_mensajes')
                    ->whereIn('id', $ids)
                    ->update(['leido_por_pasajero_at' => now()]);
            }
        }
    }

   
    $payload = $items->map(function ($m) {
        return [
            'id'             => (int) $m->id,
            'remitente_rol'  => $m->remitente_rol,  
            'rol'            => $m->remitente_rol,   
            'tipo'           => $m->tipo,
            'mensaje'        => $m->mensaje,
            'media_url'      => $m->media_url,
            'lat'            => isset($m->lat) ? (float) $m->lat : null,
            'lng'            => isset($m->lng) ? (float) $m->lng : null,
            'created_at'     => $m->created_at,
        ];
    })->values();

   
    return response()->json([
        'ok'       => true,
        'items'    => $payload,
        'messages' => $payload,
    ]);
}


public function chatSend(Request $req)
{
    $req->validate([
        'viaje_id'  => 'required|integer|exists:viajes,id',
        'mensaje'   => 'required_without:media_url|string|max:2000',
        'media_url' => 'nullable|string|max:255',
        'tipo'      => 'nullable|in:text,quick,image,file,location,system'
    ]);

   $viaje = DB::table('viajes')->where('id', $req->viaje_id)->first();
if (!$viaje) return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);

$uid = auth()->id();
[$isPasajero, $isConductor] = $this->resolveTripRoles($viaje, $uid);
if (!$isPasajero && !$isConductor){
    return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
}

$role     = $isConductor ? 'conductor' : 'pasajero';
$senderId = $isConductor
    ? (int) DB::table('conductores')->where('user_id', $uid)->value('id')
    : (int) $viaje->pasajero_id;

$id = DB::table('chat_mensajes')->insertGetId([
    'viaje_id'      => (int)$req->viaje_id,
    'remitente_id'  => $senderId,
    'remitente_rol' => $role,
    'tipo'          => $req->input('tipo', 'text'),
    'mensaje'       => $req->input('mensaje'),
    'media_url'     => $req->input('media_url'),
    'ip'            => $req->ip(),
    'created_at'    => now(),
]);

if ($isPasajero && !empty($viaje->conductor_id)) {
    $driverUserId = DB::table('conductores')
        ->where('id', $viaje->conductor_id)
        ->value('user_id');

    if ($driverUserId) {
        $prev = (string) ($req->input('mensaje') ?? '');
        if (mb_strlen($prev) > 80) $prev = mb_substr($prev, 0, 77) . '…';

        app(\App\Services\PushService::class)->notifyUsers(
            [$driverUserId],
            'Nuevo mensaje del pasajero',
            $prev !== '' ? $prev : 'Tienes un mensaje',
            [
                't'        => 'chat',
                'viaje_id' => (string) $viaje->id,
            ]
        );
    }
}

try {
    app(ChatBotService::class)->maybeReply((int) $req->viaje_id, $role, (string) $req->input('mensaje', ''));
} catch (\Throwable $e) {
    report($e);
}

return response()->json(['ok'=>true, 'id'=>(int)$id]);

}

public function chatMarkRead(Request $req)
{
    $req->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
        'max_id'   => 'required|integer|min:1',
    ]);

    $viaje = DB::table('viajes')->where('id', $req->viaje_id)->first();
if (!$viaje) return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);

$uid = auth()->id();
[$isPasajero, $isConductor] = $this->resolveTripRoles($viaje, $uid);
if (!$isPasajero && !$isConductor){
    return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
}

$maxId = (int)$req->input('max_id');
if ($isConductor){
 
    DB::table('chat_mensajes')
      ->where('viaje_id', $req->viaje_id)
      ->where('remitente_rol','pasajero')
      ->where('id','<=',$maxId)
      ->update(['leido_por_conductor_at' => now()]);
}else{
    
    DB::table('chat_mensajes')
      ->where('viaje_id', $req->viaje_id)
      ->where('remitente_rol','conductor')
      ->where('id','<=',$maxId)
      ->update(['leido_por_pasajero_at' => now()]);
}

return response()->json(['ok'=>true]);

}

public function chatListDriver(Request $req, $id)
{
    $viaje = DB::table('viajes')->where('id', $id)->first();
    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

    $uid = auth()->id();
    $esConductor = false;

    
    if (isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$uid) {
        $esConductor = true;
    }
    
    elseif (Schema::hasTable('conductores')) {
        $drv = DB::table('conductores')->where('user_id', $uid)->first();
        if ($drv && isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$drv->id) {
            $esConductor = true;
        }
    }

    if (!$esConductor) {
        return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
    }

    $q = DB::table('chat_mensajes')->where('viaje_id', $id);
    if ($req->filled('since_id')) {
        $q->where('id', '>', (int)$req->query('since_id'));
    }

    $items = $q->orderBy('id', 'asc')->limit(100)->get();

    
    if ($items->count()) {
        $idsDelPasajero = $items->where('remitente_rol', 'pasajero')
                                ->whereNull('leido_por_conductor_at')
                                ->pluck('id')->all();
        if ($idsDelPasajero) {
            DB::table('chat_mensajes')->whereIn('id', $idsDelPasajero)
                ->update(['leido_por_conductor_at' => now()]);
        }
    }

    return response()->json([
        'ok'    => true,
        'items' => $items->map(function($m){
            return [
                'id'         => (int)$m->id,
                'rol'        => $m->remitente_rol,        
                'role'       => $m->remitente_rol,        
                'from'       => $m->remitente_rol,       
                'tipo'       => $m->tipo,
                'mensaje'    => $m->mensaje,
                'text'       => $m->mensaje,             
                'body'       => $m->mensaje,              
                'media_url'  => $m->media_url,
                'lat'        => $m->lat ? (float)$m->lat : null,
                'lng'        => $m->lng ? (float)$m->lng : null,
                'created_at' => $m->created_at,
            ];
        })->values(),
    ]);
}

public function chatSendDriver(Request $req)
{
    $req->validate([
        'viaje_id'  => 'required|integer|exists:viajes,id',
        'mensaje'   => 'required_without:media_url|string|max:2000',
        'media_url' => 'nullable|string|max:255',
        'tipo'      => 'nullable|in:text,quick,image,file,location,system'
    ]);

    $viaje = DB::table('viajes')->where('id', $req->viaje_id)->first();
    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

    $uid = auth()->id();
    $esConductor = false;

   
    if (isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$uid) {
        $esConductor = true;
    }
    
    elseif (Schema::hasTable('conductores')) {
        $drv = DB::table('conductores')->where('user_id', $uid)->first();
        if ($drv && isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$drv->id) {
            $esConductor = true;
        }
    }

    if (!$esConductor) {
        return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
    }

    $id = DB::table('chat_mensajes')->insertGetId([
        'viaje_id'      => (int)$req->viaje_id,
        'remitente_id'  => (int)$uid,                         
        'remitente_rol' => 'conductor',
        'tipo'          => $req->input('tipo', 'text'),
        'mensaje'       => $req->input('mensaje'),
        'media_url'     => $req->input('media_url'),
        'ip'            => $req->ip(),
        'created_at'    => now(),
    ]);

 
    try {
        if (!empty($viaje->pasajero_id)) {
            $prev = (string) ($req->input('mensaje') ?? '');
            $prev = trim($prev);
            if ($prev === '') { $prev = 'Tienes un mensaje'; }
            if (mb_strlen($prev) > 80) { $prev = mb_substr($prev, 0, 77) . '…'; }

            app(\App\Services\PushService::class)->notifyUsers(
                [(int) $viaje->pasajero_id],
                'Nuevo mensaje del conductor',
                $prev,
                [
                    't'        => 'chat',               
                    'viaje_id' => (string) $viaje->id,
                ]
            );
        }
    } catch (\Throwable $e) {
        \Log::warning('FCM chat driver->pasajero falló', [
            'viaje_id' => $viaje->id ?? null,
            'err'      => $e->getMessage(),
        ]);
    }
  

    return response()->json(['ok'=>true, 'id'=>(int)$id]);
}


public function chatMarkReadDriver(Request $req)
{
    $req->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
        'max_id'   => 'required|integer|min:1',
    ]);

    $viaje = DB::table('viajes')->where('id', $req->viaje_id)->first();
    if (!$viaje) {
        return response()->json(['ok'=>false,'message'=>'Viaje no encontrado'], 404);
    }

    $uid = auth()->id();
    $esConductor = false;

    if (isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$uid) {
        $esConductor = true;
    } elseif (Schema::hasTable('conductores')) {
        $drv = DB::table('conductores')->where('user_id', $uid)->first();
        if ($drv && isset($viaje->conductor_id) && (int)$viaje->conductor_id === (int)$drv->id) {
            $esConductor = true;
        }
    }

    if (!$esConductor) {
        return response()->json(['ok'=>false,'message'=>'No autorizado'], 403);
    }

    DB::table('chat_mensajes')
        ->where('viaje_id', $req->viaje_id)
        ->where('remitente_rol', 'pasajero')
        ->where('id', '<=', $req->max_id)
        ->update(['leido_por_conductor_at' => now()]);

    return response()->json(['ok'=>true]);
}


	
	function index(Request $request, $fieldname = null , $fieldvalue = null){
		$view = "pages.viajes.list";

		$query = Viajes::query();
		$limit = $request->limit ?? 100;
		if($request->search){
			$search = trim($request->search);
			Viajes::search($query, $search); 
		}
		$orderby = $request->orderby ?? "viajes.id";
		$ordertype = $request->ordertype ?? "desc";
		$query->orderBy($orderby, $ordertype);
		if($fieldname){
			$query->where($fieldname , $fieldvalue); 
		}
		
		if($this->getExportFormat()){
			return $this->ExportList($query, $request);
		}

		$records = $query->paginate($limit, Viajes::listFields());
		return $this->renderView($view, compact("records"));
	}
	

	
	function importdata(Request $request){
		$importSettings = config("upload.import");
		$maxFileSize = intval($importSettings["max_file_size"]) * 1000; 
		$validator = Validator::make($request->all(), 
			[
				"file" => "file|required|max:$maxFileSize|mimes:csv,txt",
			]
		);
		if ($validator->fails()) {
			return back()->withErrors($validator->errors());
		}
		$csvOptions = array(
			'fields' => '', //leave empty to use the first row as the columns
			'delimiter' => ',', 
			'quote' => '"'
		);
		$filePath = $request->file('file')->getRealPath();
		$modeldata = parse_csv_file($filePath, $csvOptions);
		Viajes::insert($modeldata);
		return $this->redirect(url()->previous(), "Datos importados con éxito");
	}
	

	function view(Request $request, $rec_id = null){
		$query = Viajes::query();
		// if request format is for export example:- product/view/344?export=pdf
		if($this->getExportFormat()){
			return $this->ExportView($query, $rec_id, $request);
		}

		$record = $query->findOrFail($rec_id, Viajes::viewFields());
		return $this->renderView("pages.viajes.view", ["data" => $record]);
	}
	

	
	function masterDetail($rec_id = null){
		return View("pages.viajes.detail-pages", ["masterRecordId" => $rec_id]);
	}
	

	function add(){
		return $this->renderView("pages.viajes.add");
	}
	

	
	function store(ViajesAddRequest $request){
		$modeldata = $this->normalizeFormData($request->validated());
		
		//save Viajes record
		$record = Viajes::create($modeldata);
		$rec_id = $record->id;
		return $this->redirect("viajes", "Grabar agregado exitosamente");
	}
	

	
	function edit(ViajesEditRequest $request, $rec_id = null){
		$query = Viajes::query();
		$record = $query->findOrFail($rec_id, Viajes::editFields());
		if ($request->isMethod('post')) {
			$modeldata = $this->normalizeFormData($request->validated());
			$record->update($modeldata);
			return $this->redirect("viajes", "Registro actualizado con éxito");
		}
		return $this->renderView("pages.viajes.edit", ["data" => $record, "rec_id" => $rec_id]);
	}
	

	
	function delete(Request $request, $rec_id = null){
		$arr_id = explode(",", $rec_id);
		$query = Viajes::query();
		$query->whereIn("id", $arr_id);
		$query->delete();
		$redirectUrl = $request->redirect ?? url()->previous();
		return $this->redirect($redirectUrl, "Grabar eliminado con éxito");
	}
	

	
	private function ExportList($query, $request){
		ob_end_clean(); // clean any output to allow file download
		$filename = "ListViajesReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$records = $query->get(Viajes::exportListFields());
			return view("reports.viajes-list", ["records" => $records]);
		}
		elseif($format == "pdf"){
			$records = $query->get(Viajes::exportListFields());
			$pdf = PDF::loadView("reports.viajes-list", ["records" => $records]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new ViajesListExport($query), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new ViajesListExport($query), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
	

	private function ExportView($query, $rec_id, $request){
		ob_end_clean();// clean any output to allow file download
		$filename ="ViewViajesReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$record = $query->findOrFail($rec_id, Viajes::exportViewFields());
			return view("reports.viajes-view", ["record" => $record]);
		}
		elseif($format == "pdf"){
			$record = $query->findOrFail($rec_id, Viajes::exportViewFields());
			$pdf = PDF::loadView("reports.viajes-view", ["record" => $record]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new ViajesViewExport($query, $rec_id), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new ViajesViewExport($query, $rec_id), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
}
