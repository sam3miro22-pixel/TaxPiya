<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class DatabaseGeometry
{
    public static function pointRaw(float $lng, float $lat): mixed
    {
        if (config('database.default') === 'sqlite') {
            return null;
        }

        return DB::raw("ST_GeomFromText('POINT($lng $lat)')");
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function stripNullGeometry(array $data): array
    {
        return array_filter($data, static fn ($v) => $v !== null);
    }
}
