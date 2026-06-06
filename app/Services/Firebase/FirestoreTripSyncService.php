<?php

namespace App\Services\Firebase;

/**
 * Espeja viajes en Firestore para tiempo real (cliente puede escuchar cambios).
 * MySQL/SQLite sigue siendo la fuente principal en Laravel.
 */
class FirestoreTripSyncService
{
    public function isEnabled(): bool
    {
        return (bool) config('taxpiya.firebase.use_firestore')
            && is_readable(config('firebase.credentials'));
    }

    public function syncTrip(int $viajeId, array $extra = []): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $trip = \Illuminate\Support\Facades\DB::table('viajes')->where('id', $viajeId)->first();
        if (!$trip) {
            return;
        }

        try {
            $firestore = (new \Kreait\Firebase\Factory())
                ->withServiceAccount(config('firebase.credentials'))
                ->createFirestore();

            $pasajeroUid = null;
            $conductorUid = null;
            if (!empty($trip->pasajero_id)) {
                $pasajeroUid = \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $trip->pasajero_id)->value('firebase_uid');
            }
            if (!empty($trip->conductor_id)) {
                $uid = \Illuminate\Support\Facades\DB::table('conductores as c')
                    ->join('users as u', 'u.id', '=', 'c.user_id')
                    ->where('c.id', $trip->conductor_id)
                    ->value('u.firebase_uid');
                $conductorUid = $uid;
            }

            $payload = array_merge([
                'mysql_id'       => (int) $trip->id,
                'pasajero_id'    => (int) $trip->pasajero_id,
                'conductor_id'   => $trip->conductor_id ? (int) $trip->conductor_id : null,
                'pasajero_uid'   => $pasajeroUid,
                'conductor_uid'  => $conductorUid,
                'estado'         => (string) $trip->estado,
                'origen_lat'     => $trip->origen_lat,
                'origen_lng'     => $trip->origen_lng,
                'origen_texto'   => $trip->origen_texto,
                'destino_lat'    => $trip->destino_lat,
                'destino_lng'    => $trip->destino_lng,
                'destino_texto'  => $trip->destino_texto,
                'updated_at'     => now()->toIso8601String(),
            ], $extra);

            $firestore->database()
                ->collection('viajes')
                ->document((string) $viajeId)
                ->set($payload, ['merge' => true]);
        } catch (\Throwable $e) {
            \Log::warning('Firestore trip sync falló', ['viaje_id' => $viajeId, 'err' => $e->getMessage()]);
        }
    }
}
