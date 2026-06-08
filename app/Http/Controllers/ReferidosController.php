<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferidosController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('referidos as r')
            ->leftJoin('users as ru', 'ru.id', '=', 'r.referrer_user_id')
            ->leftJoin('empresas as e', 'e.id', '=', 'r.referrer_empresa_id')
            ->leftJoin('users as ref', 'ref.id', '=', 'r.referred_user_id')
            ->select(
                'r.*',
                'ru.name as referrer_name',
                'e.nombre_comercial as referrer_empresa',
                'ref.name as referred_name',
                'ref.email as referred_email'
            )
            ->orderByDesc('r.id');

        if ($request->search) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('r.codigo_usado', 'like', $s)
                    ->orWhere('ru.name', 'like', $s)
                    ->orWhere('e.nombre_comercial', 'like', $s)
                    ->orWhere('ref.name', 'like', $s)
                    ->orWhere('ref.email', 'like', $s);
            });
        }

        if ($request->estado) {
            $query->where('r.estado', $request->estado);
        }

        if ($request->tipo) {
            $query->where('r.tipo_referido', $request->tipo);
        }

        $records = $query->paginate((int) ($request->limit ?? 20));

        $stats = [
            'total'   => DB::table('referidos')->count(),
            'activos' => DB::table('referidos')->where('estado', 'activo')->count(),
            'pasajeros' => DB::table('referidos')->where('tipo_referido', 'pasajero')->count(),
            'conductores' => DB::table('referidos')->where('tipo_referido', 'conductor')->count(),
            'empresas' => DB::table('referidos')->where('tipo_referido', 'empresa')->count(),
        ];

        return $this->renderView('pages.referidos.list', compact('records', 'stats'));
    }

    public function validateCode(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);
        $result = app(\App\Services\ReferralService::class)->validateCode($request->input('code'));

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
