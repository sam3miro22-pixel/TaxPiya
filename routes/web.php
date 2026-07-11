<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ViajesController;
use App\Http\Controllers\TarifasController;
use App\Http\Controllers\ConductoresController;
use App\Http\Controllers\PushTokensController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\MapProxyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SosController;
use App\Http\Controllers\EmpresaPortalController;
use App\Support\GeoDistance;


Route::get('/api/system/keepalive', function (Request $request) {
    $expected = (string) config('taxpiya.keepalive_key', '');
    $provided = (string) $request->query('key', '');
    if ($expected === '' || !hash_equals($expected, $provided)) {
        abort(403);
    }

    $wa = app(\App\Services\WhatsAppService::class);
    $before = $wa->getStatus();
    $reconnect = null;

    if (($before['status'] ?? '') !== 'connected') {
        $reconnect = $wa->reconnect();
    }

    $after = $wa->getStatus();
    $groq = (string) config('services.groq.api_key', '') !== ''
        || (string) config('taxpiya.assistant.groq_api_key', '') !== '';

    return response()->json([
        'ok'        => true,
        'ts'        => now()->toIso8601String(),
        'whatsapp'  => [
            'before'    => $before['status'] ?? 'unknown',
            'after'     => $after['status'] ?? 'unknown',
            'user'      => $after['user'] ?? null,
            'reconnect' => $reconnect,
        ],
        'assistant' => ['groq' => $groq],
    ]);
})->middleware('throttle:30,1')->name('api.system.keepalive');

Route::get('/tarifa-fija', [TarifasController::class, 'fija'])->name('tarifa.fija');
Route::get('/api/referral/validate', [\App\Http\Controllers\ReferidosController::class, 'validateCode'])->name('api.referral.validate');
Route::get('/api/referral/user-status', function (Request $request) {
    $email = strtolower(trim((string) $request->query('email', '')));
    if ($email === '') {
        return response()->json(['ok' => false, 'message' => 'email requerido'], 422);
    }

    $user = \Illuminate\Support\Facades\DB::table('users')
        ->whereRaw('LOWER(email) = ?', [$email])
        ->orderByDesc('id')
        ->first();
    if (!$user) {
        return response()->json(['ok' => false, 'found' => false, 'message' => 'Usuario no encontrado']);
    }

    $userId = (int) $user->id;
    $referrals = app(\App\Services\ReferralService::class);
    $ledger = app(\App\Services\WalletLedgerService::class);
    $referrals->processPendingBonusesForReferrerUser($userId);
    $ledger->ensureCuenta('pasajero', $userId);

    $cuenta = \Illuminate\Support\Facades\DB::table('wallet_cuentas')
        ->where('tipo', 'pasajero')
        ->where('user_id', $userId)
        ->first();

    $refs = \Illuminate\Support\Facades\DB::table('referidos')
        ->where('referrer_user_id', $userId)
        ->orderByDesc('id')
        ->limit(20)
        ->get(['id', 'referred_user_id', 'estado', 'bonus_paid_at', 'bonus_monto', 'codigo_usado']);

    return response()->json([
        'ok'     => true,
        'found'  => true,
        'user'   => [
            'id'    => $userId,
            'email' => $user->email,
            'name'  => $user->name,
            'code'  => $referrals->codeForUser($userId),
        ],
        'stats'  => $referrals->statsForUser($userId),
        'wallet' => [
            'saldo' => (float) ($cuenta->saldo_actual ?? 0),
            'cuenta_id' => $cuenta->id ?? null,
        ],
        'referidos' => $refs,
    ]);
})->name('api.referral.user-status');

Route::get('/api/internal/sqlite-dump', function (Request $request) {
    if (config('database.default') !== 'sqlite') {
        abort(404);
    }
    $expected = (string) config('taxpiya.persistence.dump_key', '');
    $provided = (string) $request->query('key', '');
    if ($expected === '' || !hash_equals($expected, $provided)) {
        abort(403);
    }
    $path = config('database.connections.sqlite.database');
    if (!is_file($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type'        => 'application/x-sqlite3',
        'Content-Disposition' => 'attachment; filename="taxpiya.sqlite"',
    ]);
})->middleware('throttle:12,1')->name('api.internal.sqlite-dump');

Route::get('pasajero/login', function () {
    return view('pages.index.pasajero_login');
})->name('pasajero.login')->middleware(['redirect.to.home']);


Route::get('conductor/login', function () {
    return view('pages.index.conductor_login');
})->name('conductor.login')->middleware(['redirect.to.home']);

Route::get('empresa/login', function () {
    return view('pages.index.empresa_login');
})->name('empresa.login')->middleware(['redirect.to.home']);

Route::get('empresa/registro', [EmpresaPortalController::class, 'afiliarse'])
    ->name('empresa.registro')->middleware(['redirect.to.home']);
Route::get('empresa/afiliarse', [EmpresaPortalController::class, 'afiliarse'])
    ->name('empresa.afiliarse')->middleware(['redirect.to.home']);
Route::post('empresa/afiliarse', [EmpresaPortalController::class, 'afiliarseStore'])
    ->name('empresa.afiliarse.store');
Route::get('empresa/afiliarse/ok', [EmpresaPortalController::class, 'afiliarseOk'])
    ->name('empresa.afiliarse.ok')->middleware(['redirect.to.home']);


Route::get('pasajero/registro', 'AuthController@register')
    ->name('pasajero.register')->middleware(['redirect.to.home']);

Route::post('pasajero/registro', 'AuthController@register_store')
    ->name('pasajero.register_store');

Route::get('conductor/aplicar', [HomeController::class, 'conductorAplicar'])
    ->name('conductor.aplicar')->middleware(['redirect.to.home']);
Route::get('conductor/registro', [HomeController::class, 'conductorAplicar'])
    ->name('conductor.registro')->middleware(['redirect.to.home']);
Route::post('conductor/aplicar', [HomeController::class, 'conductorAplicarStore'])
    ->name('conductor.aplicar_store');
Route::get('conductor/aplicar/ok', [HomeController::class, 'conductorAplicarOk'])
    ->name('conductor.aplicar.ok');

Route::get('/assistant/diag', function () {
    return response()->json([
        'ok'      => true,
        'version' => 'assistant-v5',
        'table'   => \Illuminate\Support\Facades\Schema::hasTable('assistant_mensajes'),
        'groq'    => (string) config('services.groq.api_key', '') !== ''
            || (string) config('taxpiya.assistant.groq_api_key', '') !== '',
        'curl'    => function_exists('curl_init'),
    ]);
})->name('assistant.diag');

Route::middleware(['auth'])->group(function () {

    Route::get('/empresa', [EmpresaPortalController::class, 'dashboard'])->name('empresa.dashboard');
    Route::get('/empresa/flota', [EmpresaPortalController::class, 'flota'])->name('empresa.flota');
    Route::get('/empresa/flota/nuevo', [EmpresaPortalController::class, 'flotaNuevo'])->name('empresa.flota.nuevo');
    Route::post('/empresa/flota', [EmpresaPortalController::class, 'flotaStore'])->name('empresa.flota.store');
    Route::post('/empresa/flota/vehiculo/{vehiculoId}/conductor', [EmpresaPortalController::class, 'flotaAsignarConductor'])->name('empresa.flota.asignar');
    Route::get('/empresa/contabilidad', [EmpresaPortalController::class, 'contabilidad'])->name('empresa.contabilidad');
    Route::get('/empresa/viajes', [EmpresaPortalController::class, 'viajes'])->name('empresa.viajes');
    Route::get('/empresa/cuenta', [EmpresaPortalController::class, 'cuenta'])->name('empresa.cuenta');
    Route::get('/empresa/wallet', [\App\Http\Controllers\WalletPortalController::class, 'empresaWallet'])->name('empresa.wallet');
    Route::post('/empresa/wallet/depositar', [\App\Http\Controllers\WalletPortalController::class, 'empresaDepositar'])->name('empresa.wallet.depositar');
    Route::post('/empresa/wallet/retirar', [\App\Http\Controllers\WalletPortalController::class, 'empresaRetirar'])->name('empresa.wallet.retirar');
    Route::get('/empresa/flota/{conductorId}/wallet', [\App\Http\Controllers\WalletPortalController::class, 'empresaFlotaWallet'])->name('empresa.flota.wallet');
    Route::post('/empresa/flota/{conductorId}/pagar', [\App\Http\Controllers\WalletPortalController::class, 'empresaPagarConductor'])->name('empresa.flota.pagar');

    Route::get('/pasajero/perfil', [HomeController::class, 'pasajeroPerfil'])->name('pasajero.perfil');
    Route::post('/pasajero/perfil', [HomeController::class, 'pasajeroPerfilUpdate'])->name('pasajero.perfil.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfilePasswordController::class, 'update'])->name('profile.password.update');
    Route::get('/pasajero/viajes', [HomeController::class, 'pasajeroViajes'])->name('pasajero.viajes');
    Route::get('/pasajero/wallet', [\App\Http\Controllers\WalletPortalController::class, 'pasajeroWallet'])->name('pasajero.wallet');
    Route::post('/pasajero/wallet/depositar', [\App\Http\Controllers\WalletPortalController::class, 'pasajeroDepositar'])->name('pasajero.wallet.depositar');
    Route::get('/conductor/cuenta', [HomeController::class, 'conductorCuenta'])->name('conductor.cuenta');
    Route::get('/conductor/viajes', [HomeController::class, 'conductorViajes'])->name('conductor.viajes');
    Route::get('/conductor/wallet', [HomeController::class, 'conductorWallet'])->name('conductor.wallet');
    Route::post('/conductor/wallet/depositar', [\App\Http\Controllers\WalletPortalController::class, 'conductorDepositar'])->name('conductor.wallet.depositar');
    Route::post('/conductor/wallet/retirar', [\App\Http\Controllers\WalletPortalController::class, 'conductorRetirar'])->name('conductor.wallet.retirar');

    Route::get('/assistant/messages', [\App\Http\Controllers\AssistantController::class, 'messages'])->name('assistant.messages');
    Route::post('/assistant/send', [\App\Http\Controllers\AssistantController::class, 'send'])->name('assistant.send');
    Route::post('/assistant/human-support', [\App\Http\Controllers\AssistantController::class, 'humanSupport'])->name('assistant.human-support');

    // Admin WhatsApp panel (RBAC bypass for admin routes)
    Route::get('/admin/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('admin.whatsapp')->withoutMiddleware(['rbac']);
    Route::get('/admin/whatsapp/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('admin.whatsapp.status')->withoutMiddleware(['rbac']);
    Route::post('/admin/whatsapp/logout', [\App\Http\Controllers\WhatsAppController::class, 'logout'])->name('admin.whatsapp.logout')->withoutMiddleware(['rbac']);
    Route::post('/admin/whatsapp/config', [\App\Http\Controllers\WhatsAppController::class, 'saveConfig'])->name('admin.whatsapp.config')->withoutMiddleware(['rbac']);


    Route::get('/api/nearby-drivers', function (Request $req) {
        $lat = (float) $req->query('lat');
        $lng = (float) $req->query('lng');
        $radiusKm = (float) ($req->query('r') ?? config('taxpiya.search_radius_km', 8));

        if (!$lat || !$lng) {
            return response()->json([]);
        }

        $drivers = DB::table('conductores as c')
            ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->where('c.estado_operitivo', 1)
            ->where('c.disponible', 1);

        \App\Support\TripMatching::applyFreshDriverPositionFilter($drivers, 'p');

        $drivers = $drivers->select([
                'c.id as conductor_id',
                'p.lat', 'p.lng', 'p.heading', 'p.velocidad_kmh',
                'v.placa', 'v.marca', 'v.linea',
            ]);

        if (GeoDistance::usesSqlite()) {
            [$minLat, $maxLat, $minLng, $maxLng] = GeoDistance::boundingBox($lat, $lng, $radiusKm);
            $drivers = $drivers
                ->whereBetween('p.lat', [$minLat, $maxLat])
                ->whereBetween('p.lng', [$minLng, $maxLng])
                ->limit(80)
                ->get()
                ->map(function ($d) use ($lat, $lng) {
                    $d->dist_km = GeoDistance::km($lat, $lng, (float) $d->lat, (float) $d->lng);
                    return $d;
                })
                ->filter(fn ($d) => $d->dist_km <= $radiusKm)
                ->sortBy('dist_km')
                ->values()
                ->take(50);
        } else {
            $drivers = $drivers
                ->selectRaw('(6371 * acos( cos(radians(?)) * cos(radians(p.lat)) * cos(radians(p.lng) - radians(?)) + sin(radians(?)) * sin(radians(p.lat)) ) ) as dist_km',
                    [$lat, $lng, $lat]
                )
                ->having('dist_km', '<=', $radiusKm)
                ->orderBy('dist_km')
                ->limit(50)
                ->get();
        }

        return response()->json($drivers);
    })->name('api.nearby-drivers');

    Route::post('/viaje/solicitar', [ViajesController::class, 'solicitar'])->name('viaje.solicitar');
    Route::get('/viaje/activo', [ViajesController::class, 'activo'])->name('viaje.activo');
    Route::get('/viaje/estado/{id}', [ViajesController::class, 'estado'])->name('viaje.estado');
    Route::get('/viaje/{id}/estado', [ViajesController::class, 'estado']);
    Route::post('/viaje/cancelar', [ViajesController::class, 'cancelar'])->name('viaje.cancelar');
    Route::post('/viaje/pasajero/abordo', [ViajesController::class, 'pasajeroAbordo'])->name('viaje.pasajero.abordo');
    Route::post('/viaje/calificar', [ViajesController::class, 'calificar'])->name('viaje.calificar');

    Route::get('/viaje/{id}/chat', [ViajesController::class, 'chatList'])->name('viaje.chat.list');
    Route::post('/viaje/chat/send', [ViajesController::class, 'chatSend'])->name('viaje.chat.send');
    Route::post('/viaje/chat/read', [ViajesController::class, 'chatMarkRead'])->name('viaje.chat.read');
    Route::get('/viaje/{id}/chat-driver', [ViajesController::class, 'chatListDriver'])->name('viaje.chat.list.driver');
    Route::post('/viaje/chat/send-driver', [ViajesController::class, 'chatSendDriver'])->name('viaje.chat.send.driver');
    Route::post('/viaje/chat/read-driver', [ViajesController::class, 'chatMarkReadDriver'])->name('viaje.chat.read.driver');

    Route::get('/conductor/{id}', function ($id) {
        $c = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->where('c.id', $id)
            ->selectRaw('c.id, u.name as nombre, v.placa, v.marca, v.linea')
            ->first();

        if (!$c) {
            return response()->json(['ok' => false, 'message' => 'No encontrado'], 404);
        }

        return response()->json([
            'ok' => true,
            'id' => (int) $c->id,
            'nombre' => $c->nombre,
            'vehiculo' => [
                'placa' => $c->placa,
                'marca' => $c->marca,
                'linea' => $c->linea,
            ],
        ]);
    })->whereNumber('id')->name('conductor.show');

    Route::post('/conductor/disponible', [ConductoresController::class, 'disponible'])->name('conductor.disponible');
    Route::post('/conductor/posicion', [ConductoresController::class, 'posicion'])
        ->name('conductor.posicion')
        ->middleware(['throttle:240,1']);
    Route::get('/conductor/solicitud', [ConductoresController::class, 'solicitudPendiente'])->name('conductor.solicitud');
    Route::post('/viaje/aceptar', [ConductoresController::class, 'aceptarViaje'])->name('viaje.aceptar');
    Route::post('/viaje/rechazar', [ConductoresController::class, 'rechazarViaje'])->name('viaje.rechazar');
    Route::post('/viaje/llego', [ConductoresController::class, 'marcarLlegada'])->name('viaje.llego');
    Route::post('/viaje/terminar', [ConductoresController::class, 'terminarViaje'])->name('viaje.terminar');
    Route::post('/api/push/register', [PushTokensController::class, 'register'])->name('push.register');
    Route::post('/api/sos/reportar', [SosController::class, 'reportar'])->name('sos.reportar');

    Route::get('/api/conductor/wallet', function () {
        $userId = auth()->id();
        $conductor = DB::table('conductores')->where('user_id', $userId)->first();
        if (!$conductor) {
            return response()->json(['ok' => false], 404);
        }
        $wallet = app(\App\Services\WalletService::class);
        $wallet->ensureSaldoRow((int) $conductor->id);
        $saldo = $wallet->getSaldo((int) $conductor->id);
        return response()->json([
            'ok'    => true,
            'saldo' => (float) ($saldo->saldo_actual ?? 0),
            'min'   => (float) ($saldo->min_operativo ?? 0),
            'mon'   => $saldo->moneda ?? 'COP',
        ]);
    })->name('api.conductor.wallet');

    Route::get('/api/geocode', [MapProxyController::class, 'geocode'])->name('api.geocode');
    Route::get('/api/reverse-geocode', [MapProxyController::class, 'reverse'])->name('api.reverse-geocode');
    Route::get('/api/route', [MapProxyController::class, 'route'])->name('api.route');
});



	

	Route::get('', 'IndexController@index')->name('index')->middleware(['redirect.to.home']);
	Route::get('index/login', 'IndexController@login')->name('login');
	
	Route::post('auth/login', function (Request $request) {
		$request->validate([
			'username' => 'required',
			'password' => 'required',
			'app'      => 'nullable|in:pasajero,conductor,empresa',
		]);

		$username = trim((string) $request->input('username'));
		$password = (string) $request->input('password');
		$app      = $request->input('app');
		$isEmail  = filter_var($username, FILTER_VALIDATE_EMAIL) !== false;
		$loginPath = match ($app) {
			'pasajero'  => '/pasajero/login',
			'conductor' => '/conductor/login',
			'empresa'   => '/empresa/login',
			default     => '/index/login',
		};
		$fail = function (string $message) use ($loginPath, $username) {
			session()->flash('auth_error', $message);
			session()->flash('old_username', $username);

			return redirect($loginPath);
		};

		try {
			$user = null;
			if ($isEmail) {
				$user = \App\Models\Users::query()->where('email', $username)->first();
			} else {
				$telDigits = \App\Support\PhoneNormalizer::digits($username);
				$user = \App\Models\Users::query()
					->where(function ($q) use ($username, $telDigits) {
						$q->where('telefono', $username);
						if ($telDigits !== '') {
							$q->orWhere('telefono', $telDigits)
								->orWhere('telefono', 'like', '%' . $telDigits);
						}
					})
					->first();
			}

			if (!$user || !\Illuminate\Support\Facades\Hash::check($password, (string) $user->password)) {
				return $fail('Nombre de usuario o contraseña no correctos');
			}

			if ($app && in_array($app, ['pasajero', 'conductor', 'empresa'], true)) {
				if ((int) ($user->estado ?? 1) !== 1) {
					return $fail('Tu cuenta está inactiva. Por favor comunícate con el Equipo de Taxpiya.');
				}

				$roleName = match ($app) {
					'pasajero'  => 'Pasajero',
					'conductor' => 'Conductor',
					'empresa'   => 'Empresa',
					default     => null,
				};
				if ($roleName) {
					$expectedRoleId = \Illuminate\Support\Facades\DB::table('roles')
						->where('role_name', $roleName)
						->value('role_id');
					if ($expectedRoleId !== null && (int) $user->user_role_id !== (int) $expectedRoleId) {
						$roleMsg = match ($app) {
							'pasajero'  => 'Este acceso es solo para Pasajeros.',
							'conductor' => 'Acceso exclusivo para Conductores.',
							'empresa'   => 'Acceso exclusivo para empresas afiliadas.',
							default     => 'No tienes acceso a este portal.',
						};

						return $fail($roleMsg);
					}
				}

				if ($app === 'conductor') {
					$conductor = \Illuminate\Support\Facades\DB::table('conductores')
						->where('user_id', $user->id)
						->first();
					if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
						return $fail('Tu cuenta de conductor no está activa. Comunícate con el Equipo de Taxpiya.');
					}
					$conductorUpdate = ['disponible' => 0];
					if (\Illuminate\Support\Facades\Schema::hasColumn('conductores', 'updated_at')) {
						$conductorUpdate['updated_at'] = now()->format('Y-m-d H:i:s');
					}
					\Illuminate\Support\Facades\DB::table('conductores')
						->where('id', (int) $conductor->id)
						->update($conductorUpdate);
				} elseif ($app === 'empresa') {
					if (!\Illuminate\Support\Facades\Schema::hasTable('empresas')) {
						return $fail('El portal de empresas no está disponible en este momento.');
					}
					$empresa = \Illuminate\Support\Facades\DB::table('empresas')->where('user_id', $user->id)->first();
					if (!$empresa) {
						return $fail('Tu cuenta no tiene una empresa vinculada.');
					}
					if ((string) ($empresa->estado ?? '') !== 'activa') {
						return $fail('Tu empresa está pendiente de aprobación. Comunícate con el Equipo de Taxpiya.');
					}
				}
			}

			if (!\Illuminate\Support\Facades\Auth::loginUsingId((int) $user->id, false)) {
				return $fail('Nombre de usuario o contraseña no correctos');
			}

			try {
				$request->session()->regenerate();
				$request->session()->save();
				app(\App\Services\SessionGuardService::class)->invalidateOtherSessions($request, (int) auth()->id());
			} catch (\Throwable $e) {
				report($e);
			}

			$destination = match ($app) {
				'empresa'   => '/empresa',
				'pasajero', 'conductor' => '/home',
				default     => \App\Providers\RouteServiceProvider::homeForUser($user, $app),
			};

			return redirect($destination);
		} catch (\Illuminate\Validation\ValidationException $e) {
			throw $e;
		} catch (\Throwable $e) {
			report($e);

			return $fail('Error al iniciar sesión. Intenta de nuevo.');
		}
	})->name('auth.login');

	$firebaseSyncHandler = function (Request $request) {
		try {
			$idToken = (string) $request->input('id_token', '');
			$app     = (string) $request->input('app', 'pasajero');
			if ($idToken === '') {
				return response()->json(['ok' => false, 'message' => 'id_token requerido'], 422);
			}
			if (!in_array($app, ['pasajero', 'conductor', 'empresa'], true)) {
				return response()->json(['ok' => false, 'message' => 'Portal no válido'], 422);
			}

			$apiKey = config('firebase.web.api_key');
			$client = new \GuzzleHttp\Client(['timeout' => 15]);
			$res = $client->post('https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode((string) $apiKey), [
				'json' => ['idToken' => $idToken],
			]);
			$body = json_decode((string) $res->getBody(), true);
			$fbUser = $body['users'][0] ?? null;
			if (!$fbUser) {
				return response()->json(['ok' => false, 'message' => 'Token inválido'], 401);
			}

			$email = isset($fbUser['email']) ? strtolower(trim((string) $fbUser['email'])) : '';
			if ($email === '') {
				return response()->json(['ok' => false, 'message' => 'Token sin correo'], 422);
			}

			$user = \App\Models\Users::query()
				->whereRaw('LOWER(email) = ?', [$email])
				->orderByDesc('id')
				->first();

			if (!$user) {
				if ($app === 'conductor') {
					return response()->json(['ok' => false, 'message' => 'Cuenta de conductor no activa.'], 403);
				}
				if ($app === 'empresa') {
					return response()->json(['ok' => false, 'message' => 'Registra tu empresa primero en TaxPiya.'], 403);
				}
				$uid = (string) ($fbUser['localId'] ?? '');
				$user = \App\Models\Users::create([
					'firebase_uid' => $uid,
					'name'         => $request->input('name') ?: ($fbUser['displayName'] ?? 'Usuario Taxpiya'),
					'email'        => $email,
					'telefono'     => 'fb_' . preg_replace('/[^a-zA-Z0-9]/', '', $uid),
					'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
					'estado'       => 1,
					'user_role_id' => 2,
				]);
				$user->assignRole('Pasajero');
			}

			$expectedRole = match ($app) {
				'conductor' => 'Conductor',
				'empresa'   => 'Empresa',
				default     => 'Pasajero',
			};
			$expectedRoleId = (int) (\Illuminate\Support\Facades\DB::table('roles')->where('role_name', $expectedRole)->value('role_id') ?? 0);
			if ($expectedRoleId > 0 && (int) $user->user_role_id !== $expectedRoleId) {
				return response()->json(['ok' => false, 'message' => 'No tienes acceso a este portal.'], 403);
			}
			if ((int) ($user->estado ?? 1) !== 1) {
				return response()->json(['ok' => false, 'message' => 'Cuenta inactiva.'], 403);
			}
			if ($app === 'conductor') {
				$conductor = \Illuminate\Support\Facades\DB::table('conductores')->where('user_id', $user->id)->first();
				if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
					return response()->json(['ok' => false, 'message' => 'Conductor no activo.'], 403);
				}
			}
			if ($app === 'empresa') {
				if (!\Illuminate\Support\Facades\Schema::hasTable('empresas')) {
					return response()->json(['ok' => false, 'message' => 'Portal de empresas no disponible.'], 403);
				}
				$empresa = \Illuminate\Support\Facades\DB::table('empresas')->where('user_id', $user->id)->first();
				if (!$empresa) {
					return response()->json(['ok' => false, 'message' => 'Tu cuenta no tiene empresa vinculada.'], 403);
				}
				if ((string) ($empresa->estado ?? '') !== 'activa') {
					return response()->json(['ok' => false, 'message' => 'Tu empresa está pendiente de aprobación.'], 403);
				}
			}

			if (!\Illuminate\Support\Facades\Auth::loginUsingId((int) $user->id, false)) {
				return response()->json(['ok' => false, 'message' => 'No se pudo iniciar sesión.'], 500);
			}
			$request->session()->save();

			$uid = (string) ($fbUser['localId'] ?? '');
			if ($uid !== '') {
				try {
					\Illuminate\Support\Facades\DB::table('users')
						->where('firebase_uid', $uid)
						->where('id', '!=', $user->id)
						->update(['firebase_uid' => null]);
					\Illuminate\Support\Facades\DB::table('users')
						->where('id', $user->id)
						->update(['firebase_uid' => $uid]);
				} catch (\Throwable $e) {
					report($e);
				}
			}

			$redirect = $app === 'empresa' ? '/empresa' : '/home';

			return response()->json(['ok' => true, 'user_id' => $user->id, 'redirect' => $redirect]);
		} catch (\Throwable $e) {
			report($e);
			return response()->json(['ok' => false, 'message' => 'Sync: ' . $e->getMessage()], 500);
		}
	};

	Route::post('auth/firebase/sync', $firebaseSyncHandler)->name('auth.firebase.sync');
	Route::post('auth/firebase/diag-sync', $firebaseSyncHandler)->name('auth.firebase.diag-sync');
	Route::get('auth/firebase/diag', function () {
		$checks = [
			'users_firebase_uid'      => \Illuminate\Support\Facades\Schema::hasColumn('users', 'firebase_uid'),
			'users_codigo_referido'   => \Illuminate\Support\Facades\Schema::hasColumn('users', 'codigo_referido'),
			'sessions_table'          => \Illuminate\Support\Facades\Schema::hasTable('sessions'),
			'wallet_cuentas'          => \Illuminate\Support\Facades\Schema::hasTable('wallet_cuentas'),
			'wallet_puede_depositar'  => \Illuminate\Support\Facades\Schema::hasColumn('wallet_cuentas', 'puede_depositar'),
			'role_pasajero'           => \Illuminate\Support\Facades\DB::table('roles')->where('role_name', 'Pasajero')->exists(),
			'session_driver'          => config('session.driver'),
			'firebase_auth'           => (bool) config('taxpiya.firebase.use_firebase_auth'),
		];
		try {
			\Illuminate\Support\Facades\DB::beginTransaction();
			\Illuminate\Support\Facades\DB::table('users')->insertGetId([
				'name'         => '_probe_',
				'email'        => '_probe_' . uniqid('', true) . '@test.local',
				'telefono'     => 'fbprobe' . substr(uniqid('', true), -10),
				'password'     => bcrypt('x'),
				'estado'       => 1,
				'user_role_id' => 2,
				'firebase_uid' => 'probe_' . uniqid('', true),
			]);
			\Illuminate\Support\Facades\DB::rollBack();
			$checks['user_insert_probe'] = 'ok';
		} catch (\Throwable $e) {
			\Illuminate\Support\Facades\DB::rollBack();
			$checks['user_insert_probe'] = $e->getMessage();
		}
		try {
			$sid = 'probe_' . uniqid('', true);
			\Illuminate\Support\Facades\DB::table('sessions')->insert([
				'id'            => $sid,
				'user_id'       => null,
				'ip_address'    => '127.0.0.1',
				'user_agent'    => 'diag',
				'payload'       => base64_encode('test'),
				'last_activity' => time(),
			]);
			\Illuminate\Support\Facades\DB::table('sessions')->where('id', $sid)->delete();
			$checks['session_insert_probe'] = 'ok';
		} catch (\Throwable $e) {
			$checks['session_insert_probe'] = $e->getMessage();
		}
		$checks['firebase_credentials'] = is_readable(config('firebase.credentials'));
		$checks['kreait_available'] = class_exists(\Kreait\Firebase\Factory::class);
		try {
			$loggedIn = \Illuminate\Support\Facades\Auth::attempt([
				'telefono' => '__diag_missing_user__',
				'password' => '__diag__',
			], false);
			$checks['auth_attempt_missing_user'] = $loggedIn === false ? 'ok' : 'unexpected_true';
		} catch (\Throwable $e) {
			$checks['auth_attempt_missing_user'] = $e->getMessage();
		}
		try {
			$probe = redirect('/index/login')->with('auth_error', 'probe');
			$checks['login_redirect_probe'] = $probe->getTargetUrl() ? 'ok' : 'no_target';
		} catch (\Throwable $e) {
			$checks['login_redirect_probe'] = $e->getMessage();
		}
		$checks['login_flow_version'] = 'firebase-sync-v12-closure';
		return response()->json($checks);
	})->name('auth.firebase.diag');
	Route::any('auth/logout', 'AuthController@logout')->name('logout')->middleware(['auth']);

	Route::get('auth/accountcreated', 'AuthController@accountcreated')->name('accountcreated');
	Route::get('auth/accountpending', 'AuthController@accountpending')->name('accountpending');
	Route::get('auth/accountblocked', 'AuthController@accountblocked')->name('accountblocked');
	Route::get('auth/accountinactive', 'AuthController@accountinactive')->name('accountinactive');


	
	Route::redirect('index/register', '/pasajero/registro', 301);
	Route::post('index/register', 'AuthController@register_store')->name('auth.register_store');
		
	Route::get('auth/password/forgotpassword', function () {
		return redirect()->route('login')->with('auth_error', 'Recuperación por correo no disponible. Cambia tu contraseña desde Mi perfil si ya tienes sesión.');
	})->name('password.forgotpassword');
	Route::post('auth/password/sendemail', 'AuthController@sendPasswordResetLink')->name('password.email');
	Route::get('auth/password/reset', 'AuthController@showResetPassword')->name('password.reset.token');
	Route::post('auth/password/resetpassword', 'AuthController@resetPassword')->name('password.resetpassword');
	Route::get('auth/password/resetcompleted', 'AuthController@passwordResetCompleted')->name('password.resetcompleted');
	Route::get('auth/password/linksent', 'AuthController@passwordResetLinkSent')->name('password.resetlinksent');
	


Route::middleware(['auth', 'rbac'])->group(function () {

    Route::get('/api/admin/active-drivers', function () {
        $drivers = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('conductor_posicion_actual as p', 'p.conductor_id', '=', 'c.id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->where('c.estado_operitivo', 1)
            ->where('c.disponible', 1)
            ->selectRaw('
                c.id as conductor_id,
                u.name as nombre,
                p.lat,
                p.lng,
                p.velocidad_kmh,
                p.heading,
                COALESCE(p.actualizada_at, c.last_online_at) as last_at,
                v.placa,
                v.marca,
                v.linea
            ')
            ->get();

        return response()->json($drivers);
    })->withoutMiddleware(['rbac'])->name('api.admin.active-drivers');
		
	Route::get('home', 'HomeController@index')->name('home');

	


	Route::get('asignaciones', 'AsignacionesController@index')->name('asignaciones');
	Route::get('asignaciones/index/{filter?}/{filtervalue?}', 'AsignacionesController@index')->name('asignaciones.index');	
	Route::post('asignaciones/importdata', 'AsignacionesController@importdata');	
	Route::get('asignaciones/view/{rec_id}', 'AsignacionesController@view')->name('asignaciones.view');	
	Route::get('asignaciones/add', 'AsignacionesController@add')->name('asignaciones.add');
	Route::post('asignaciones/add', 'AsignacionesController@store')->name('asignaciones.store');
		
	Route::any('asignaciones/edit/{rec_id}', 'AsignacionesController@edit')->name('asignaciones.edit');	
	Route::get('asignaciones/delete/{rec_id}', 'AsignacionesController@delete');


	Route::get('auditoriaeventos', 'AuditoriaEventosController@index')->name('auditoriaeventos');
	Route::get('auditoriaeventos/index/{filter?}/{filtervalue?}', 'AuditoriaEventosController@index')->name('auditoriaeventos.index');	
	Route::post('auditoriaeventos/importdata', 'AuditoriaEventosController@importdata');	
	Route::get('auditoriaeventos/view/{rec_id}', 'AuditoriaEventosController@view')->name('auditoriaeventos.view');	
	Route::get('auditoriaeventos/add', 'AuditoriaEventosController@add')->name('auditoriaeventos.add');
	Route::post('auditoriaeventos/add', 'AuditoriaEventosController@store')->name('auditoriaeventos.store');
		
	Route::any('auditoriaeventos/edit/{rec_id}', 'AuditoriaEventosController@edit')->name('auditoriaeventos.edit');	
	Route::get('auditoriaeventos/delete/{rec_id}', 'AuditoriaEventosController@delete');


	Route::get('calificaciones', 'CalificacionesController@index')->name('calificaciones');
	Route::get('calificaciones/index/{filter?}/{filtervalue?}', 'CalificacionesController@index')->name('calificaciones.index');	
	Route::post('calificaciones/importdata', 'CalificacionesController@importdata');	
	Route::get('calificaciones/view/{rec_id}', 'CalificacionesController@view')->name('calificaciones.view');	
	Route::get('calificaciones/add', 'CalificacionesController@add')->name('calificaciones.add');
	Route::post('calificaciones/add', 'CalificacionesController@store')->name('calificaciones.store');
		
	Route::any('calificaciones/edit/{rec_id}', 'CalificacionesController@edit')->name('calificaciones.edit');	
	Route::get('calificaciones/delete/{rec_id}', 'CalificacionesController@delete');


	Route::get('chatmensajes', 'ChatMensajesController@index')->name('chatmensajes');
	Route::get('chatmensajes/index/{filter?}/{filtervalue?}', 'ChatMensajesController@index')->name('chatmensajes.index');	
	Route::post('chatmensajes/importdata', 'ChatMensajesController@importdata');	
	Route::get('chatmensajes/view/{rec_id}', 'ChatMensajesController@view')->name('chatmensajes.view');
	Route::get('chatmensajes/masterdetail/{rec_id}', 'ChatMensajesController@masterDetail')->name('chatmensajes.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('chatmensajes/add', 'ChatMensajesController@add')->name('chatmensajes.add');
	Route::post('chatmensajes/add', 'ChatMensajesController@store')->name('chatmensajes.store');
		
	Route::any('chatmensajes/edit/{rec_id}', 'ChatMensajesController@edit')->name('chatmensajes.edit');	
	Route::get('chatmensajes/delete/{rec_id}', 'ChatMensajesController@delete');


	Route::get('conductores', 'ConductoresController@index')->name('conductores');
	Route::get('conductores/index/{filter?}/{filtervalue?}', 'ConductoresController@index')->name('conductores.index');	
	Route::post('conductores/importdata', 'ConductoresController@importdata');	
	Route::get('conductores/view/{rec_id}', 'ConductoresController@view')->name('conductores.view');
	Route::get('conductores/masterdetail/{rec_id}', 'ConductoresController@masterDetail')->name('conductores.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('conductores/add', 'ConductoresController@add')->name('conductores.add');
	Route::post('conductores/add', 'ConductoresController@store')->name('conductores.store');
		
	Route::any('conductores/edit/{rec_id}', 'ConductoresController@edit')->name('conductores.edit');	
	Route::get('conductores/delete/{rec_id}', 'ConductoresController@delete');

	Route::get('empresas', 'EmpresasController@index')->name('empresas');
	Route::get('empresas/index/{filter?}/{filtervalue?}', 'EmpresasController@index')->name('empresas.index');
	Route::get('empresas/view/{rec_id}', 'EmpresasController@view')->name('empresas.view');
	Route::any('empresas/edit/{rec_id}', 'EmpresasController@edit')->name('empresas.edit');

	Route::get('referidos', 'ReferidosController@index')->name('referidos');
	Route::get('referidos/index/{filter?}/{filtervalue?}', 'ReferidosController@index')->name('referidos.index');

	Route::get('conductorposicionactual', 'ConductorPosicionActualController@index')->name('conductorposicionactual');
	Route::get('conductorposicionactual/index/{filter?}/{filtervalue?}', 'ConductorPosicionActualController@index')->name('conductorposicionactual.index');	
	Route::post('conductorposicionactual/importdata', 'ConductorPosicionActualController@importdata');	
	Route::get('conductorposicionactual/view/{rec_id}', 'ConductorPosicionActualController@view')->name('conductorposicionactual.view');	
	Route::get('conductorposicionactual/add', 'ConductorPosicionActualController@add')->name('conductorposicionactual.add');
	Route::post('conductorposicionactual/add', 'ConductorPosicionActualController@store')->name('conductorposicionactual.store');
		
	Route::any('conductorposicionactual/edit/{rec_id}', 'ConductorPosicionActualController@edit')->name('conductorposicionactual.edit');	
	Route::get('conductorposicionactual/delete/{rec_id}', 'ConductorPosicionActualController@delete');


	Route::get('conductorposiciones', 'ConductorPosicionesController@index')->name('conductorposiciones');
	Route::get('conductorposiciones/index/{filter?}/{filtervalue?}', 'ConductorPosicionesController@index')->name('conductorposiciones.index');	
	Route::post('conductorposiciones/importdata', 'ConductorPosicionesController@importdata');	
	Route::get('conductorposiciones/view/{rec_id}', 'ConductorPosicionesController@view')->name('conductorposiciones.view');	
	Route::get('conductorposiciones/add', 'ConductorPosicionesController@add')->name('conductorposiciones.add');
	Route::post('conductorposiciones/add', 'ConductorPosicionesController@store')->name('conductorposiciones.store');
		
	Route::any('conductorposiciones/edit/{rec_id}', 'ConductorPosicionesController@edit')->name('conductorposiciones.edit');	
	Route::get('conductorposiciones/delete/{rec_id}', 'ConductorPosicionesController@delete');


	Route::get('documentosconductor', 'DocumentosConductorController@index')->name('documentosconductor');
	Route::get('documentosconductor/index/{filter?}/{filtervalue?}', 'DocumentosConductorController@index')->name('documentosconductor.index');	
	Route::post('documentosconductor/importdata', 'DocumentosConductorController@importdata');	
	Route::get('documentosconductor/view/{rec_id}', 'DocumentosConductorController@view')->name('documentosconductor.view');	
	Route::get('documentosconductor/add', 'DocumentosConductorController@add')->name('documentosconductor.add');
	Route::post('documentosconductor/add', 'DocumentosConductorController@store')->name('documentosconductor.store');
		
	Route::any('documentosconductor/edit/{rec_id}', 'DocumentosConductorController@edit')->name('documentosconductor.edit');	
	Route::get('documentosconductor/delete/{rec_id}', 'DocumentosConductorController@delete');

	Route::get('llamadas', 'LlamadasController@index')->name('llamadas');
	Route::get('llamadas/index/{filter?}/{filtervalue?}', 'LlamadasController@index')->name('llamadas.index');	
	Route::post('llamadas/importdata', 'LlamadasController@importdata');	
	Route::get('llamadas/view/{rec_id}', 'LlamadasController@view')->name('llamadas.view');	
	Route::get('llamadas/add', 'LlamadasController@add')->name('llamadas.add');
	Route::post('llamadas/add', 'LlamadasController@store')->name('llamadas.store');
		
	Route::any('llamadas/edit/{rec_id}', 'LlamadasController@edit')->name('llamadas.edit');	
	Route::get('llamadas/delete/{rec_id}', 'LlamadasController@delete');


	Route::get('notasoperacion', 'NotasOperacionController@index')->name('notasoperacion');
	Route::get('notasoperacion/index/{filter?}/{filtervalue?}', 'NotasOperacionController@index')->name('notasoperacion.index');	
	Route::post('notasoperacion/importdata', 'NotasOperacionController@importdata');	
	Route::get('notasoperacion/view/{rec_id}', 'NotasOperacionController@view')->name('notasoperacion.view');	
	Route::get('notasoperacion/add', 'NotasOperacionController@add')->name('notasoperacion.add');
	Route::post('notasoperacion/add', 'NotasOperacionController@store')->name('notasoperacion.store');
		
	Route::any('notasoperacion/edit/{rec_id}', 'NotasOperacionController@edit')->name('notasoperacion.edit');	
	Route::get('notasoperacion/delete/{rec_id}', 'NotasOperacionController@delete');


	Route::get('notificaciones', 'NotificacionesController@index')->name('notificaciones');
	Route::get('notificaciones/index/{filter?}/{filtervalue?}', 'NotificacionesController@index')->name('notificaciones.index');	
	Route::post('notificaciones/importdata', 'NotificacionesController@importdata');	
	Route::get('notificaciones/view/{rec_id}', 'NotificacionesController@view')->name('notificaciones.view');	
	Route::get('notificaciones/add', 'NotificacionesController@add')->name('notificaciones.add');
	Route::post('notificaciones/add', 'NotificacionesController@store')->name('notificaciones.store');
		
	Route::any('notificaciones/edit/{rec_id}', 'NotificacionesController@edit')->name('notificaciones.edit');	
	Route::get('notificaciones/delete/{rec_id}', 'NotificacionesController@delete');


	Route::get('permissions', 'PermissionsController@index')->name('permissions');
	Route::get('permissions/index/{filter?}/{filtervalue?}', 'PermissionsController@index')->name('permissions.index');	
	Route::post('permissions/importdata', 'PermissionsController@importdata');	
	Route::get('permissions/view/{rec_id}', 'PermissionsController@view')->name('permissions.view');	
	Route::get('permissions/add', 'PermissionsController@add')->name('permissions.add');
	Route::post('permissions/add', 'PermissionsController@store')->name('permissions.store');
		
	Route::any('permissions/edit/{rec_id}', 'PermissionsController@edit')->name('permissions.edit');	
	Route::get('permissions/delete/{rec_id}', 'PermissionsController@delete');

	Route::get('pushtokens', 'PushTokensController@index')->name('pushtokens');
	Route::get('pushtokens/index/{filter?}/{filtervalue?}', 'PushTokensController@index')->name('pushtokens.index');	
	Route::post('pushtokens/importdata', 'PushTokensController@importdata');	
	Route::get('pushtokens/view/{rec_id}', 'PushTokensController@view')->name('pushtokens.view');	
	Route::get('pushtokens/add', 'PushTokensController@add')->name('pushtokens.add');
	Route::post('pushtokens/add', 'PushTokensController@store')->name('pushtokens.store');
		
	Route::any('pushtokens/edit/{rec_id}', 'PushTokensController@edit')->name('pushtokens.edit');	
	Route::get('pushtokens/delete/{rec_id}', 'PushTokensController@delete');


	Route::get('roles', 'RolesController@index')->name('roles');
	Route::get('roles/index/{filter?}/{filtervalue?}', 'RolesController@index')->name('roles.index');	
	Route::post('roles/importdata', 'RolesController@importdata');	
	Route::get('roles/view/{rec_id}', 'RolesController@view')->name('roles.view');
	Route::get('roles/masterdetail/{rec_id}', 'RolesController@masterDetail')->name('roles.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('roles/add', 'RolesController@add')->name('roles.add');
	Route::post('roles/add', 'RolesController@store')->name('roles.store');
		
	Route::any('roles/edit/{rec_id}', 'RolesController@edit')->name('roles.edit');	
	Route::get('roles/delete/{rec_id}', 'RolesController@delete');

	Route::get('sosincidentes', 'SosIncidentesController@index')->name('sosincidentes');
	Route::get('sosincidentes/index/{filter?}/{filtervalue?}', 'SosIncidentesController@index')->name('sosincidentes.index');	
	Route::post('sosincidentes/importdata', 'SosIncidentesController@importdata');	
	Route::get('sosincidentes/view/{rec_id}', 'SosIncidentesController@view')->name('sosincidentes.view');	
	Route::get('sosincidentes/add', 'SosIncidentesController@add')->name('sosincidentes.add');
	Route::post('sosincidentes/add', 'SosIncidentesController@store')->name('sosincidentes.store');
		
	Route::any('sosincidentes/edit/{rec_id}', 'SosIncidentesController@edit')->name('sosincidentes.edit');	
	Route::get('sosincidentes/delete/{rec_id}', 'SosIncidentesController@delete');


	Route::get('tarifas', 'TarifasController@index')->name('tarifas');
	Route::get('tarifas/index/{filter?}/{filtervalue?}', 'TarifasController@index')->name('tarifas.index');	
	Route::post('tarifas/importdata', 'TarifasController@importdata');	
	Route::get('tarifas/view/{rec_id}', 'TarifasController@view')->name('tarifas.view');
	Route::get('tarifas/masterdetail/{rec_id}', 'TarifasController@masterDetail')->name('tarifas.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('tarifas/add', 'TarifasController@add')->name('tarifas.add');
	Route::post('tarifas/add', 'TarifasController@store')->name('tarifas.store');
		
	Route::any('tarifas/edit/{rec_id}', 'TarifasController@edit')->name('tarifas.edit');	
	Route::get('tarifas/delete/{rec_id}', 'TarifasController@delete');

	Route::get('users', 'UsersController@index')->name('users');
	Route::get('users/index/{filter?}/{filtervalue?}', 'UsersController@index')->name('users.index');	
	Route::post('users/importdata', 'UsersController@importdata');	
	Route::get('users/view/{rec_id}', 'UsersController@view')->name('users.view');
	Route::get('users/masterdetail/{rec_id}', 'UsersController@masterDetail')->name('users.masterdetail')->withoutMiddleware(['rbac']);	
	Route::any('account/edit', 'AccountController@edit')->name('account.edit');	
	Route::get('account', 'AccountController@index');	
	Route::post('account/changepassword', 'AccountController@changepassword')->name('account.changepassword');	
	Route::get('users/add', 'UsersController@add')->name('users.add');
	Route::post('users/add', 'UsersController@store')->name('users.store');
		
	Route::any('users/edit/{rec_id}', 'UsersController@edit')->name('users.edit');	
	Route::get('users/delete/{rec_id}', 'UsersController@delete');

	Route::get('usuariodispositivos', 'UsuarioDispositivosController@index')->name('usuariodispositivos');
	Route::get('usuariodispositivos/index/{filter?}/{filtervalue?}', 'UsuarioDispositivosController@index')->name('usuariodispositivos.index');	
	Route::post('usuariodispositivos/importdata', 'UsuarioDispositivosController@importdata');	
	Route::get('usuariodispositivos/view/{rec_id}', 'UsuarioDispositivosController@view')->name('usuariodispositivos.view');
	Route::get('usuariodispositivos/masterdetail/{rec_id}', 'UsuarioDispositivosController@masterDetail')->name('usuariodispositivos.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('usuariodispositivos/add', 'UsuarioDispositivosController@add')->name('usuariodispositivos.add');
	Route::post('usuariodispositivos/add', 'UsuarioDispositivosController@store')->name('usuariodispositivos.store');
		
	Route::any('usuariodispositivos/edit/{rec_id}', 'UsuarioDispositivosController@edit')->name('usuariodispositivos.edit');	
	Route::get('usuariodispositivos/delete/{rec_id}', 'UsuarioDispositivosController@delete');

	Route::get('vehiculos', 'VehiculosController@index')->name('vehiculos');
	Route::get('vehiculos/index/{filter?}/{filtervalue?}', 'VehiculosController@index')->name('vehiculos.index');	
	Route::post('vehiculos/importdata', 'VehiculosController@importdata');	
	Route::get('vehiculos/view/{rec_id}', 'VehiculosController@view')->name('vehiculos.view');
	Route::get('vehiculos/masterdetail/{rec_id}', 'VehiculosController@masterDetail')->name('vehiculos.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('vehiculos/add', 'VehiculosController@add')->name('vehiculos.add');
	Route::post('vehiculos/add', 'VehiculosController@store')->name('vehiculos.store');
		
	Route::any('vehiculos/edit/{rec_id}', 'VehiculosController@edit')->name('vehiculos.edit');	
	Route::get('vehiculos/delete/{rec_id}', 'VehiculosController@delete');


	Route::get('viajeestadoslog', 'ViajeEstadosLogController@index')->name('viajeestadoslog');
	Route::get('viajeestadoslog/index/{filter?}/{filtervalue?}', 'ViajeEstadosLogController@index')->name('viajeestadoslog.index');	
	Route::post('viajeestadoslog/importdata', 'ViajeEstadosLogController@importdata');	
	Route::get('viajeestadoslog/view/{rec_id}', 'ViajeEstadosLogController@view')->name('viajeestadoslog.view');	
	Route::get('viajeestadoslog/add', 'ViajeEstadosLogController@add')->name('viajeestadoslog.add');
	Route::post('viajeestadoslog/add', 'ViajeEstadosLogController@store')->name('viajeestadoslog.store');
		
	Route::any('viajeestadoslog/edit/{rec_id}', 'ViajeEstadosLogController@edit')->name('viajeestadoslog.edit');	
	Route::get('viajeestadoslog/delete/{rec_id}', 'ViajeEstadosLogController@delete');

	Route::get('viajes', 'ViajesController@index')->name('viajes');
	Route::get('viajes/index/{filter?}/{filtervalue?}', 'ViajesController@index')->name('viajes.index');	
	Route::post('viajes/importdata', 'ViajesController@importdata');	
	Route::get('viajes/view/{rec_id}', 'ViajesController@view')->name('viajes.view');
	Route::get('viajes/masterdetail/{rec_id}', 'ViajesController@masterDetail')->name('viajes.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('viajes/add', 'ViajesController@add')->name('viajes.add');
	Route::post('viajes/add', 'ViajesController@store')->name('viajes.store');
		
	Route::any('viajes/edit/{rec_id}', 'ViajesController@edit')->name('viajes.edit');	
	Route::get('viajes/delete/{rec_id}', 'ViajesController@delete');


	Route::get('walletmovimientos', 'WalletMovimientosController@index')->name('walletmovimientos');
	Route::get('walletmovimientos/index/{filter?}/{filtervalue?}', 'WalletMovimientosController@index')->name('walletmovimientos.index');	
	Route::post('walletmovimientos/importdata', 'WalletMovimientosController@importdata');	
	Route::get('walletmovimientos/view/{rec_id}', 'WalletMovimientosController@view')->name('walletmovimientos.view');
	Route::get('walletmovimientos/masterdetail/{rec_id}', 'WalletMovimientosController@masterDetail')->name('walletmovimientos.masterdetail')->withoutMiddleware(['rbac']);	
	Route::get('walletmovimientos/add', 'WalletMovimientosController@add')->name('walletmovimientos.add');
	Route::post('walletmovimientos/add', 'WalletMovimientosController@store')->name('walletmovimientos.store');
		
	Route::any('walletmovimientos/edit/{rec_id}', 'WalletMovimientosController@edit')->name('walletmovimientos.edit');	
	Route::get('walletmovimientos/delete/{rec_id}', 'WalletMovimientosController@delete');


	Route::get('walletsaldos', 'WalletSaldosController@index')->name('walletsaldos');
	Route::get('walletsaldos/index/{filter?}/{filtervalue?}', 'WalletSaldosController@index')->name('walletsaldos.index');	
	Route::post('walletsaldos/importdata', 'WalletSaldosController@importdata');	
	Route::get('walletsaldos/view/{rec_id}', 'WalletSaldosController@view')->name('walletsaldos.view');	
	Route::get('walletsaldos/add', 'WalletSaldosController@add')->name('walletsaldos.add');
	Route::post('walletsaldos/add', 'WalletSaldosController@store')->name('walletsaldos.store');
		
	Route::any('walletsaldos/edit/{rec_id}', 'WalletSaldosController@edit')->name('walletsaldos.edit');	
	Route::get('walletsaldos/delete/{rec_id}', 'WalletSaldosController@delete');

	Route::get('walletsolicitudes', 'WalletSolicitudesController@index')->name('walletsolicitudes');
	Route::get('walletsolicitudes/view/{rec_id}', 'WalletSolicitudesController@view')->name('walletsolicitudes.view');
	Route::post('walletsolicitudes/aprobar/{rec_id}', 'WalletSolicitudesController@aprobar')->name('walletsolicitudes.aprobar');
	Route::post('walletsolicitudes/rechazar/{rec_id}', 'WalletSolicitudesController@rechazar')->name('walletsolicitudes.rechazar');
});


	
Route::get('componentsdata/viaje_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->viaje_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/conductor_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->conductor_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/actor_user_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->actor_user_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/reply_to_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->reply_to_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/dispositivo_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->dispositivo_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/role_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->role_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('componentsdata/users_name_value_exist', function (Request $request) {
        $compModel = new App\Models\ComponentsData();
        return $compModel->users_name_value_exist($request);
    });

    Route::get('componentsdata/users_email_value_exist', function (Request $request) {
        $compModel = new App\Models\ComponentsData();
        return $compModel->users_email_value_exist($request);
    });
});
	
Route::get('componentsdata/vehiculo_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->vehiculo_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/tarifa_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->tarifa_id_option_list($request);
	}
)->middleware(['auth']);
	
Route::get('componentsdata/last_movimiento_id_option_list',  function(Request $request){
		$compModel = new App\Models\ComponentsData();
		return $compModel->last_movimiento_id_option_list($request);
	}
)->middleware(['auth']);


Route::post('fileuploader/upload/fotoperfil', [\App\Http\Controllers\FileUploaderController::class, 'upload'])
    ->defaults('fieldname', 'fotoperfil');
Route::post('fileuploader/remove_temp_file', [\App\Http\Controllers\FileUploaderController::class, 'remove_temp_file']);

Route::middleware(['auth'])->group(function () {
    Route::post('fileuploader/upload/{fieldname}', [\App\Http\Controllers\FileUploaderController::class, 'upload'])
        ->where('fieldname', '^(?!fotoperfil$).+');
    Route::post('fileuploader/s3upload/{fieldname}', [\App\Http\Controllers\FileUploaderController::class, 's3upload']);
});



Route::get('info/about',  function(){
		return view("pages.info.about");
	}
);
Route::get('info/faq',  function(){
		return view("pages.info.faq");
	}
);

Route::get('info/contact',  function(){
	return view("pages.info.contact");
}
);
Route::get('info/contactsent',  function(){
	return view("pages.info.contactsent");
}
);

Route::post('info/contact',  function(Request $request){
		$request->validate([
			'name' => 'required',
			'email' => 'required|email',
			'message' => 'required'
		]);

		$senderName = $request->name;
		$senderEmail = $request->email;
		$message = $request->message;

		$receiverEmail = config("mail.from.address");

		Mail::send(
			'pages.info.contactemail', [
				'name' => $senderName,
				'email' => $senderEmail,
				'comment' => $message
			],
			function ($mail) use ($senderEmail, $receiverEmail) {
				$mail->from($senderEmail);
				$mail->to($receiverEmail)
					->subject('Contact Form');
			}
		);
		return redirect("info/contactsent");
	}
);


Route::get('info/features',  function(){
		return view("pages.info.features");
	}
);
Route::get('info/guia-roles', function () {
    return view('pages.info.guia-roles');
})->name('info.guia-roles');
Route::get('info/privacypolicy',  function(){
		return view("pages.info.privacypolicy");
	}
);
Route::get('info/termsandconditions',  function(){
		return view("pages.info.termsandconditions");
	}
);

Route::get('info/changelocale/{locale}', function ($locale) {
	app()->setlocale($locale);
	session()->put('locale', $locale);
    return redirect()->back();
})->name('info.changelocale');