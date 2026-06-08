<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Autenticación de portales móviles.
 *
 * Pasajero/Conductor (correo): Firebase Auth → POST auth/firebase/sync → sesión Laravel.
 * Pasajero/Conductor (celular): bcrypt en MySQL (cuentas legacy / demo).
 * Empresa/Admin: bcrypt en MySQL únicamente.
 */
class PortalAuthService
{
    public static function firebasePasajeroConductorEnabled(): bool
    {
        return (bool) config('taxpiya.firebase.use_firebase_auth')
            && (string) config('firebase.web.api_key') !== '';
    }

    public function validateLoginGate($user, ?string $app): ?string
    {
        if ((int) ($user->estado ?? 1) !== 1) {
            return 'Tu cuenta está inactiva. Por favor comunícate con el Equipo de Taxpiya.';
        }

        if ($app === 'conductor') {
            if (!$user->hasRole('Conductor')) {
                return 'Acceso exclusivo para Conductores.';
            }
            $conductor = DB::table('conductores')->where('user_id', $user->id)->first();
            if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
                return 'Tu cuenta de conductor no está activa. Comunícate con el Equipo de Taxpiya.';
            }

            DB::table('conductores')
                ->where('id', (int) $conductor->id)
                ->update([
                    'disponible' => 0,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);
        } elseif ($app === 'pasajero') {
            if (!$user->hasRole('Pasajero')) {
                return 'Este acceso es solo para Pasajeros.';
            }
        } elseif ($app === 'empresa') {
            if (!$user->hasRole('Empresa')) {
                return 'Acceso exclusivo para empresas afiliadas.';
            }
            $empresa = DB::table('empresas')->where('user_id', $user->id)->first();
            if (!$empresa) {
                return 'Tu cuenta no tiene una empresa vinculada.';
            }
        }

        return null;
    }
}
