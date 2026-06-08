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
            $this->mergeUsers((int) $user->id, (int) $other->id);

            return;
        }

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
                DB::table('referidos')->where('referred_user_id', $discardId)->update(['referred_user_id' => $keepId]);
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
                    DB::table('wallet_movimientos')
                        ->where('cuenta_id', $discardCuenta->id)
                        ->update(['cuenta_id' => $keepCuenta->id, 'user_id' => $keepId]);
                    DB::table('wallet_cuentas')->where('id', $keepCuenta->id)->update([
                        'saldo_actual' => (float) $keepCuenta->saldo_actual + (float) $discardCuenta->saldo_actual,
                        'updated_at'   => now()->toDateTimeString(),
                    ]);
                    DB::table('wallet_cuentas')->where('id', $discardCuenta->id)->delete();
                } elseif ($discardCuenta && !$keepCuenta) {
                    DB::table('wallet_cuentas')->where('id', $discardCuenta->id)->update([
                        'user_id'    => $keepId,
                        'updated_at' => now()->toDateTimeString(),
                    ]);
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
                $keep->save();
            }

            DB::table('users')->where('id', $discardId)->delete();
        });
    }
}
