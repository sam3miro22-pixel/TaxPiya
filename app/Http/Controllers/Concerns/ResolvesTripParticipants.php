<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\DB;

trait ResolvesTripParticipants
{
    /**
     * @return array{0: bool, 1: bool} [isPasajero, isConductor]
     */
    protected function resolveTripRoles(object $viaje, ?int $userId): array
    {
        if (!$userId) {
            return [false, false];
        }

        $isPasajero = isset($viaje->pasajero_id) && (int) $viaje->pasajero_id === $userId;

        $isConductor = false;
        if (!empty($viaje->conductor_id)) {
            $drv = DB::table('conductores')->where('user_id', $userId)->first();
            $isConductor = $drv && (int) $drv->id === (int) $viaje->conductor_id;
        }

        return [$isPasajero, $isConductor];
    }

    protected function userCanAccessTrip(object $viaje, ?int $userId): bool
    {
        [$isPasajero, $isConductor] = $this->resolveTripRoles($viaje, $userId);

        return $isPasajero || $isConductor;
    }
}
