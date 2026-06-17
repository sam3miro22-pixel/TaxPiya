<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReferralService;
use App\Services\EmpresaContabilidadService;

class EmpresasController extends Controller
{
    public function index(Request $request, $fieldname = null, $fieldvalue = null)
    {
        $query = DB::table('empresas as e')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->select(
                'e.id',
                'e.nombre_comercial',
                'e.ciudad',
                'e.estado',
                'e.verificacion_estado',
                'e.telefono',
                'e.email',
                'e.created_at',
                'u.name as contacto',
                'u.email as user_email'
            );

        if ($request->search) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('e.nombre_comercial', 'like', $search)
                    ->orWhere('e.nit', 'like', $search)
                    ->orWhere('u.name', 'like', $search)
                    ->orWhere('u.email', 'like', $search);
            });
        }

        if ($fieldname && $fieldvalue !== null && $fieldvalue !== '') {
            $query->where('e.' . $fieldname, $fieldvalue);
        }

        $limit = (int) ($request->limit ?? 15);
        $records = $query->orderByDesc('e.id')->paginate($limit);

        return $this->renderView('pages.empresas.list', compact('records', 'fieldname', 'fieldvalue'));
    }

    public function view($rec_id)
    {
        $record = $this->findEmpresa((int) $rec_id);
        if (!$record) {
            return $this->reject('Empresa no encontrada', 404);
        }

        $record->flota_count = DB::table('conductores')->where('empresa_id', $record->id)->count();
        $record->viajes_count = 0;
        $conductorIds = DB::table('conductores')->where('empresa_id', $record->id)->pluck('id');
        if ($conductorIds->isNotEmpty()) {
            $record->viajes_count = DB::table('viajes')->whereIn('conductor_id', $conductorIds)->count();
        }

        $contabilidad = app(EmpresaContabilidadService::class)->resumen((int) $record->id);
        $movimientos = app(EmpresaContabilidadService::class)->movimientosRecientes((int) $record->id, 15);

        return $this->renderView('pages.empresas.view', [
            'data' => $record,
            'rec_id' => (int) $rec_id,
            'contabilidad' => $contabilidad,
            'movimientos' => $movimientos,
        ]);
    }

    public function edit(Request $request, $rec_id)
    {
        $record = $this->findEmpresa((int) $rec_id);
        if (!$record) {
            return $this->reject('Empresa no encontrada', 404);
        }

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'estado'              => 'required|in:pendiente,activa,suspendida,rechazada',
                'verificacion_estado' => 'required|in:pendiente,verificado,rechazado',
                'notas'               => 'nullable|string|max:2000',
            ]);

            DB::table('empresas')->where('id', $record->id)->update([
                'estado'              => $data['estado'],
                'verificacion_estado' => $data['verificacion_estado'],
                'notas'               => $data['notas'] ?? null,
                'updated_at'          => now()->toDateTimeString(),
            ]);

            if ($data['estado'] === 'activa') {
                DB::table('users')->where('id', $record->user_id)->update(['estado' => 1]);
                app(ReferralService::class)->activateReferral((int) $record->user_id, 'empresa');
            } elseif (in_array($data['estado'], ['suspendida', 'rechazada'], true)) {
                DB::table('users')->where('id', $record->user_id)->update(['estado' => 2]);
            }

            return $this->redirect('empresas/view/' . $record->id, 'Empresa actualizada');
        }

        return $this->renderView('pages.empresas.edit', ['data' => $record, 'rec_id' => (int) $rec_id]);
    }

    private function findEmpresa(int $id): ?object
    {
        return DB::table('empresas as e')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->select('e.*', 'u.name as contacto', 'u.email as user_email', 'u.telefono as user_telefono', 'u.estado as user_estado')
            ->where('e.id', $id)
            ->first();
    }
}
