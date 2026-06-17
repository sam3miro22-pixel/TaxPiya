<?php

namespace App\Http\Controllers;

use App\Services\WalletComprobanteService;
use App\Services\WalletLedgerService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletPortalController extends Controller
{
    public function pasajeroWallet(WalletLedgerService $ledger)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Pasajero')) {
            return redirect()->route('home');
        }

        app(\App\Services\ReferralService::class)->processPendingBonusesForReferrerUser((int) $user->id);

        $cuenta = $ledger->ensureCuenta('pasajero', (int) $user->id);
        $movimientos = $cuenta ? $ledger->getMovimientos((int) $cuenta->id) : [];
        $solicitudes = $cuenta ? $ledger->getSolicitudesForCuenta((int) $cuenta->id, null, 15) : [];

        return view('pages.pasajero.wallet', [
            'cuenta'      => $cuenta,
            'movimientos' => $movimientos,
            'solicitudes' => $solicitudes,
        ]);
    }

    public function pasajeroDepositar(Request $request, WalletLedgerService $ledger, WalletComprobanteService $comprobantes)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Pasajero')) {
            return redirect()->route('home');
        }

        $cuenta = $ledger->ensureCuenta('pasajero', (int) $user->id);

        return $this->procesarDepositoNequi($request, $ledger, $comprobantes, $cuenta, (int) $user->id, 'pasajero.wallet');
    }

    public function conductorDepositar(Request $request, WalletLedgerService $ledger, WalletComprobanteService $comprobantes)
    {
        return $this->conductorOperacion($request, $ledger, $comprobantes, 'deposito');
    }

    public function conductorRetirar(Request $request, WalletLedgerService $ledger)
    {
        return $this->conductorOperacion($request, $ledger, null, 'retiro');
    }

    private function conductorOperacion(Request $request, WalletLedgerService $ledger, ?WalletComprobanteService $comprobantes, string $tipo)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Conductor')) {
            return redirect()->route('home');
        }

        $conductor = DB::table('conductores')->where('user_id', $user->id)->first();
        if (!$conductor) {
            return redirect()->route('home');
        }

        $ledger->syncConductorPermissions((int) $conductor->id);
        $cuenta = $ledger->ensureCuenta('conductor', (int) $conductor->id);
        if ($ledger->isConductorFlota($conductor) || (int) $cuenta->solo_lectura) {
            return redirect()->route('conductor.wallet')->with('wallet_error', 'Tu billetera es administrada por la empresa.');
        }

        if ($tipo === 'deposito') {
            return $this->procesarDepositoNequi($request, $ledger, $comprobantes, $cuenta, (int) $user->id, 'conductor.wallet');
        }

        $min = 10000;
        $data = $request->validate(['monto' => "required|numeric|min:{$min}|max:50000000"]);
        $result = $ledger->solicitarRetiro((int) $cuenta->id, (float) $data['monto'], (int) $user->id);
        $msg = $result['ok']
            ? (($result['estado'] ?? '') === 'pendiente'
                ? 'Solicitud de retiro enviada. Un administrador la revisará.'
                : 'Retiro registrado.')
            : ($result['message'] ?? 'Error');

        return redirect()->route('conductor.wallet')
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $msg);
    }

    public function empresaWallet(WalletLedgerService $ledger)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $cuenta = $ledger->ensureCuenta('empresa', (int) $empresa->id);
        $movimientos = $ledger->getMovimientos((int) $cuenta->id);
        $solicitudes = $ledger->getSolicitudesForCuenta((int) $cuenta->id, null, 15);

        return view('pages.empresa.wallet', compact('empresa', 'cuenta', 'movimientos', 'solicitudes'));
    }

    public function empresaDepositar(Request $request, WalletLedgerService $ledger, WalletComprobanteService $comprobantes)
    {
        return $this->empresaOperacion($request, $ledger, $comprobantes, 'deposito');
    }

    public function empresaRetirar(Request $request, WalletLedgerService $ledger)
    {
        return $this->empresaOperacion($request, $ledger, null, 'retiro');
    }

    private function empresaOperacion(Request $request, WalletLedgerService $ledger, ?WalletComprobanteService $comprobantes, string $tipo)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $cuenta = $ledger->ensureCuenta('empresa', (int) $empresa->id);

        if ($tipo === 'deposito') {
            return $this->procesarDepositoNequi($request, $ledger, $comprobantes, $cuenta, (int) auth()->id(), 'empresa.wallet');
        }

        $min = 10000;
        $data = $request->validate(['monto' => "required|numeric|min:{$min}|max:50000000"]);
        $result = $ledger->solicitarRetiro((int) $cuenta->id, (float) $data['monto'], (int) auth()->id());
        $msg = $result['ok']
            ? (($result['estado'] ?? '') === 'pendiente'
                ? 'Solicitud de retiro enviada. Un administrador la revisará.'
                : 'Retiro registrado.')
            : ($result['message'] ?? 'Error');

        return redirect()->route('empresa.wallet')
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $msg);
    }

    private function procesarDepositoNequi(
        Request $request,
        WalletLedgerService $ledger,
        WalletComprobanteService $comprobantes,
        ?object $cuenta,
        int $userId,
        string $redirectRoute
    ) {
        if (!$cuenta || !(int) $cuenta->puede_depositar) {
            return redirect()->route($redirectRoute)->with('wallet_error', 'Depósitos no permitidos en esta cuenta.');
        }

        $data = $request->validate([
            'monto'           => 'required|numeric|min:1000|max:50000000',
            'referencia_pago' => 'required|string|max:64',
            'comprobante'     => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        try {
            $comprobantePath = $comprobantes->store($request->file('comprobante'));
        } catch (\Throwable $e) {
            return redirect()->route($redirectRoute)->with('wallet_error', $e->getMessage());
        }

        $result = $ledger->solicitarDeposito(
            (int) $cuenta->id,
            (float) $data['monto'],
            $userId,
            'nequi',
            [
                'referencia_pago'     => trim($data['referencia_pago']),
                'comprobante_path'    => $comprobantePath,
                'solicitante_user_id' => $userId,
            ]
        );

        if ($result['ok'] ?? false) {
            \App\Services\SqlitePersistenceService::scheduleBackupAfterRequest();
        }

        $msg = $result['ok']
            ? 'Solicitud de recarga NEQUI enviada. Un administrador revisará tu comprobante y acreditará el saldo.'
            : ($result['message'] ?? 'Error al registrar la solicitud.');

        return redirect()->route($redirectRoute)
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $msg);
    }

    public function empresaFlotaWallet(int $conductorId, WalletLedgerService $ledger)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $conductor = DB::table('conductores as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('vehiculos as v', 'v.conductor_id', '=', 'c.id')
            ->where('c.id', $conductorId)
            ->where('c.empresa_id', $empresa->id)
            ->selectRaw('c.*, u.name, u.telefono, u.email, v.placa, v.marca, v.linea')
            ->first();

        if (!$conductor) {
            return redirect()->route('empresa.flota')->with('wallet_error', 'Conductor no encontrado en tu flota.');
        }

        $cuenta = $ledger->ensureCuenta('conductor', (int) $conductor->id);
        $movimientos = $ledger->getMovimientos((int) $cuenta->id, 100);
        $resumen = $ledger->resumenIngresosConductor((int) $conductor->id);

        $viajes = DB::table('viajes')
            ->where('conductor_id', $conductorId)
            ->where('estado', 'terminado')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('pages.empresa.flota_wallet', compact('empresa', 'conductor', 'cuenta', 'movimientos', 'resumen', 'viajes'));
    }

    public function empresaPagarConductor(Request $request, int $conductorId, WalletLedgerService $ledger)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $data = $request->validate([
            'monto' => 'required|numeric|min:1000|max:50000000',
            'nota'  => 'nullable|string|max:255',
        ]);

        $result = $ledger->pagarConductorDesdeEmpresa(
            (int) $empresa->id,
            $conductorId,
            (float) $data['monto'],
            (int) auth()->id(),
            $data['nota'] ?? null
        );

        return redirect()->route('empresa.flota.wallet', $conductorId)
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $result['ok'] ? 'Pago registrado al conductor.' : ($result['message'] ?? 'Error'));
    }

    private function requireEmpresaActiva()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Empresa')) {
            return redirect()->route('empresa.login');
        }

        $empresa = DB::table('empresas')->where('user_id', $user->id)->first();
        if (!$empresa || $empresa->estado !== 'activa') {
            return redirect()->route('empresa.dashboard');
        }

        return $empresa;
    }
}
