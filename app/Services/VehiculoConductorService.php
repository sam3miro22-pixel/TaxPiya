<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VehiculoConductorService
{
    public function resolveVehiculoId(int $conductorId): ?int
    {
        if (Schema::hasTable('vehiculo_conductores')) {
            $fromJunction = DB::table('vehiculo_conductores')
                ->where('conductor_id', $conductorId)
                ->where('activo', 1)
                ->orderByDesc('es_titular')
                ->value('vehiculo_id');

            if ($fromJunction) {
                return (int) $fromJunction;
            }
        }

        $legacy = DB::table('vehiculos')->where('conductor_id', $conductorId)->value('id');

        return $legacy ? (int) $legacy : null;
    }

    public function resolveVehiculo(int $conductorId): ?object
    {
        $vehiculoId = $this->resolveVehiculoId($conductorId);
        if (!$vehiculoId) {
            return null;
        }

        return DB::table('vehiculos')->where('id', $vehiculoId)->first();
    }

    public function assignConductor(int $vehiculoId, int $conductorId, bool $esTitular = false): void
    {
        if (!Schema::hasTable('vehiculo_conductores')) {
            return;
        }

        $now = now()->toDateTimeString();
        $exists = DB::table('vehiculo_conductores')
            ->where('vehiculo_id', $vehiculoId)
            ->where('conductor_id', $conductorId)
            ->exists();

        if ($exists) {
            DB::table('vehiculo_conductores')
                ->where('vehiculo_id', $vehiculoId)
                ->where('conductor_id', $conductorId)
                ->update([
                    'activo'     => 1,
                    'es_titular' => $esTitular ? 1 : 0,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('vehiculo_conductores')->insert([
                'vehiculo_id'   => $vehiculoId,
                'conductor_id'  => $conductorId,
                'activo'        => 1,
                'es_titular'    => $esTitular ? 1 : 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        if ($esTitular) {
            DB::table('vehiculos')
                ->where('id', $vehiculoId)
                ->update(['conductor_id' => $conductorId, 'updated_at' => $now]);
        }
    }

    public function conductoresForVehiculo(int $vehiculoId): array
    {
        if (!Schema::hasTable('vehiculo_conductores')) {
            $titular = DB::table('vehiculos')->where('id', $vehiculoId)->value('conductor_id');
            if (!$titular) {
                return [];
            }

            return [(int) $titular];
        }

        return DB::table('vehiculo_conductores')
            ->where('vehiculo_id', $vehiculoId)
            ->where('activo', 1)
            ->pluck('conductor_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function backfillFromLegacy(): void
    {
        if (!Schema::hasTable('vehiculo_conductores')) {
            return;
        }

        $vehiculos = DB::table('vehiculos')->whereNotNull('conductor_id')->get(['id', 'conductor_id']);
        $now = now()->toDateTimeString();

        foreach ($vehiculos as $v) {
            $exists = DB::table('vehiculo_conductores')
                ->where('vehiculo_id', $v->id)
                ->where('conductor_id', $v->conductor_id)
                ->exists();

            if (!$exists) {
                DB::table('vehiculo_conductores')->insert([
                    'vehiculo_id'   => (int) $v->id,
                    'conductor_id'  => (int) $v->conductor_id,
                    'activo'        => 1,
                    'es_titular'    => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }
}
