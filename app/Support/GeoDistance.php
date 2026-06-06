<?php

namespace App\Support;

final class GeoDistance
{
    public static function km(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function usesSqlite(): bool
    {
        return config('database.default') === 'sqlite';
    }

    /**
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    public static function boundingBox(float $lat, float $lng, float $radiusKm): array
    {
        $deltaLat = $radiusKm / 111.0;
        $cosLat = max(cos(deg2rad($lat)), 0.01);
        $deltaLng = $radiusKm / (111.0 * $cosLat);

        return [
            $lat - $deltaLat,
            $lat + $deltaLat,
            $lng - $deltaLng,
            $lng + $deltaLng,
        ];
    }
}
