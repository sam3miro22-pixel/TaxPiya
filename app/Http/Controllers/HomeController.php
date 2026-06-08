<?php 

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Services\ReferralService;
/**
 * Home Page Controller
 * @category  Controller
 */
class HomeController extends Controller{
	/**
     * Index Action
     * @return \Illuminate\View\View
     */
	function index(){
		$user = auth()->user();
		if ($user && ($user->hasRole('Pasajero') || $user->hasRole('Conductor'))) {
			app(ReferralService::class)->processPendingBonusesForReferrerUser((int) $user->id);
		}
		if($user->hasRole('admin')){
			return view("pages.home.admin");
		}
		elseif($user->hasRole('pasajero')){
			return view("pages.home.pasajero");
		}
		elseif($user->hasRole('conductor')){
			return view("pages.home.conductor");
		}
		elseif($user->hasRole('empresa')){
			return redirect()->route('empresa.dashboard');
		}
		else{
			return view("pages.home.index");
		}
	}

	function pasajeroPerfil(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Pasajero')) {
			return redirect()->route('home');
		}
		$referrals = app(ReferralService::class);
		$referrals->processPendingBonusesForReferrerUser((int) $user->id);
		$referral = $referrals->statsForUser((int) $user->id);
		$referralShareUrl = url('/pasajero/registro?ref=' . urlencode($referral['codigo'] ?? ''));
		return view('pages.pasajero.perfil', ['user' => $user, 'saved' => session('profile_saved'), 'referral' => $referral, 'referralShareUrl' => $referralShareUrl]);
	}

	function pasajeroPerfilUpdate(Request $request){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Pasajero')) {
			return redirect()->route('home');
		}

		$data = $request->validate([
			'name'            => 'required|string|max:120',
			'fotoperfil'      => 'nullable|string|max:255',
			'fotoperfil_file' => 'nullable|image|max:5120',
		]);

		$update = ['name' => $data['name']];
		if ($request->hasFile('fotoperfil_file')) {
			try {
				$update['fotoperfil'] = app(\App\Services\ProfilePhotoService::class)
					->store($request->file('fotoperfil_file'));
			} catch (\Throwable $e) {
				return back()->withErrors(['fotoperfil' => $e->getMessage()])->withInput();
			}
		} elseif (!empty($data['fotoperfil'])) {
			$update['fotoperfil'] = $data['fotoperfil'];
		}

		DB::table('users')->where('id', $user->id)->update($update);

		return redirect()->route('pasajero.perfil')->with('profile_saved', true);
	}

	function pasajeroViajes(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Pasajero')) {
			return redirect()->route('home');
		}
		$viajes = DB::table('viajes')
			->where('pasajero_id', $user->id)
			->orderByDesc('id')
			->limit(50)
			->get();
		return view('pages.pasajero.viajes', ['viajes' => $viajes]);
	}

	function conductorCuenta(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Conductor')) {
			return redirect()->route('home');
		}
		$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
		$vehiculo = $conductor
			? DB::table('vehiculos')->where('conductor_id', $conductor->id)->first()
			: null;
		app(ReferralService::class)->processPendingBonusesForReferrerUser((int) $user->id);
		$referral = app(ReferralService::class)->statsForUser((int) $user->id);
		$referralShareUrl = url('/conductor/aplicar?ref=' . urlencode($referral['codigo'] ?? ''));
		return view('pages.conductor.cuenta', compact('user', 'conductor', 'vehiculo', 'referral', 'referralShareUrl'));
	}

	function conductorViajes(){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Conductor')) {
			return redirect()->route('home');
		}
		$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
		if (!$conductor) {
			return redirect()->route('home');
		}
		$viajes = DB::table('viajes')
			->where('conductor_id', $conductor->id)
			->orderByDesc('id')
			->limit(50)
			->get();
		return view('pages.conductor.viajes', ['viajes' => $viajes]);
	}

	function conductorWallet(WalletService $wallet, \App\Services\WalletLedgerService $ledger){
		$user = auth()->user();
		if (!$user || !$user->hasRole('Conductor')) {
			return redirect()->route('home');
		}
		$conductor = DB::table('conductores')->where('user_id', $user->id)->first();
		if (!$conductor) {
			return redirect()->route('home');
		}

		$wallet->ensureSaldoRow((int) $conductor->id);
		$ledger->syncConductorPermissions((int) $conductor->id);
		$cuenta = $ledger->ensureCuenta('conductor', (int) $conductor->id);
		$saldo = $wallet->getSaldo((int) $conductor->id);
		$movimientos = $cuenta
			? $ledger->getMovimientos((int) $cuenta->id, 40)
			: DB::table('wallet_movimientos')->where('conductor_id', $conductor->id)->where('anulado', 0)->orderByDesc('id')->limit(40)->get()->all();
		$resumen = $ledger->resumenIngresosConductor((int) $conductor->id);
		$isFlota = $ledger->isConductorFlota($conductor);
		if ($cuenta && !$isFlota && (int) ($cuenta->solo_lectura ?? 0) === 1) {
			$ledger->syncConductorPermissions((int) $conductor->id);
			$cuenta = $ledger->ensureCuenta('conductor', (int) $conductor->id);
		}

		return view('pages.conductor.wallet', compact('saldo', 'movimientos', 'cuenta', 'resumen', 'isFlota'));
	}

	function conductorAplicar(){
		return view('pages.index.conductor_aplicar');
	}

	function conductorAplicarOk(){
		return view('pages.index.conductor_aplicar_ok');
	}

	function conductorAplicarStore(Request $request){
		$data = $request->validate([
			'name'            => 'required|string|max:120',
			'telefono'        => 'required|string|max:20',
			'email'           => 'required|email|max:120',
			'password'        => 'required|string|min:6|confirmed',
			'cedula'          => 'required|string|max:20',
			'ciudad'          => 'required|string|max:80',
			'licencia_numero' => 'required|string|max:64',
			'licencia_categoria' => 'nullable|string|max:16',
			'placa'           => 'required|string|max:16',
			'marca'           => 'required|string|max:64',
			'linea'           => 'required|string|max:64',
			'modelo_anio'     => 'nullable|integer|min:1990|max:2100',
			'color'           => 'nullable|string|max:40',
			'soat_numero'     => 'nullable|string|max:64',
			'codigo_referido' => 'nullable|string|max:20',
		]);

		$referrals = app(ReferralService::class);
		if ($referrals->normalizeCode($data['codigo_referido'] ?? null)) {
			$check = $referrals->validateCode($data['codigo_referido']);
			if (!$check['ok']) {
				return back()->withInput()->with('error', $check['message'] ?? 'Código de referido inválido');
			}
		}

		$exists = DB::table('users')->where('telefono', $data['telefono'])->first();
		if ($exists) {
			$conductorExists = DB::table('conductores')->where('user_id', $exists->id)->exists();
			if ($conductorExists) {
				return back()->withInput()->with('error', 'Ya existe una solicitud o cuenta de conductor con este celular.');
			}
		}

		$now = now()->format('Y-m-d H:i:s');
		$userId = $exists ? (int) $exists->id : null;

        if (!$userId) {
            $userId = DB::table('users')->insertGetId([
                'name'         => $data['name'],
                'telefono'     => $data['telefono'],
                'email'        => $data['email'],
                'password'     => bcrypt($data['password']),
                'estado'       => 0,
                'user_role_id' => 3,
            ]);
            $created = \App\Models\Users::find($userId);
            if ($created) {
                $created->assignRole('Conductor');
            }
        } else {
            DB::table('users')->where('id', $userId)->update([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'password'     => bcrypt($data['password']),
                'estado'       => 0,
                'user_role_id' => 3,
            ]);
        }

        $notas = 'Cédula: ' . $data['cedula'] . ' | Ciudad: ' . $data['ciudad'];
        if (!empty($data['soat_numero'])) {
            $notas .= ' | SOAT: ' . $data['soat_numero'];
        }
        $notas .= ' | Docs a: ' . config('taxpiya.registration.docs_email');

        $conductorId = DB::table('conductores')->insertGetId([
            'user_id'             => $userId,
            'estado_operitivo'    => 0,
            'disponible'          => 0,
            'total_viajes'        => 0,
            'licencia_numero'     => $data['licencia_numero'],
            'licencia_categoria'  => $data['licencia_categoria'] ?? null,
            'soat_numero'         => $data['soat_numero'] ?? null,
            'verificacion_estado' => 'pendiente',
            'verificacion_nivel'  => 0,
            'verificacion_notas'  => $notas,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        DB::table('vehiculos')->insert([
            'conductor_id'        => $conductorId,
            'placa'               => strtoupper($data['placa']),
            'marca'               => $data['marca'],
            'linea'               => $data['linea'],
            'modelo_anio'         => $data['modelo_anio'] ?? null,
            'color'               => $data['color'] ?? null,
            'categoria'           => 'taxi',
            'estado_vehiculo'     => 'inactivo',
            'verificacion_estado' => 'pendiente',
        ]);

		$referrals->ensureUserCode((int) $userId);
		$referrals->registerReferral($data['codigo_referido'] ?? null, (int) $userId, 'conductor');

		return redirect()->route('conductor.aplicar.ok');
	}
	
}
