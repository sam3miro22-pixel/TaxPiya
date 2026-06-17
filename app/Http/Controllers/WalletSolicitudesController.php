<?php

namespace App\Http\Controllers;

use App\Services\WalletComprobanteService;
use App\Services\WalletLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletSolicitudesController extends Controller
{
    public function index(Request $request)
    {
        if (!DB::getSchemaBuilder()->hasTable('wallet_solicitudes')) {
            return $this->renderView('pages.walletsolicitudes.list', [
                'records' => collect(),
                'stats'   => ['pendientes' => 0, 'completadas' => 0, 'rechazadas' => 0],
            ]);
        }

        $query = DB::table('wallet_solicitudes as s')
            ->leftJoin('wallet_cuentas as c', 'c.id', '=', 's.cuenta_id')
            ->leftJoin('users as u', 'u.id', '=', 's.solicitante_user_id')
            ->leftJoin('users as up', 'up.id', '=', 's.procesado_por')
            ->select(
                's.*',
                'c.tipo as cuenta_tipo',
                'c.user_id',
                'c.conductor_id',
                'c.empresa_id',
                'u.name as solicitante_nombre',
                'u.email as solicitante_email',
                'up.name as procesado_nombre'
            )
            ->orderByDesc('s.id');

        if ($request->has('estado')) {
            if ($request->estado !== '') {
                $query->where('s.estado', $request->estado);
            }
        } else {
            $query->where('s.estado', 'pendiente');
        }

        if ($request->operacion) {
            $query->where('s.operacion', $request->operacion);
        }

        if ($request->search) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('s.referencia_pago', 'like', $s)
                    ->orWhere('u.name', 'like', $s)
                    ->orWhere('u.email', 'like', $s)
                    ->orWhere('u.telefono', 'like', $s);
            });
        }

        $records = $query->paginate((int) ($request->limit ?? 20));
        $ledger = app(WalletLedgerService::class);

        foreach ($records as $row) {
            $cuenta = DB::table('wallet_cuentas')->where('id', $row->cuenta_id)->first();
            $row->titular = $ledger->resolveCuentaTitular($cuenta);
        }

        $stats = [
            'pendientes'  => DB::table('wallet_solicitudes')->where('estado', 'pendiente')->count(),
            'completadas' => DB::table('wallet_solicitudes')->where('estado', 'completado')->count(),
            'rechazadas'  => DB::table('wallet_solicitudes')->where('estado', 'rechazada')->count(),
        ];

        return $this->renderView('pages.walletsolicitudes.list', compact('records', 'stats'));
    }

    public function view(int $rec_id, WalletComprobanteService $comprobantes)
    {
        $record = $this->findSolicitud($rec_id);
        if (!$record) {
            return $this->reject('Solicitud no encontrada', 404);
        }

        $cuenta = DB::table('wallet_cuentas')->where('id', $record->cuenta_id)->first();
        $ledger = app(WalletLedgerService::class);
        $record->titular = $ledger->resolveCuentaTitular($cuenta);
        $record->comprobante_url = $comprobantes->publicUrl($record->comprobante_path ?? null);

        if ($record->solicitante_user_id) {
            $record->solicitante = DB::table('users')->where('id', $record->solicitante_user_id)->first();
        }

        return $this->renderView('pages.walletsolicitudes.view', [
            'data'    => $record,
            'rec_id'  => $rec_id,
        ]);
    }

    public function aprobar(int $rec_id, WalletLedgerService $ledger)
    {
        $record = $this->findSolicitud($rec_id);
        if (!$record) {
            return redirect()->route('walletsolicitudes')->with('error', 'Solicitud no encontrada');
        }

        if ($record->estado === 'completado') {
            return redirect()->route('walletsolicitudes.view', $rec_id)->with('error', 'Esta solicitud ya fue acreditada.');
        }

        if ($record->estado === 'rechazada') {
            return redirect()->route('walletsolicitudes.view', $rec_id)->with('error', 'Esta solicitud fue rechazada.');
        }

        try {
            $movId = $ledger->aplicarSolicitud($rec_id, (int) auth()->id());
            if (!$movId) {
                return redirect()->route('walletsolicitudes.view', $rec_id)->with('error', 'No se pudo aplicar la solicitud.');
            }
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('walletsolicitudes.view', $rec_id)->with('error', 'Error al acreditar: ' . $e->getMessage());
        }

        return redirect()->route('walletsolicitudes.view', $rec_id)->with('success', 'Recarga aprobada y saldo acreditado.');
    }

    public function rechazar(Request $request, int $rec_id, WalletLedgerService $ledger)
    {
        $record = $this->findSolicitud($rec_id);
        if (!$record) {
            return redirect()->route('walletsolicitudes')->with('error', 'Solicitud no encontrada');
        }

        $data = $request->validate([
            'notas' => 'nullable|string|max:500',
        ]);

        $ok = $ledger->rechazarSolicitud($rec_id, (int) auth()->id(), $data['notas'] ?? 'Comprobante no válido o pago no verificado.');

        return redirect()->route('walletsolicitudes.view', $rec_id)
            ->with($ok ? 'success' : 'error', $ok ? 'Solicitud rechazada.' : 'No se pudo rechazar la solicitud.');
    }

    private function findSolicitud(int $id): ?object
    {
        if (!DB::getSchemaBuilder()->hasTable('wallet_solicitudes')) {
            return null;
        }

        return DB::table('wallet_solicitudes')->where('id', $id)->first();
    }
}
