<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class ActiveTripResolver
{
    private const TERMINAL = [
        'terminado',
        'cancelado',
        'cancelado_sistema',
        'cancelado_pasajero',
        'cancelado_conductor',
        'no_show',
        'fallo_localizacion',
    ];

    public static function forPassenger(int $userId): ?object
    {
        return DB::table('viajes')
            ->where('pasajero_id', $userId)
            ->whereNotIn('estado', self::TERMINAL)
            ->orderByDesc('id')
            ->first();
    }

    public static function forConductor(int $userId): ?object
    {
        $conductor = DB::table('conductores')->where('user_id', $userId)->first();
        if (!$conductor) {
            return null;
        }

        return DB::table('viajes')
            ->where('conductor_id', $conductor->id)
            ->whereNotIn('estado', self::TERMINAL)
            ->orderByDesc('id')
            ->first();
    }

    /** @return array<string, mixed>|null */
    public static function bootstrapPayload(?object $viaje): ?array
    {
        if (!$viaje) {
            return null;
        }

        return [
            'id'              => (int) $viaje->id,
            'estado'          => (string) $viaje->estado,
            'tarifa_aplicada' => (float) ($viaje->tarifa_aplicada ?? 0),
            'origen_texto'    => $viaje->origen_texto ?? '',
            'destino_texto'   => $viaje->destino_texto ?? '',
            'o_lat'           => $viaje->origen_lat ?? null,
            'o_lng'           => $viaje->origen_lng ?? null,
            'd_lat'           => $viaje->destino_lat ?? null,
            'd_lng'           => $viaje->destino_lng ?? null,
            'conductor_id'    => $viaje->conductor_id ?? null,
        ];
    }
}
