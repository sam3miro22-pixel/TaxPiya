<?php

namespace App\Services\Firebase;

use App\Contracts\TripRepositoryInterface;

/**
 * Repositorio Firestore — implementar durante la migración a Firebase.
 * Mientras TAXPIYA_USE_FIRESTORE=false, ViajesController sigue usando MySQL.
 */
class FirestoreTripRepository implements TripRepositoryInterface
{
    public function createTrip(array $data): int
    {
        throw new \RuntimeException('Firestore no está activo. Configure TAXPIYA_USE_FIRESTORE=true y el SDK de Firebase.');
    }

    public function findTrip(int $viajeId): ?object
    {
        throw new \RuntimeException('Firestore no está activo.');
    }

    public function updateTripState(int $viajeId, string $estado, array $extra = []): bool
    {
        throw new \RuntimeException('Firestore no está activo.');
    }
}
