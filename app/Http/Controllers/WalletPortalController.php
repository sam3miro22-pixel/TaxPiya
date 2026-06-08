<?php

namespace App\Http\Controllers;

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

        $cuenta = $ledger->ensureCuenta('pasajero', (int) $user->id);
        $movimientos = $cuenta ? $ledger->getMovimientos((int) $cuenta->id) : [];

        return view('pages.pasajero.wallet', [
            'cuenta'      => $cuenta,
            'movimientos' => $movimientos,
        ]);
    }

    public function pasajeroDepositar(Request $request, WalletLedgerService $ledger)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('Pasajero')) {
            return redirect()->route('home');
        }

        $data = $request->validate(['monto' => 'required|numeric|min:1000|max:50000000']);
        $cuenta = $ledger->ensureCuenta('pasajero', (int) $user->id);
        $result = $ledger->solicitarDeposito((int) $cuenta->id, (float) $data['monto'], (int) $user->id);

        return redirect()->route('pasajero.wallet')
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $result['ok'] ? 'Depósito registrado correctamente.' : ($result['message'] ?? 'Error'));
    }

    public function conductorDepositar(Request $request, WalletLedgerService $ledger)
    {
        return $this->conductorOperacion($request, $ledger, 'deposito');
    }

    public function conductorRetirar(Request $request, WalletLedgerService $ledger)
    {
        return $this->conductorOperacion($request, $ledger, 'retiro');
    }

    private function conductorOperacion(Request $request, WalletLedgerService $ledger, string $tipo)
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

        $min = $tipo === 'retiro' ? 10000 : 1000;
        $data = $request->validate(['monto' => "required|numeric|min:{$min}|max:50000000"]);

        $result = $tipo === 'deposito'
            ? $ledger->solicitarDeposito((int) $cuenta->id, (float) $data['monto'], (int) $user->id)
            : $ledger->solicitarRetiro((int) $cuenta->id, (float) $data['monto'], (int) $user->id);

        return redirect()->route('conductor.wallet')
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $result['ok'] ? ucfirst($tipo) . ' registrado.' : ($result['message'] ?? 'Error'));
    }

    public function empresaWallet(WalletLedgerService $ledger)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $cuenta = $ledger->ensureCuenta('empresa', (int) $empresa->id);
        $movimientos = $ledger->getMovimientos((int) $cuenta->id);

        return view('pages.empresa.wallet', compact('empresa', 'cuenta', 'movimientos'));
    }

    public function empresaDepositar(Request $request, WalletLedgerService $ledger)
    {
        return $this->empresaOperacion($request, $ledger, 'deposito');
    }

    public function empresaRetirar(Request $request, WalletLedgerService $ledger)
    {
        return $this->empresaOperacion($request, $ledger, 'retiro');
    }

    private function empresaOperacion(Request $request, WalletLedgerService $ledger, string $tipo)
    {
        $empresa = $this->requireEmpresaActiva();
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }

        $min = $tipo === 'retiro' ? 10000 : 1000;
        $data = $request->validate(['monto' => "required|numeric|min:{$min}|max:50000000"]);
        $cuenta = $ledger->ensureCuenta('empresa', (int) $empresa->id);

        $result = $tipo === 'deposito'
            ? $ledger->solicitarDeposito((int) $cuenta->id, (float) $data['monto'], (int) auth()->id())
            : $ledger->solicitarRetiro((int) $cuenta->id, (float) $data['monto'], (int) auth()->id());

        return redirect()->route('empresa.wallet')
            ->with($result['ok'] ? 'wallet_ok' : 'wallet_error', $result['ok'] ? ucfirst($tipo) . ' registrado.' : ($result['message'] ?? 'Error'));
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
