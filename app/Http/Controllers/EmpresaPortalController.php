<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Services\WalletService;
use App\Services\ReferralService;
use App\Services\Firebase\FirebaseIdentityService;
use App\Services\Firebase\FirestoreUserService;
use App\Services\WalletLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpresaPortalController extends Controller
{
    private function empresaForUser(int $userId): ?object
    {
        return DB::table('empresas')->where('user_id', $userId)->first();
    }

    private function requireEmpresa()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Empresa')) {
            return redirect()->route('empresa.login');
        }

        $empresa = $this->empresaForUser((int) $user->id);
        if (!$empresa) {
            Auth::logout();
            return redirect()->route('empresa.login')
                ->withErrors('No tienes una empresa vinculada a tu cuenta.');
        }

        if ($empresa->estado === 'pendiente') {
            return view('pages.empresa.pendiente', compact('empresa', 'user'));
        }

        if ($empresa->estado === 'suspendida') {
            return view('pages.empresa.suspendida', compact('empresa', 'user'));
        }

        return null;
    }

    public function dashboard()
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $user = auth()->user();
        $empresa = $this->empresaForUser((int) $user->id);
        $empresaId = (int) $empresa->id;
        app(WalletLedgerService::class)->ensureCuenta('empresa', $empresaId);

        $conductorIds = DB::table('conductores')
            ->where('empresa_id', $empresaId)
            ->pluck('id');

        $stats = [
            'total_taxis'    => $conductorIds->count(),
            'activos'        => DB::table('conductores')
                ->where('empresa_id', $empresaId)
                ->where('estado_operitivo', 1)
                ->count(),
            'disponibles'    => DB::table('conductores')
                ->where('empresa_id', $empresaId)
                ->where('estado_operitivo', 1)
                ->where('disponible', 1)
                ->count(),
            'viajes_hoy'     => 0,
            'viajes_mes'     => 0,
            'ingresos_mes'   => 0,
            'wallet_total'   => 0,
        ];

        if ($conductorIds->isNotEmpty()) {
            $today = now()->toDateString();
            $monthStart = now()->startOfMonth()->toDateTimeString();

            $stats['viajes_hoy'] = DB::table('viajes')
                ->whereIn('conductor_id', $conductorIds)
                ->whereDate('created_at', $today)
                ->where('estado', 'terminado')
                ->count();

            $stats['viajes_mes'] = DB::table('viajes')
                ->whereIn('conductor_id', $conductorIds)
                ->where('created_at', '>=', $monthStart)
                ->where('estado', 'terminado')
                ->count();

            $stats['ingresos_mes'] = (float) DB::table('viajes')
                ->whereIn('conductor_id', $conductorIds)
                ->where('created_at', '>=', $monthStart)
                ->where('estado', 'terminado')
                ->sum('tarifa_aplicada');

            $stats['wallet_total'] = (float) DB::table('wallet_saldos')
                ->whereIn('conductor_id', $conductorIds)
                ->sum('saldo_actual');
        }

        $flotaReciente = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->where('c.empresa_id', $empresaId)
            ->selectRaw('c.id, c.disponible, c.estado_operitivo, u.name as nombre, v.placa, v.marca, v.linea')
            ->orderByDesc('c.id')
            ->limit(5)
            ->get();

        return view('pages.empresa.dashboard', compact('empresa', 'user', 'stats', 'flotaReciente'));
    }

    public function flota()
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $empresa = $this->empresaForUser((int) auth()->id());
        $items = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->leftJoin('wallet_cuentas as wc', function ($join) {
                $join->on('wc.conductor_id', '=', 'c.id')->where('wc.tipo', 'conductor');
            })
            ->leftJoin('wallet_saldos as w', 'w.conductor_id', '=', 'c.id')
            ->where('c.empresa_id', $empresa->id)
            ->selectRaw('c.id, c.disponible, c.estado_operitivo, c.total_viajes, u.name as nombre, u.telefono, u.email, v.placa, v.marca, v.linea, v.color, COALESCE(wc.saldo_actual, w.saldo_actual, 0) as saldo')
            ->orderByDesc('c.id')
            ->get();

        return view('pages.empresa.flota', compact('empresa', 'items'));
    }

    public function flotaNuevo()
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $empresa = $this->empresaForUser((int) auth()->id());
        return view('pages.empresa.flota_nuevo', compact('empresa'));
    }

    public function flotaStore(Request $request)
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $empresa = $this->empresaForUser((int) auth()->id());
        $data = $request->validate([
            'nombre'       => 'required|string|max:120',
            'telefono'     => 'required|string|max:20|unique:users,telefono',
            'email'        => 'nullable|email|max:120|unique:users,email',
            'password'     => 'required|string|min:6|max:64',
            'placa'        => 'required|string|max:12|unique:vehiculos,placa',
            'marca'        => 'nullable|string|max:60',
            'linea'        => 'nullable|string|max:60',
            'modelo_anio'  => 'nullable|integer|min:1990|max:2030',
            'color'        => 'nullable|string|max:40',
        ]);

        $now = now()->toDateTimeString();
        $roleConductor = DB::table('roles')->where('role_name', 'Conductor')->value('role_id') ?: 3;

        $firebaseUid = null;
        $email = $data['email'] ?? null;
        if (!$email) {
            $email = preg_replace('/\D+/', '', $data['telefono']) . '@flota.taxpiya.local';
        }

        $firebase = app(FirebaseIdentityService::class);
        if ($firebase->isConfigured() && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $identity = $firebase->signUp($email, $data['password']);
                $firebaseUid = $identity['localId'] ?? null;
            } catch (\Throwable $e) {
                return back()->withErrors('No se pudo crear el usuario en Firebase: ' . $e->getMessage())->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId([
                'name'          => $data['nombre'],
                'email'         => $email,
                'telefono'      => $data['telefono'],
                'password'      => bcrypt($data['password']),
                'firebase_uid'  => $firebaseUid,
                'estado'        => 1,
                'user_role_id'  => $roleConductor,
            ]);

            $conductorId = DB::table('conductores')->insertGetId([
                'user_id'             => $userId,
                'empresa_id'          => $empresa->id,
                'estado_operitivo'    => 1,
                'disponible'          => 0,
                'total_viajes'        => 0,
                'verificacion_estado' => 'verificado',
                'verificacion_nivel'  => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            DB::table('vehiculos')->insert([
                'conductor_id'        => $conductorId,
                'placa'               => strtoupper($data['placa']),
                'marca'               => $data['marca'] ?? null,
                'linea'               => $data['linea'] ?? null,
                'modelo_anio'         => $data['modelo_anio'] ?? null,
                'color'               => $data['color'] ?? 'Amarillo',
                'categoria'           => 'taxi',
                'asientos'            => 4,
                'estado_vehiculo'     => 'activo',
                'verificacion_estado' => 'verificado',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            app(WalletService::class)->ensureSaldoRow((int) $conductorId);

            app(WalletLedgerService::class)->ensureCuenta('conductor', (int) $conductorId);
            app(WalletLedgerService::class)->syncConductorPermissions((int) $conductorId);

            $userModel = Users::find($userId);
            if ($userModel) {
                app(ReferralService::class)->ensureUserCode($userModel);
                try {
                    app(FirestoreUserService::class)->upsertFromUser($userModel, 'conductor');
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('No se pudo registrar el taxi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('empresa.flota')->with('flota_ok', 'Taxi registrado correctamente.');
    }

    public function viajes()
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $empresa = $this->empresaForUser((int) auth()->id());
        $conductorIds = DB::table('conductores')
            ->where('empresa_id', $empresa->id)
            ->pluck('id');

        $viajes = collect();
        if ($conductorIds->isNotEmpty()) {
            $viajes = DB::table('viajes as v')
                ->join('users as p', 'p.id', '=', 'v.pasajero_id')
                ->join('conductores as c', 'c.id', '=', 'v.conductor_id')
                ->join('users as u', 'u.id', '=', 'c.user_id')
                ->leftJoin('vehiculos as veh', 'veh.conductor_id', '=', 'c.id')
                ->whereIn('v.conductor_id', $conductorIds)
                ->selectRaw('v.*, p.name as pasajero_nombre, u.name as conductor_nombre, veh.placa')
                ->orderByDesc('v.id')
                ->limit(80)
                ->get();
        }

        return view('pages.empresa.viajes', compact('empresa', 'viajes'));
    }

    public function cuenta()
    {
        if ($gate = $this->requireEmpresa()) {
            return $gate;
        }

        $user = auth()->user();
        $empresa = $this->empresaForUser((int) $user->id);
        app(ReferralService::class)->processPendingBonusesForReferrerUser((int) $user->id);
        $referral = app(ReferralService::class)->statsForEmpresa((int) $empresa->id);
        $referralShareUrl = url('/empresa/afiliarse?ref=' . urlencode($referral['codigo'] ?? ''));
        return view('pages.empresa.cuenta', compact('empresa', 'user', 'referral', 'referralShareUrl'));
    }

    public function afiliarse()
    {
        return view('pages.empresa.afiliarse');
    }

    public function afiliarseStore(Request $request)
    {
        $data = $request->validate([
            'nombre_comercial' => 'required|string|max:160',
            'razon_social'     => 'nullable|string|max:160',
            'nit'              => 'nullable|string|max:32',
            'ciudad'           => 'required|string|max:80',
            'direccion'        => 'nullable|string|max:255',
            'contacto_nombre'  => 'required|string|max:120',
            'telefono'         => 'required|string|max:20|unique:users,telefono',
            'email'            => 'required|email|max:120|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',
            'acepta'           => 'accepted',
            'codigo_referido'  => 'nullable|string|max:20',
        ]);

        $referrals = app(ReferralService::class);
        if ($referrals->normalizeCode($data['codigo_referido'] ?? null)) {
            $check = $referrals->validateCode($data['codigo_referido']);
            if (!$check['ok']) {
                return back()->withErrors($check['message'] ?? 'Código de referido inválido')->withInput();
            }
        }

        $roleEmpresa = DB::table('roles')->where('role_name', 'Empresa')->value('role_id') ?: 4;
        $now = now()->toDateTimeString();

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId([
                'name'         => $data['contacto_nombre'],
                'email'        => $data['email'],
                'telefono'     => $data['telefono'],
                'password'     => bcrypt($data['password']),
                'estado'       => 0,
                'user_role_id' => $roleEmpresa,
            ]);

            $empresaRow = [
                'user_id'             => $userId,
                'nombre_comercial'    => $data['nombre_comercial'],
                'razon_social'        => $data['razon_social'] ?? $data['nombre_comercial'],
                'nit'                 => $data['nit'] ?? null,
                'telefono'            => $data['telefono'],
                'email'               => $data['email'],
                'ciudad'              => $data['ciudad'],
                'direccion'           => $data['direccion'] ?? null,
                'estado'              => 'pendiente',
                'verificacion_estado' => 'pendiente',
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('empresas', 'notas')) {
                $empresaRow['notas'] = 'Solicitud web. Enviar documentación a ' . config('taxpiya.registration.docs_email');
            }
            $empresaId = DB::table('empresas')->insertGetId($empresaRow);

            $referrals->ensureUserCode((int) $userId);
            $referrals->ensureEmpresaCode((int) $empresaId);
            $referrals->registerReferral($data['codigo_referido'] ?? null, (int) $userId, 'empresa');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('No se pudo enviar la solicitud: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('empresa.afiliarse.ok');
    }

    public function afiliarseOk()
    {
        return view('pages.empresa.afiliarse_ok');
    }
}
