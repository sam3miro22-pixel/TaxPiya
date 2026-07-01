<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AccountPurgeService
{
    /** @return list<int> */
    public function keepUserIds(): array
    {
        $emails = DemoAccountCatalog::keepEmails();

        $ids = DB::table('users')
            ->where(function ($q) use ($emails) {
                foreach ($emails as $email) {
                    $q->orWhereRaw('LOWER(email) = ?', [strtolower($email)]);
                }
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return array_values(array_unique($ids));
    }

    /**
     * @return array{users:int,conductores:int,empresas:int,referidos:int,viajes:int,firebase:int}
     */
    public function purgeNonDemo(bool $purgeFirebase = true): array
    {
        $keepIds = $this->keepUserIds();
        if ($keepIds === []) {
            throw new \RuntimeException('No hay usuarios demo en la base de datos. Ejecuta taxpiya:seed-demo primero.');
        }

        $deleteUserIds = DB::table('users')
            ->whereNotIn('id', $keepIds)
            ->where(function ($q) {
                $q->whereNull('email')
                    ->orWhere('email', 'not like', '%@internal.local');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($deleteUserIds === []) {
            $firebaseDeleted = $purgeFirebase ? $this->purgeFirebaseAuth() : 0;

            return [
                'users'      => 0,
                'conductores'=> 0,
                'empresas'   => 0,
                'referidos'  => 0,
                'viajes'     => 0,
                'firebase'   => $firebaseDeleted,
            ];
        }

        $deleteConductorIds = Schema::hasTable('conductores')
            ? DB::table('conductores')->whereIn('user_id', $deleteUserIds)->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];

        $deleteEmpresaIds = Schema::hasTable('empresas')
            ? DB::table('empresas')->whereNotIn('user_id', $keepIds)->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];

        $deleted = [
            'users'       => 0,
            'conductores' => 0,
            'empresas'    => 0,
            'referidos'   => 0,
            'viajes'      => 0,
            'firebase'    => 0,
        ];

        DB::transaction(function () use ($deleteUserIds, $deleteConductorIds, $deleteEmpresaIds, &$deleted) {
            if (Schema::hasTable('referidos')) {
                $deleted['referidos'] = DB::table('referidos')
                    ->where(function ($q) use ($deleteUserIds, $deleteEmpresaIds) {
                        $q->whereIn('referrer_user_id', $deleteUserIds)
                            ->orWhereIn('referred_user_id', $deleteUserIds);
                        if ($deleteEmpresaIds !== []) {
                            $q->orWhereIn('referrer_empresa_id', $deleteEmpresaIds);
                        }
                    })
                    ->delete();
            }

            if (Schema::hasTable('viajes')) {
                $deleted['viajes'] = DB::table('viajes')
                    ->where(function ($q) use ($deleteUserIds, $deleteConductorIds) {
                        $q->whereIn('pasajero_id', $deleteUserIds);
                        if ($deleteConductorIds !== []) {
                            $q->orWhereIn('conductor_id', $deleteConductorIds);
                        }
                    })
                    ->delete();
            }

            if (Schema::hasTable('conductor_posicion_actual') && $deleteConductorIds !== []) {
                DB::table('conductor_posicion_actual')->whereIn('conductor_id', $deleteConductorIds)->delete();
            }

            if (Schema::hasTable('wallet_movimientos')) {
                DB::table('wallet_movimientos')->whereIn('user_id', $deleteUserIds)->delete();
                if ($deleteConductorIds !== []) {
                    DB::table('wallet_movimientos')->whereIn('conductor_id', $deleteConductorIds)->delete();
                }
            }

            if (Schema::hasTable('wallet_cuentas')) {
                $cuentaIds = DB::table('wallet_cuentas')
                    ->where(function ($q) use ($deleteUserIds, $deleteConductorIds, $deleteEmpresaIds) {
                        $q->whereIn('user_id', $deleteUserIds);
                        if ($deleteConductorIds !== []) {
                            $q->orWhereIn('conductor_id', $deleteConductorIds);
                        }
                        if ($deleteEmpresaIds !== []) {
                            $q->orWhereIn('empresa_id', $deleteEmpresaIds);
                        }
                    })
                    ->pluck('id');
                if ($cuentaIds->isNotEmpty()) {
                    if (Schema::hasTable('wallet_movimientos')) {
                        DB::table('wallet_movimientos')->whereIn('cuenta_id', $cuentaIds)->delete();
                    }
                    if (Schema::hasTable('wallet_solicitudes')) {
                        DB::table('wallet_solicitudes')->whereIn('cuenta_id', $cuentaIds)->delete();
                    }
                    DB::table('wallet_cuentas')->whereIn('id', $cuentaIds)->delete();
                }
            }

            if (Schema::hasTable('wallet_saldos') && $deleteConductorIds !== []) {
                DB::table('wallet_saldos')->whereIn('conductor_id', $deleteConductorIds)->delete();
            }

            if (Schema::hasTable('vehiculos') && $deleteConductorIds !== []) {
                DB::table('vehiculos')->whereIn('conductor_id', $deleteConductorIds)->delete();
            }

            if (Schema::hasTable('conductores') && $deleteConductorIds !== []) {
                $deleted['conductores'] = DB::table('conductores')->whereIn('id', $deleteConductorIds)->delete();
            }

            if (Schema::hasTable('empresas') && $deleteEmpresaIds !== []) {
                $deleted['empresas'] = DB::table('empresas')->whereIn('id', $deleteEmpresaIds)->delete();
            }

            foreach (['push_tokens', 'usuario_dispositivos', 'notificaciones', 'sos_incidentes', 'llamadas', 'auditoria_eventos'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                    DB::table($table)->whereIn('user_id', $deleteUserIds)->delete();
                }
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->whereIn('user_id', $deleteUserIds)->delete();
            }

            $deleted['users'] = DB::table('users')->whereIn('id', $deleteUserIds)->delete();
        });

        app(ReferralService::class)->backfillCodes();

        if ($purgeFirebase) {
            $deleted['firebase'] = $this->purgeFirebaseAuth();
        }

        return $deleted;
    }

    /**
     * Elimina pasajeros/conductores SQLite sin firebase_uid (cuentas locales huérfanas).
     */
    public function purgeLocalShadowAccounts(): int
    {
        if (!Schema::hasTable('users')) {
            return 0;
        }

        $keepIds = $this->keepUserIds();
        $roleIds = DB::table('roles')
            ->whereIn('role_name', ['Pasajero', 'Conductor'])
            ->pluck('role_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($roleIds === []) {
            return 0;
        }

        $deleteUserIds = DB::table('users')
            ->whereNotIn('id', $keepIds)
            ->whereIn('user_role_id', $roleIds)
            ->where(function ($q) {
                $q->whereNull('firebase_uid')->orWhere('firebase_uid', '');
            })
            ->where(function ($q) {
                $q->whereNull('email')->orWhere('email', 'not like', '%@internal.local');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($deleteUserIds === []) {
            return 0;
        }

        DB::transaction(function () use ($deleteUserIds) {
            if (Schema::hasTable('wallet_solicitudes') && Schema::hasTable('wallet_cuentas')) {
                $cuentaIds = DB::table('wallet_cuentas')->whereIn('user_id', $deleteUserIds)->pluck('id');
                if ($cuentaIds->isNotEmpty()) {
                    DB::table('wallet_solicitudes')->whereIn('cuenta_id', $cuentaIds)->delete();
                    DB::table('wallet_movimientos')->whereIn('cuenta_id', $cuentaIds)->delete();
                    DB::table('wallet_cuentas')->whereIn('id', $cuentaIds)->delete();
                }
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->whereIn('user_id', $deleteUserIds)->delete();
            }

            DB::table('users')->whereIn('id', $deleteUserIds)->delete();
        });

        return count($deleteUserIds);
    }

    /**
     * Limpia viajes, calificaciones, billeteras y demás datos transaccionales.
     * Conserva usuarios demo y estructura de conductores/empresas.
     *
     * @return array<string, int>
     */
    public function purgeAllTransactionalData(): array
    {
        $counts = [];

        DB::transaction(function () use (&$counts) {
            foreach (['calificaciones', 'asignaciones', 'llamadas'] as $table) {
                if (Schema::hasTable($table)) {
                    $counts[$table] = DB::table($table)->delete();
                }
            }

            if (Schema::hasTable('viajes')) {
                $counts['viajes'] = DB::table('viajes')->delete();
            }

            if (Schema::hasTable('sos_incidentes')) {
                $counts['sos_incidentes'] = DB::table('sos_incidentes')->delete();
            }

            if (Schema::hasTable('referidos')) {
                $counts['referidos'] = DB::table('referidos')->delete();
            }

            if (Schema::hasTable('wallet_solicitudes')) {
                $counts['wallet_solicitudes'] = DB::table('wallet_solicitudes')->delete();
            }

            if (Schema::hasTable('wallet_movimientos')) {
                $counts['wallet_movimientos'] = DB::table('wallet_movimientos')->delete();
            }

            if (Schema::hasTable('wallet_cuentas')) {
                DB::table('wallet_cuentas')->update([
                    'saldo_actual'       => 0,
                    'saldo_reservado'    => 0,
                    'last_movimiento_id' => null,
                    'last_movimiento_at' => null,
                    'updated_at'         => now()->toDateTimeString(),
                ]);
                $counts['wallet_cuentas_reset'] = DB::table('wallet_cuentas')->count();
            }

            if (Schema::hasTable('wallet_saldos')) {
                DB::table('wallet_saldos')->update([
                    'saldo_actual'       => 0,
                    'saldo_reservado'    => 0,
                    'last_movimiento_id' => null,
                    'last_movimiento_at' => null,
                    'updated_at'         => now()->toDateTimeString(),
                ]);
                $counts['wallet_saldos_reset'] = DB::table('wallet_saldos')->count();
            }

            if (Schema::hasTable('conductor_posicion_actual')) {
                $counts['conductor_posicion_actual'] = DB::table('conductor_posicion_actual')->delete();
            }

            if (Schema::hasTable('conductores')) {
                DB::table('conductores')->update(['disponible' => 0]);
            }
        });

        return $counts;
    }

    public function purgeFirebaseAuth(): int
    {
        if (!config('taxpiya.firebase.use_firebase_auth')) {
            return 0;
        }

        $credentials = config('firebase.credentials');
        if (!is_readable($credentials) || !class_exists(\Kreait\Firebase\Factory::class)) {
            Log::warning('Firebase purge omitido: sin credenciales o SDK');

            return 0;
        }

        $keepEmails = array_map('strtolower', DemoAccountCatalog::keepFirebaseEmails());
        $keepUids = DB::table('users')
            ->where(function ($q) use ($keepEmails) {
                foreach ($keepEmails as $email) {
                    $q->orWhereRaw('LOWER(email) = ?', [$email]);
                }
            })
            ->whereNotNull('firebase_uid')
            ->where('firebase_uid', '!=', '')
            ->pluck('firebase_uid')
            ->map(fn ($uid) => (string) $uid)
            ->all();
        $deleted = 0;

        try {
            $auth = (new \Kreait\Firebase\Factory())
                ->withServiceAccount($credentials)
                ->createAuth();

            $users = $auth->listUsers(1000, 1000);
            foreach ($users as $user) {
                $email = strtolower((string) ($user->email ?? ''));
                $uid = (string) $user->uid;
                if (in_array($uid, $keepUids, true)) {
                    continue;
                }
                if ($email !== '' && in_array($email, $keepEmails, true)) {
                    continue;
                }
                try {
                    $auth->deleteUser($user->uid);
                    $this->deleteFirestoreUser($user->uid);
                    $deleted++;
                } catch (\Throwable $e) {
                    Log::warning('No se pudo borrar usuario Firebase', ['email' => $email, 'err' => $e->getMessage()]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Firebase purge falló', ['err' => $e->getMessage()]);
            throw $e;
        }

        return $deleted;
    }

    private function deleteFirestoreUser(string $uid): void
    {
        if (!config('taxpiya.firebase.use_firestore') || !class_exists(\Kreait\Firebase\Factory::class)) {
            return;
        }

        try {
            $firestore = (new \Kreait\Firebase\Factory())
                ->withServiceAccount(config('firebase.credentials'))
                ->createFirestore();
            $firestore->database()->collection('users')->document($uid)->delete();
        } catch (\Throwable $e) {
            Log::warning('Firestore delete user', ['uid' => $uid, 'err' => $e->getMessage()]);
        }
    }
}
