<?php

namespace App\Support;

final class ColombiaGeo
{
    /** @var array{minLat: float, maxLat: float, minLng: float, maxLng: float} */
    private const BOUNDS = [
        'minLat' => -4.30,
        'maxLat' => 13.50,
        'minLng' => -82.00,
        'maxLng' => -66.80,
    ];

    public static function contains(float $lat, float $lng): bool
    {
        return $lat >= self::BOUNDS['minLat']
            && $lat <= self::BOUNDS['maxLat']
            && $lng >= self::BOUNDS['minLng']
            && $lng <= self::BOUNDS['maxLng'];
    }

    public static function rejectMessage(): string
    {
        return 'La ubicación debe estar dentro de Colombia.';
    }
}
