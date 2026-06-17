<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmpresaContabilidadService
{
    public function resumen(int $empresaId, ?string $monthStart = null): array
    {
        $monthStart = $monthStart ?? now()->startOfMonth()->toDateTimeString();
        $conductorIds = DB::table('conductores')
            ->where('empresa_id', $empresaId)
            ->pluck('id');

        $stats = [
            'conductores'    => $conductorIds->count(),
            'viajes_mes'     => 0,
            'viajes_hoy'     => 0,
            'ingresos_mes'   => 0.0,
            'wallet_total'   => 0.0,
            'comisiones_mes' => 0.0,
        ];

        if ($conductorIds->isEmpty()) {
            return $stats;
        }

        $today = now()->toDateString();

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

        if (DB::getSchemaBuilder()->hasTable('wallet_movimientos')) {
            $stats['comisiones_mes'] = (float) DB::table('wallet_movimientos')
                ->whereIn('conductor_id', $conductorIds)
                ->where('created_at', '>=', $monthStart)
                ->where('sentido', 'debito')
                ->where('motivo', 'like', '%comision%')
                ->sum('monto');
        }

        return $stats;
    }

    public function movimientosRecientes(int $empresaId, int $limit = 50): Collection
    {
        $conductorIds = DB::table('conductores')
            ->where('empresa_id', $empresaId)
            ->pluck('id');

        if ($conductorIds->isEmpty() || !DB::getSchemaBuilder()->hasTable('wallet_movimientos')) {
            return collect();
        }

        return DB::table('wallet_movimientos as wm')
            ->join('conductores as c', 'c.id', '=', 'wm.conductor_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->whereIn('wm.conductor_id', $conductorIds)
            ->selectRaw('wm.*, u.name as conductor_nombre, c.id as conductor_id')
            ->orderByDesc('wm.id')
            ->limit($limit)
            ->get();
    }

    public function viajesRecientes(int $empresaId, int $limit = 30): Collection
    {
        $conductorIds = DB::table('conductores')
            ->where('empresa_id', $empresaId)
            ->pluck('id');

        if ($conductorIds->isEmpty()) {
            return collect();
        }

        return DB::table('viajes as v')
            ->join('users as p', 'p.id', '=', 'v.pasajero_id')
            ->join('conductores as c', 'c.id', '=', 'v.conductor_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->whereIn('v.conductor_id', $conductorIds)
            ->selectRaw('v.id, v.estado, v.tarifa_aplicada, v.moneda, v.created_at, p.name as pasajero, u.name as conductor')
            ->orderByDesc('v.id')
            ->limit($limit)
            ->get();
    }
}
