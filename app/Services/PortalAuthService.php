<?php

namespace App\Services;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Autenticación de portales móviles.
 *
 * Pasajero/Conductor: Firebase Auth obligatorio (Google o correo Firebase).
 * Excepción: cuentas demo con teléfono/contraseña local solo para pruebas E2E automatizadas.
 * Empresa/Admin: bcrypt en SQLite/MySQL.
 * Una sesión Laravel activa por usuario (SessionGuardService).
 */
class PortalAuthService
{
    public static function firebasePasajeroConductorEnabled(): bool
    {
        return (bool) config('taxpiya.firebase.use_firebase_auth')
            && (string) config('firebase.web.api_key') !== '';
    }

    public function roleIdForApp(?string $app): ?int
    {
        $roleName = match ($app) {
            'pasajero'  => 'Pasajero',
            'conductor' => 'Conductor',
            'empresa'   => 'Empresa',
            default     => null,
        };

        if (!$roleName) {
            return null;
        }

        $id = DB::table('roles')->where('role_name', $roleName)->value('role_id');

        return $id !== null ? (int) $id : null;
    }

    public function userMatchesPortal(Users|object $user, ?string $app): bool
    {
        $expected = $this->roleIdForApp($app);
        if ($expected === null) {
            return true;
        }

        return (int) ($user->user_role_id ?? 0) === $expected;
    }

    public function roleMismatchMessage(?string $app): ?string
    {
        return match ($app) {
            'pasajero'  => 'Este acceso es solo para Pasajeros.',
            'conductor' => 'Acceso exclusivo para Conductores.',
            'empresa'   => 'Acceso exclusivo para empresas afiliadas.',
            default     => null,
        };
    }

    /**
     * Valida que el usuario pertenezca al portal antes de autenticar.
     */
    public function validateRoleForPortal(Users|object|null $user, ?string $app): ?string
    {
        if (!$user || !$app || !in_array($app, ['pasajero', 'conductor', 'empresa'], true)) {
            return null;
        }

        if (!$this->userMatchesPortal($user, $app)) {
            return $this->roleMismatchMessage($app);
        }

        return null;
    }

    public function validateLoginGate($user, ?string $app): ?string
    {
        if ((int) ($user->estado ?? 1) !== 1) {
            return 'Tu cuenta está inactiva. Por favor comunícate con el Equipo de Taxpiya.';
        }

        $roleError = $this->validateRoleForPortal($user, $app);
        if ($roleError) {
            return $roleError;
        }

        if ($app === 'conductor') {
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
        } elseif ($app === 'empresa') {
            if (!Schema::hasTable('empresas')) {
                return 'El portal de empresas no está disponible en este momento.';
            }
            $empresa = DB::table('empresas')->where('user_id', $user->id)->first();
            if (!$empresa) {
                return 'Tu cuenta no tiene una empresa vinculada.';
            }
        }

        return null;
    }
}
