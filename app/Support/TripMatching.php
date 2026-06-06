<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TripMatching
{
    public static function driverPositionCutoff(): string
    {
        $mins = max(1, (int) config('taxpiya.driver_position_ttl_minutes', 5));

        return now()->subMinutes($mins)->format('Y-m-d H:i:s');
    }

    public static function tripSearchCutoff(): string
    {
        $mins = max(1, (int) config('taxpiya.trip_search_ttl_minutes', 15));

        return now()->subMinutes($mins)->format('Y-m-d H:i:s');
    }

    public static function expireStaleSearchingTrips(): int
    {
        if (!Schema::hasTable('viajes')) {
            return 0;
        }

        $cutoff = self::tripSearchCutoff();
        $now    = now()->format('Y-m-d H:i:s');

        $staleIds = DB::table('viajes')
            ->where('estado', 'buscando')
            ->whereNull('conductor_id')
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        if ($staleIds->isEmpty()) {
            return 0;
        }

        DB::table('viajes')
            ->whereIn('id', $staleIds)
            ->update([
                'estado'             => 'cancelado_sistema',
                'cancelado_at'       => $now,
                'cancelado_por'      => 'sistema',
                'cancelacion_motivo' => 'Solicitud expirada por tiempo',
                'updated_at'         => $now,
            ]);

        if (Schema::hasTable('viaje_estados_log')) {
            foreach ($staleIds as $viajeId) {
                DB::table('viaje_estados_log')->insert([
                    'viaje_id'      => (int) $viajeId,
                    'from_estado'   => 'buscando',
                    'to_estado'     => 'cancelado_sistema',
                    'actor_tipo'    => 'sistema',
                    'actor_id'      => null,
                    'motivo_codigo' => 'expirado',
                    'motivo_texto'  => 'Solicitud expirada por tiempo',
                    'app_origen'    => 'sistema',
                    'ip'            => null,
                    'created_at'    => $now,
                ]);
            }
        }

        return $staleIds->count();
    }

    public static function cancelPassengerOpenSearches(int $pasajeroId): int
    {
        if (!Schema::hasTable('viajes')) {
            return 0;
        }

        $now = now()->format('Y-m-d H:i:s');

        $openIds = DB::table('viajes')
            ->where('pasajero_id', $pasajeroId)
            ->where('estado', 'buscando')
            ->whereNull('conductor_id')
            ->pluck('id');

        if ($openIds->isEmpty()) {
            return 0;
        }

        DB::table('viajes')
            ->whereIn('id', $openIds)
            ->update([
                'estado'             => 'cancelado_pasajero',
                'cancelado_at'       => $now,
                'cancelado_por'      => 'pasajero',
                'cancelacion_motivo' => 'Nueva solicitud del pasajero',
                'updated_at'         => $now,
            ]);

        return $openIds->count();
    }

    public static function applyFreshDriverPositionFilter($query, string $posAlias = 'p')
    {
        if (Schema::hasColumn('conductor_posicion_actual', 'actualizada_at')) {
            $query->where("{$posAlias}.actualizada_at", '>=', self::driverPositionCutoff());
        }

        return $query;
    }
}
