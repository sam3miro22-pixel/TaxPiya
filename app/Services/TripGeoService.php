<?php

namespace App\Services;

class TripGeoService
{
    public const ARRIVAL_RADIUS_METERS = 250;

    public static function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function distanceToPickup(object $viaje, ?float $lat, ?float $lng): ?float
    {
        if ($lat === null || $lng === null) {
            return null;
        }
        if (!isset($viaje->origen_lat, $viaje->origen_lng)) {
            return null;
        }

        return self::haversineMeters(
            (float) $viaje->origen_lat,
            (float) $viaje->origen_lng,
            $lat,
            $lng
        );
    }

    public function isNearPickup(object $viaje, ?float $lat, ?float $lng, float $maxMeters = self::ARRIVAL_RADIUS_METERS): bool
    {
        $dist = $this->distanceToPickup($viaje, $lat, $lng);

        return $dist !== null && $dist <= $maxMeters;
    }

    public function canConfirmArrival(object $viaje, ?float $lat, ?float $lng): bool
    {
        return $this->isNearPickup($viaje, $lat, $lng);
    }
}
