<?php

namespace App\Contracts;

interface TripRepositoryInterface
{
    public function createTrip(array $data): int;

    public function findTrip(int $viajeId): ?object;

    public function updateTripState(int $viajeId, string $estado, array $extra = []): bool;
}
