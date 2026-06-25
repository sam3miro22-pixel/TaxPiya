<?php

namespace App\Services;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserAccountService
{
    public function normalizeTelefono(?string $telefono): ?string
    {
        if ($telefono === null || $telefono === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $telefono);

        return $digits !== '' ? $digits : null;
    }

    public function findByFirebaseIdentity(string $uid, ?string $email, ?string $telefono = null): ?Users
    {
        $user = Users::query()->where('firebase_uid', $uid)->first();
        if ($user) {
            return $user;
        }

        if ($email) {
            $user = Users::query()
                ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
                ->orderByDesc('id')
                ->first();
            if ($user) {
                return $user;
            }
        }

        $tel = $this->normalizeTelefono($telefono);
        if ($tel) {
            $user = Users::query()
                ->where(function ($q) use ($tel) {
                    $q->where('telefono', $tel)
                        ->orWhere('telefono', 'like', '%' . $tel);
                })
                ->orderByDesc('id')
                ->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    public function linkFirebaseUid(Users $user, string $uid): void
    {
        if ($user->firebase_uid === $uid) {
            return;
        }

        $other = Users::query()->where('firebase_uid', $uid)->where('id', '!=', $user->id)->first();
        if ($other) {
            try {
                $this->mergeUsers((int) $user->id, (int) $other->id);
            } catch (\Throwable $e) {
                report($e);
                $this->reassignFirebaseUid($user, $uid);
            }

            return;
        }

        $user->firebase_uid = $uid;
        $user->save();
    }

    /**
     * Asigna firebase_uid al usuario conservado, liberando el valor en otras filas.
     */
    private function reassignFirebaseUid(Users $user, string $uid): void
    {
        DB::table('users')
            ->where('firebase_uid', $uid)
            ->where('id', '!=', $user->id)
            ->update(['firebase_uid' => null]);

        $user->firebase_uid = $uid;
        $user->save();
    }

    /**
     * Mueve referidos, billetera y vínculos al usuario que se conserva.
     */
    public function mergeUsers(int $keepId, int $discardId): void
    {
        if ($keepId === $discardId) {
            return;
        }

        DB::transaction(function () use ($keepId, $discardId) {
            if (Schema::hasTable('referidos')) {
                DB::table('referidos')->where('referrer_user_id', $discardId)->update(['referrer_user_id' => $keepId]);

                $keepAlreadyReferred = DB::table('referidos')->where('referred_user_id', $keepId)->exists();
                if ($keepAlreadyReferred) {
                    DB::table('referidos')->where('referred_user_id', $discardId)->delete();
                } else {
                    DB::table('referidos')->where('referred_user_id', $discardId)->update(['referred_user_id' => $keepId]);
                }
            }

            if (Schema::hasTable('wallet_cuentas')) {
                $discardCuenta = DB::table('wallet_cuentas')
                    ->where('tipo', 'pasajero')
                    ->where('user_id', $discardId)
                    ->first();
                $keepCuenta = DB::table('wallet_cuentas')
                    ->where('tipo', 'pasajero')
                    ->where('user_id', $keepId)
                    ->first();

                if ($discardCuenta && $keepCuenta) {
                    if (Schema::hasTable('wallet_movimientos') && Schema::hasColumn('wallet_movimientos', 'cuenta_id')) {
                        $movUpdate = ['cuenta_id' => $keepCuenta->id];
                        if (Schema::hasColumn('wallet_movimientos', 'user_id')) {
                            $movUpdate['user_id'] = $keepId;
                        }
                        DB::table('wallet_movimientos')
                            ->where('cuenta_id', $discardCuenta->id)
                            ->update($movUpdate);
                    }
                    $cuentaUpdate = ['saldo_actual' => (float) $keepCuenta->saldo_actual + (float) $discardCuenta->saldo_actual];
                    if (Schema::hasColumn('wallet_cuentas', 'updated_at')) {
                        $cuentaUpdate['updated_at'] = now()->toDateTimeString();
                    }
                    DB::table('wallet_cuentas')->where('id', $keepCuenta->id)->update($cuentaUpdate);
                    DB::table('wallet_cuentas')->where('id', $discardCuenta->id)->delete();
                } elseif ($discardCuenta && !$keepCuenta) {
                    $cuentaUpdate = ['user_id' => $keepId];
                    if (Schema::hasColumn('wallet_cuentas', 'updated_at')) {
                        $cuentaUpdate['updated_at'] = now()->toDateTimeString();
                    }
                    DB::table('wallet_cuentas')->where('id', $discardCuenta->id)->update($cuentaUpdate);
                }
            }

            $keep = Users::find($keepId);
            $discard = Users::find($discardId);
            if ($keep && $discard) {
                if (empty($keep->codigo_referido) && !empty($discard->codigo_referido)) {
                    $keep->codigo_referido = $discard->codigo_referido;
                }
                if (empty($keep->firebase_uid) && !empty($discard->firebase_uid)) {
                    $keep->firebase_uid = $discard->firebase_uid;
                }
                if (empty($keep->fotoperfil) && !empty($discard->fotoperfil)) {
                    $keep->fotoperfil = $discard->fotoperfil;
                }
                if ((str_starts_with((string) $keep->telefono, 'fb_') || empty($keep->telefono)) && !empty($discard->telefono)) {
                    $keep->telefono = $discard->telefono;
                }
                if (str_contains((string) $keep->email, '@firebase.taxpiya.local') && !empty($discard->email)) {
                    $keep->email = $discard->email;
                }
                $keep->save();
            }

            if (Schema::hasTable('viajes')) {
                DB::table('viajes')->where('pasajero_id', $discardId)->update(['pasajero_id' => $keepId]);
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $discardId)->update(['user_id' => $keepId]);
            }

            if (Schema::hasTable('push_tokens') && Schema::hasColumn('push_tokens', 'user_id')) {
                DB::table('push_tokens')->where('user_id', $discardId)->update(['user_id' => $keepId]);
            }

            DB::table('users')->where('id', $discardId)->delete();
        });
    }

    /**
     * Fusiona cuentas duplicadas (mismo email, teléfono o firebase_uid).
     *
     * @return array{merged:int,groups:int}
     */
    public function repairDuplicateAccounts(): array
    {
        $merged = 0;
        $groups = 0;

        foreach ($this->duplicateEmailGroups() as $ids) {
            $groups++;
            $keepId = $this->pickAccountToKeep($ids);
            foreach ($ids as $id) {
                if ((int) $id !== $keepId) {
                    $this->mergeUsers($keepId, (int) $id);
                    $merged++;
                }
            }
        }

        foreach ($this->duplicateTelefonoGroups() as $ids) {
            $groups++;
            $keepId = $this->pickAccountToKeep($ids);
            foreach ($ids as $id) {
                if ((int) $id !== $keepId) {
                    $this->mergeUsers($keepId, (int) $id);
                    $merged++;
                }
            }
        }

        if (Schema::hasColumn('users', 'firebase_uid')) {
            $uidDupes = DB::table('users')
                ->whereNotNull('firebase_uid')
                ->where('firebase_uid', '!=', '')
                ->select('firebase_uid')
                ->groupBy('firebase_uid')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('firebase_uid');

            foreach ($uidDupes as $uid) {
                $ids = DB::table('users')->where('firebase_uid', $uid)->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
                if (count($ids) < 2) {
                    continue;
                }
                $groups++;
                $keepId = $this->pickAccountToKeep($ids);
                foreach ($ids as $id) {
                    if ($id !== $keepId) {
                        $this->mergeUsers($keepId, $id);
                        $merged++;
                    }
                }
            }
        }

        return ['merged' => $merged, 'groups' => $groups];
    }

    /**
     * @return list<list<int>>
     */
    private function duplicateEmailGroups(): array
    {
        $rows = DB::table('users')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%@firebase.taxpiya.local')
            ->where('email', 'not like', '%@conductor.taxpiya.local')
            ->selectRaw('LOWER(TRIM(email)) as norm, GROUP_CONCAT(id) as ids')
            ->groupByRaw('LOWER(TRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $groups = [];
        foreach ($rows as $row) {
            $ids = array_map('intval', array_filter(explode(',', (string) $row->ids)));
            if (count($ids) > 1) {
                $groups[] = $ids;
            }
        }

        return $groups;
    }

    /**
     * @return list<list<int>>
     */
    private function duplicateTelefonoGroups(): array
    {
        $rows = DB::table('users')
            ->whereNotNull('telefono')
            ->where('telefono', '!=', '')
            ->where('telefono', 'not like', 'fb\\_%')
            ->selectRaw('telefono as norm, GROUP_CONCAT(id) as ids')
            ->groupBy('telefono')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $groups = [];
        foreach ($rows as $row) {
            $ids = array_map('intval', array_filter(explode(',', (string) $row->ids)));
            if (count($ids) > 1) {
                $groups[] = $ids;
            }
        }

        return $groups;
    }

    /**
     * @param list<int> $ids
     */
    private function pickAccountToKeep(array $ids): int
    {
        $bestId = $ids[0];
        $bestScore = PHP_INT_MIN;

        foreach ($ids as $id) {
            $score = $this->accountKeepScore((int) $id);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = (int) $id;
            }
        }

        return $bestId;
    }

    private function accountKeepScore(int $userId): int
    {
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) {
            return 0;
        }

        $score = 0;
        if (!empty($user->firebase_uid)) {
            $score += 100;
        }
        if ((int) ($user->estado ?? 0) === 1) {
            $score += 50;
        }
        if (!empty($user->codigo_referido)) {
            $score += 20;
        }
        if (!empty($user->fotoperfil)) {
            $score += 5;
        }
        if (!str_starts_with((string) $user->telefono, 'fb_')) {
            $score += 10;
        }

        if (Schema::hasTable('referidos')) {
            $score += (int) DB::table('referidos')->where('referrer_user_id', $userId)->count() * 25;
            $score += (int) DB::table('referidos')->where('referred_user_id', $userId)->count() * 5;
        }

        if (Schema::hasTable('wallet_cuentas')) {
            $balance = (float) DB::table('wallet_cuentas')
                ->where('user_id', $userId)
                ->where('tipo', 'pasajero')
                ->value('saldo_actual');
            $score += (int) min($balance / 500, 200);
        }

        if (Schema::hasTable('viajes')) {
            $score += (int) min(DB::table('viajes')->where('pasajero_id', $userId)->count(), 50);
        }

        return $score * 1000 - $userId;
    }
}
