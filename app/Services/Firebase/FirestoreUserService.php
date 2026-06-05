<?php

namespace App\Services\Firebase;

use App\Models\Users;

/**
 * Sincroniza perfiles de usuario a Firestore (fase de migración).
 */
class FirestoreUserService
{
    public function isEnabled(): bool
    {
        return (bool) config('taxpiya.firebase.use_firestore')
            && is_readable(config('firebase.credentials'));
    }

    public function upsertFromUser(Users $user, string $role = 'pasajero'): void
    {
        if (!$this->isEnabled() || empty($user->firebase_uid)) {
            return;
        }

        if (!class_exists(\Kreait\Firebase\Factory::class)) {
            return;
        }

        try {
            $firestore = (new \Kreait\Firebase\Factory())
                ->withServiceAccount(config('firebase.credentials'))
                ->createFirestore();

            $doc = $firestore->database()->collection('users')->document($user->firebase_uid);
            $doc->set([
                'mysql_id'  => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'telefono'  => $user->telefono,
                'role'      => $role,
                'estado'    => (int) ($user->estado ?? 1),
                'updated_at'=> now()->toIso8601String(),
            ], ['merge' => true]);
        } catch (\Throwable $e) {
            \Log::warning('Firestore upsert user falló', [
                'user_id' => $user->id,
                'err'     => $e->getMessage(),
            ]);
        }
    }
}
