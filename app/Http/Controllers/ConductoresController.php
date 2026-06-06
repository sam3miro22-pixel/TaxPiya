<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConductoresAddRequest;
use App\Http\Requests\ConductoresEditRequest;
use App\Models\Conductores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use \PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConductoresListExport;
use App\Exports\ConductoresViewExport;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Support\DatabaseGeometry;
use App\Support\GeoDistance;
use App\Services\WalletService;
class ConductoresController extends Controller
{
	
public function disponible(Request $req)
    {
        try {
            $userId = Auth::id();
            $conductor = DB::table('conductores')->where('user_id', $userId)->first();
            if (!$conductor) {
                return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
            }

            $on = filter_var($req->input('disponible'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($on === null) {
                return response()->json(['ok' => false, 'message' => 'Parámetro disponible inválido'], 422);
            }

            if ($on) {
                try {
                    $walletCheck = app(WalletService::class)->canOperate((int) $conductor->id);
                    if (!$walletCheck['ok']) {
                        return response()->json(['ok' => false, 'message' => $walletCheck['message']], 402);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $update = ['disponible' => $on ? 1 : 0];
            if (Schema::hasColumn('conductores', 'updated_at')) {
                $update['updated_at'] = now()->format('Y-m-d H:i:s');
            }
            DB::table('conductores')->where('id', $conductor->id)->update($update);

            return response()->json(['ok' => true, 'disponible' => $on ? 1 : 0]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'message' => 'No se pudo cambiar tu estado. Intenta de nuevo.'], 500);
        }
    }

  public function posicion(Request $req)
  {
    try {
        $userId = Auth::id();
        $conductor = DB::table('conductores')->where('user_id', $userId)->select('id')->first();
        if (!$conductor) {
            return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
        }

        $req->validate([
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'heading'        => 'nullable|numeric',
            'velocidad_kmh'  => 'nullable|numeric',
        ]);

        $lat = (float) $req->input('lat');
        $lng = (float) $req->input('lng');
        $now = now()->format('Y-m-d H:i:s');

        $data = ['lat' => $lat, 'lng' => $lng];

        if ($req->filled('heading') && Schema::hasColumn('conductor_posicion_actual', 'heading')) {
            $data['heading'] = (float) $req->input('heading');
        }
        if ($req->filled('velocidad_kmh') && Schema::hasColumn('conductor_posicion_actual', 'velocidad_kmh')) {
            $data['velocidad_kmh'] = (float) $req->input('velocidad_kmh');
        }
        if (Schema::hasColumn('conductor_posicion_actual', 'actualizada_at')) {
            $data['actualizada_at'] = $now;
        }

        $exists = DB::table('conductor_posicion_actual')
            ->where('conductor_id', $conductor->id)
            ->exists();

        if ($exists) {
            DB::table('conductor_posicion_actual')
                ->where('conductor_id', $conductor->id)
                ->update($data);
        } else {
            $insert = $data + ['conductor_id' => $conductor->id];
            if (Schema::hasColumn('conductor_posicion_actual', 'created_at')) {
                $insert['created_at'] = $now;
            }
            DB::table('conductor_posicion_actual')->insert($insert);
        }

        if (Schema::hasTable('usuario_dispositivos')) {
            try {
                $deviceUpdate = [];
                if (Schema::hasColumn('usuario_dispositivos', 'last_seen_at')) {
                    $deviceUpdate['last_seen_at'] = $now;
                }
                if (Schema::hasColumn('usuario_dispositivos', 'last_ip')) {
                    $deviceUpdate['last_ip'] = $req->ip();
                }
                if ($deviceUpdate !== []) {
                    DB::table('usuario_dispositivos')
                        ->where('user_id', $userId)
                        ->update($deviceUpdate);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $viajeActivo = DB::table('viajes')
            ->where('conductor_id', $conductor->id)
            ->where('estado', 'asignado')
            ->first();

        if ($viajeActivo) {
            $tripUpdate = ['estado' => 'en_camino'];
            if (Schema::hasColumn('viajes', 'en_camino_at')) {
                $tripUpdate['en_camino_at'] = $now;
            }
            if (Schema::hasColumn('viajes', 'updated_at')) {
                $tripUpdate['updated_at'] = $now;
            }
            DB::table('viajes')->where('id', $viajeActivo->id)->update($tripUpdate);
        }

        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        report($e);
        return response()->json(['ok' => false, 'message' => 'No se pudo guardar tu ubicación.'], 500);
    }
  }

	


public function solicitudPendiente(Request $request)
{
    try {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        $conductor = DB::table('conductores')->where('user_id', $userId)->first();
        if (!$conductor) {
            return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
        }

        $pos = DB::table('conductor_posicion_actual')
            ->where('conductor_id', $conductor->id)
            ->first();

        if (!$pos || !$pos->lat || !$pos->lng) {
            return response()->json(['ok' => false, 'message' => 'Activa tu ubicación para recibir solicitudes'], 200);
        }

        $driverLat = (float) $pos->lat;
        $driverLng = (float) $pos->lng;
        $radiusKm = (float) config('taxpiya.search_radius_km', 8);

        $query = DB::table('viajes as v')
            ->where('v.estado', 'buscando')
            ->whereNull('v.conductor_id');

        if (Schema::hasTable('viaje_estados_log')) {
            $query->whereNotExists(function ($sub) use ($userId) {
                $sub->from('viaje_estados_log as l')
                    ->whereColumn('l.viaje_id', 'v.id')
                    ->where('l.actor_tipo', 'conductor')
                    ->where('l.actor_id', $userId)
                    ->where('l.from_estado', 'buscando')
                    ->where('l.to_estado', 'buscando');
            });
        }

        $columns = [
            'v.id',
            'v.origen_lat', 'v.origen_lng', 'v.origen_texto',
            'v.destino_lat', 'v.destino_lng', 'v.destino_texto',
            'v.tarifa_aplicada', 'v.moneda', 'v.created_at',
        ];

        if (GeoDistance::usesSqlite()) {
            [$minLat, $maxLat, $minLng, $maxLng] = GeoDistance::boundingBox($driverLat, $driverLng, $radiusKm);
            $candidates = $query
                ->whereBetween('v.origen_lat', [$minLat, $maxLat])
                ->whereBetween('v.origen_lng', [$minLng, $maxLng])
                ->select($columns)
                ->orderByDesc('v.id')
                ->limit(40)
                ->get();

            $viaje = null;
            $bestDist = PHP_FLOAT_MAX;
            foreach ($candidates as $candidate) {
                $dist = GeoDistance::km(
                    $driverLat,
                    $driverLng,
                    (float) $candidate->origen_lat,
                    (float) $candidate->origen_lng
                );
                if ($dist <= $radiusKm && $dist < $bestDist) {
                    $bestDist = $dist;
                    $viaje = $candidate;
                }
            }
        } else {
            $viaje = $query
                ->select($columns)
                ->selectRaw(
                    '(6371 * acos( cos(radians(?)) * cos(radians(v.origen_lat)) * cos(radians(v.origen_lng) - radians(?)) + sin(radians(?)) * sin(radians(v.origen_lat)) ) ) as dist_km',
                    [$driverLat, $driverLng, $driverLat]
                )
                ->having('dist_km', '<=', $radiusKm)
                ->orderBy('dist_km')
                ->orderByDesc('v.id')
                ->first();
        }

        if (!$viaje) {
            return response()->json(['ok' => false, 'message' => 'Sin solicitudes'], 200);
        }

        return response()->json([
            'ok' => true,
            'viaje' => [
                'id' => (int) $viaje->id,
                'o'  => [
                    'lat' => (float) $viaje->origen_lat,
                    'lng' => (float) $viaje->origen_lng,
                    'txt' => $viaje->origen_texto,
                ],
                'd'  => [
                    'lat' => is_null($viaje->destino_lat) ? null : (float) $viaje->destino_lat,
                    'lng' => is_null($viaje->destino_lng) ? null : (float) $viaje->destino_lng,
                    'txt' => $viaje->destino_texto,
                ],
                'monto' => is_null($viaje->tarifa_aplicada) ? null : (float) $viaje->tarifa_aplicada,
                'mon'   => $viaje->moneda,
                'ts'    => $viaje->created_at,
            ],
        ]);
    } catch (\Throwable $e) {
        report($e);
        return response()->json(['ok' => false, 'message' => 'Sin solicitudes'], 200);
    }
}



public function aceptarViaje(Request $request)
{
    $request->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
    ]);

    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $conductor = DB::table('conductores')->where('user_id', $userId)->first();
    if (!$conductor) {
        return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 403);
    }

    $walletCheck = app(WalletService::class)->canOperate((int) $conductor->id);
    if (!$walletCheck['ok']) {
        return response()->json(['ok' => false, 'message' => $walletCheck['message']], 402);
    }

    $viajeId    = (int) $request->viaje_id;
    $vehiculoId = DB::table('vehiculos')->where('conductor_id', $conductor->id)->value('id');

    $updated     = 0;
    $viajeResult = null;

    DB::transaction(function () use (&$updated, &$viajeResult, $viajeId, $conductor, $vehiculoId, $userId, $request) {
        $viaje = DB::table('viajes')
            ->where('id', $viajeId)
            ->lockForUpdate()
            ->first();

        if (!$viaje) {
            return;
        }

        if ($viaje->estado !== 'buscando' || !is_null($viaje->conductor_id)) {
            return;
        }

        $now = now();

        DB::table('viajes')
            ->where('id', $viajeId)
            ->update([
                'conductor_id' => (int) $conductor->id,
                'vehiculo_id'  => $vehiculoId ? (int) $vehiculoId : $viaje->vehiculo_id,
                'estado'       => 'asignado',
                'asignado_at'  => $now,
                'aceptado_at'  => $now,
                'updated_at'   => $now,
            ]);

        DB::table('conductores')
            ->where('id', $conductor->id)
            ->update([
                'disponible'     => 0,
                'last_online_at' => $now,
            ]);

       if (Schema::hasTable('viaje_estados_log')) {
    DB::table('viaje_estados_log')->insert([
        'viaje_id'      => $viajeId,
        'from_estado'   => $viaje->estado,
        'to_estado'     => 'asignado',
        'actor_tipo'    => 'conductor',
        'actor_id'      => $userId,
        'motivo_codigo' => 'aceptado', 
        'motivo_texto'  => 'Viaje aceptado por el conductor',
        'app_origen'    => 'app_conductor',
        'ip'            => $request->ip(),
        'created_at'    => $now,
    ]);
}


        $viajeResult = DB::table('viajes')->where('id', $viajeId)->first();
        $updated     = 1;
    });

    if ($updated === 0) {
        return response()->json(['ok' => false, 'message' => 'Otro conductor tomó el viaje'], 409);
    }

    try {
        app(WalletService::class)->debitoAceptacion(
            (int) $conductor->id,
            $viajeId,
            $viajeResult->moneda ?? 'COP'
        );
    } catch (\Throwable $e) {
        \Log::warning('Wallet debito_aceptacion falló', ['viaje_id' => $viajeId, 'err' => $e->getMessage()]);
    }

    $v = $viajeResult;

    try {
        if (!empty($v->pasajero_id)) {
            app(\App\Services\PushService::class)->notifyUsers(
                [(int) $v->pasajero_id],
                'Conductor asignado',
                'Un conductor aceptó tu viaje.',
                [
                    't'        => 'assigned',
                    'viaje_id' => (string) $viajeId,
                ]
            );
        }
    } catch (\Throwable $e) {
        \Log::warning('FCM assigned (pasajero) falló', [
            'viaje_id' => $viajeId,
            'err'      => $e->getMessage(),
        ]);
    }

    return response()->json([
        'ok'    => true,
        'viaje' => [
            'id'     => (int) $v->id,
            'estado' => $v->estado,
            'o'      => [
                'lat' => (float) $v->origen_lat,
                'lng' => (float) $v->origen_lng,
                'txt' => $v->origen_texto,
            ],
            'd'      => [
                'lat' => is_null($v->destino_lat) ? null : (float) $v->destino_lat,
                'lng' => is_null($v->destino_lng) ? null : (float) $v->destino_lng,
                'txt' => $v->destino_texto,
            ],
            'monto' => is_null($v->tarifa_aplicada) ? null : (float) $v->tarifa_aplicada,
            'mon'   => $v->moneda,
        ],
    ]);
}




public function rechazarViaje(Request $request)
{
    $request->validate([
        'viaje_id' => 'required|integer|exists:viajes,id',
    ]);

    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $conductor = DB::table('conductores')->where('user_id', $userId)->first();
    if (!$conductor) {
        return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
    }

    $viajeId = (int) $request->viaje_id;

    DB::transaction(function () use ($viajeId, $userId, $request) {
        $viaje = DB::table('viajes')
            ->where('id', $viajeId)
            ->lockForUpdate()
            ->first();

        if (!$viaje) {
            return;
        }

      
        if ($viaje->estado !== 'buscando' || !is_null($viaje->conductor_id)) {
            return;
        }

        $now = now();

        
        if (Schema::hasTable('viaje_estados_log')) {
            DB::table('viaje_estados_log')->insert([
                'viaje_id'      => $viajeId,
                'from_estado'   => 'buscando',
                'to_estado'     => 'buscando', 
                'actor_tipo'    => 'conductor',
                'actor_id'      => $userId,
                'motivo_codigo' => 'flujo', 
                'motivo_texto'  => 'Conductor rechazó la solicitud',
                'app_origen'    => 'app_conductor',
                'ip'            => $request->ip(),
                'created_at'    => $now,
            ]);
        }

      
        DB::table('viajes')
            ->where('id', $viajeId)
            ->update(['updated_at' => $now]);
    });


    return response()->json(['ok' => true]);
}

public function marcarLlegada(Request $req)
{
    $userId  = auth()->id();
    $viajeId = (int) $req->input('viaje_id');

    $conductor = DB::table('conductores')->where('user_id', $userId)->first();
    if (!$conductor) {
        return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
    }

    $v = DB::table('viajes')->where('id', $viajeId)->first();
    if (!$v || (int)$v->conductor_id !== (int)$conductor->id) {
        return response()->json(['ok' => false, 'message' => 'Viaje no válido'], 404);
    }

    if (!in_array($v->estado, ['asignado', 'en_camino'])) {
        return response()->json(['ok' => false, 'message' => "Estado no permite 'llego'"], 422);
    }

    DB::table('viajes')->where('id', $viajeId)->update([
        'estado'   => 'llego',
        'llego_at' => DB::raw('NOW()'),
    ]);

   
    try {
        if (!empty($v->pasajero_id)) {
            app(\App\Services\PushService::class)->notifyUsers(
                [(int) $v->pasajero_id],
                'Tu taxi ha llegado',
                'El conductor está en el punto de recogida.',
                [
                    't'          => 'arrived',            
                    'viaje_id'   => (string) $viajeId,
                    'conductor'  => (string) $conductor->id,
                ]
            );
        }
    } catch (\Throwable $e) {
        \Log::warning('FCM arrived (pasajero) falló', [
            'viaje_id' => $viajeId,
            'err'      => $e->getMessage(),
        ]);
    }


    return response()->json(['ok' => true]);
}


public function terminarViaje(Request $req)
{
    $userId  = auth()->id();
    if (!$userId) {
        return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
    }

    $req->validate(['viaje_id' => 'required|integer|exists:viajes,id']);
    $viajeId = (int) $req->input('viaje_id');

    $conductor = DB::table('conductores')->where('user_id', $userId)->first();
    if (!$conductor) {
        return response()->json(['ok' => false, 'message' => 'Conductor no encontrado'], 404);
    }

    $v = DB::table('viajes')->where('id', $viajeId)->first();
    if (!$v || (int) $v->conductor_id !== (int) $conductor->id) {
        return response()->json(['ok' => false, 'message' => 'Viaje no válido'], 404);
    }

    if (!in_array($v->estado, ['iniciado', 'llego'], true)) {
        return response()->json(['ok' => false, 'message' => "Estado no permite 'terminado'"], 422);
    }

    DB::transaction(function () use ($req, $v, $conductor, $userId) {
        $viajeId = (int) $v->id;
        $updates = [
            'estado'       => 'terminado',
            'terminado_at' => DB::raw('NOW()'),
        ];

        if (is_null($v->tarifa_aplicada) && $req->filled('monto')) {
            $updates['tarifa_aplicada'] = (float) $req->input('monto');
        }

        DB::table('viajes')
            ->where('id', $viajeId)
            ->update($updates);

        $viajeActualizado = DB::table('viajes')->where('id', $viajeId)->first();
        $tarifa = (float) ($viajeActualizado->tarifa_aplicada ?? $viajeActualizado->tarifa_final ?? $viajeActualizado->tarifa_estimada ?? 0);

        if ($tarifa > 0) {
            try {
                app(WalletService::class)->debitoTerminoViaje(
                    (int) $conductor->id,
                    $viajeId,
                    $tarifa,
                    $viajeActualizado->moneda ?? 'COP'
                );
            } catch (\Throwable $e) {
                \Log::warning('Wallet debito_termino falló', ['viaje_id' => $viajeId, 'err' => $e->getMessage()]);
            }
        }

        DB::table('conductores')
            ->where('id', $conductor->id)
            ->update([
                'disponible'     => 1,
                'last_online_at' => now(),
                'total_viajes'   => DB::raw('total_viajes + 1'),
            ]);

if (Schema::hasTable('viaje_estados_log')) {
            DB::table('viaje_estados_log')->insert([
                'viaje_id'      => $viajeId,
                'from_estado'   => $v->estado,
                'to_estado'     => 'terminado',
                'actor_tipo'    => 'conductor',
                'actor_id'      => $userId,
                'motivo_codigo' => 'fin',
                'motivo_texto'  => 'Viaje finalizado por el conductor',
                'app_origen'    => 'app_conductor',
                'ip'            => $req->ip(),
                'created_at'    => now(),
            ]);
        }
    });

    return response()->json(['ok' => true, 'estado' => 'terminado']);
}

    
	
	function index(Request $request, $fieldname = null , $fieldvalue = null){
		$view = "pages.conductores.list";

		$query = Conductores::query();
		$limit = $request->limit ?? 10;
		if($request->search){
			$search = trim($request->search);
			Conductores::search($query, $search); 
		}
		$orderby = $request->orderby ?? "conductores.id";
		$ordertype = $request->ordertype ?? "desc";
		$query->orderBy($orderby, $ordertype);
		if($fieldname){
			$query->where($fieldname , $fieldvalue); 
		}
	
		if($this->getExportFormat()){
			return $this->ExportList($query, $request); 
		}

		$records = $query->paginate($limit, Conductores::listFields());
		return $this->renderView($view, compact("records"));
	}
	

	
	function importdata(Request $request){
		$importSettings = config("upload.import");
		$maxFileSize = intval($importSettings["max_file_size"]) * 1000; //in kilobyte
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
		Conductores::insert($modeldata);
		return $this->redirect(url()->previous(), "Datos importados con éxito");
	}
	

	
	function view(Request $request, $rec_id = null){
		$query = Conductores::query();
		
		if($this->getExportFormat()){
			return $this->ExportView($query, $rec_id, $request);
		}

		$record = $query->findOrFail($rec_id, Conductores::viewFields());
		return $this->renderView("pages.conductores.view", ["data" => $record]);
	}
	

	
	function masterDetail($rec_id = null){
		return View("pages.conductores.detail-pages", ["masterRecordId" => $rec_id]);
	}
	

	
	function add(){
		return $this->renderView("pages.conductores.add");
	}
	

	
	function store(ConductoresAddRequest $request){
		$modeldata = $this->normalizeFormData($request->validated());
		
		//save Conductores record
		$record = Conductores::create($modeldata);
		$rec_id = $record->id;
		return $this->redirect("conductores", "Grabar agregado exitosamente");
	}
	

	
	function edit(ConductoresEditRequest $request, $rec_id = null){
		$query = Conductores::query();
		$record = $query->findOrFail($rec_id, Conductores::editFields());
		if ($request->isMethod('post')) {
			$modeldata = $this->normalizeFormData($request->validated());
			$record->update($modeldata);
			return $this->redirect("conductores", "Registro actualizado con éxito");
		}
		return $this->renderView("pages.conductores.edit", ["data" => $record, "rec_id" => $rec_id]);
	}
	

	
	function delete(Request $request, $rec_id = null){
		$arr_id = explode(",", $rec_id);
		$query = Conductores::query();
		$query->whereIn("id", $arr_id);
		$query->delete();
		$redirectUrl = $request->redirect ?? url()->previous();
		return $this->redirect($redirectUrl, "Grabar eliminado con éxito");
	}
	

	
	private function ExportList($query, $request){
		ob_end_clean(); // clean any output to allow file download
		$filename = "ListConductoresReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$records = $query->get(Conductores::exportListFields());
			return view("reports.conductores-list", ["records" => $records]);
		}
		elseif($format == "pdf"){
			$records = $query->get(Conductores::exportListFields());
			$pdf = PDF::loadView("reports.conductores-list", ["records" => $records]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new ConductoresListExport($query), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new ConductoresListExport($query), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
	

	
	private function ExportView($query, $rec_id, $request){
		ob_end_clean();// clean any output to allow file download
		$filename ="ViewConductoresReport-" . date_now();
		$format = $this->getExportFormat();

		if($format == "print"){
			$record = $query->findOrFail($rec_id, Conductores::exportViewFields());
			return view("reports.conductores-view", ["record" => $record]);
		}
		elseif($format == "pdf"){
			$record = $query->findOrFail($rec_id, Conductores::exportViewFields());
			$pdf = PDF::loadView("reports.conductores-view", ["record" => $record]);
			return $pdf->download("$filename.pdf");
		}
		elseif($format == "csv"){
			return Excel::download(new ConductoresViewExport($query, $rec_id), "$filename.csv", \Maatwebsite\Excel\Excel::CSV);
		}
		elseif($format == "excel"){
			return Excel::download(new ConductoresViewExport($query, $rec_id), "$filename.xlsx", \Maatwebsite\Excel\Excel::XLSX);
		}
	}
}
