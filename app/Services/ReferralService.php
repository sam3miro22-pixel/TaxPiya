<?php

namespace App\Services;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReferralService
{
    public function normalizeCode(?string $code): ?string
    {
        if ($code === null) {
            return null;
        }

        $code = strtoupper(trim(preg_replace('/\s+/', '', $code) ?? ''));

        return $code !== '' ? $code : null;
    }

    public function codeForUser(int $userId): string
    {
        return 'TXP-P' . str_pad((string) $userId, 6, '0', STR_PAD_LEFT);
    }

    public function codeForEmpresa(int $empresaId): string
    {
        return 'TXP-E' . str_pad((string) $empresaId, 6, '0', STR_PAD_LEFT);
    }

    public function ensureUserCode(Users|int $user): string
    {
        $model = $user instanceof Users ? $user : Users::query()->findOrFail($user);
        if (!empty($model->codigo_referido)) {
            return $model->codigo_referido;
        }

        $code = $this->codeForUser((int) $model->id);
        DB::table('users')->where('id', $model->id)->update(['codigo_referido' => $code]);

        return $code;
    }

    public function ensureEmpresaCode(int $empresaId): string
    {
        $row = DB::table('empresas')->where('id', $empresaId)->first();
        if (!$row) {
            throw new \InvalidArgumentException('Empresa no encontrada');
        }
        if (!empty($row->codigo_referido)) {
            return $row->codigo_referido;
        }

        $code = $this->codeForEmpresa($empresaId);
        DB::table('empresas')->where('id', $empresaId)->update([
            'codigo_referido' => $code,
            'updated_at'      => now()->toDateTimeString(),
        ]);

        return $code;
    }

    /**
     * @return array{type:string,user_id?:int,empresa_id?:int,code:string}|null
     */
    public function resolveCode(?string $rawCode): ?array
    {
        $code = $this->normalizeCode($rawCode);
        if (!$code) {
            return null;
        }

        $user = DB::table('users')->where('codigo_referido', $code)->first();
        if ($user) {
            return [
                'type'    => 'user',
                'user_id' => (int) $user->id,
                'code'    => $code,
            ];
        }

        $empresa = DB::table('empresas')->where('codigo_referido', $code)->first();
        if ($empresa) {
            return [
                'type'       => 'empresa',
                'empresa_id' => (int) $empresa->id,
                'user_id'    => (int) $empresa->user_id,
                'code'       => $code,
            ];
        }

        return null;
    }

    public function validateCode(?string $rawCode): array
    {
        $resolved = $this->resolveCode($rawCode);
        if (!$resolved) {
            return ['ok' => false, 'message' => 'Código de referido no válido'];
        }

        return ['ok' => true, 'code' => $resolved['code'], 'type' => $resolved['type']];
    }

    /**
     * @return array{ok:bool,message?:string,referido_id?:int,empresa_id?:int}
     */
    public function registerReferral(?string $rawCode, int $referredUserId, string $tipoReferido): array
    {
        $code = $this->normalizeCode($rawCode);
        if (!$code) {
            return ['ok' => true];
        }

        $resolved = $this->resolveCode($code);
        if (!$resolved) {
            return ['ok' => false, 'message' => 'El código de referido no existe'];
        }

        if ($resolved['type'] === 'user' && (int) $resolved['user_id'] === $referredUserId) {
            return ['ok' => false, 'message' => 'No puedes usar tu propio código de referido'];
        }

        $exists = DB::table('referidos')->where('referred_user_id', $referredUserId)->exists();
        if ($exists) {
            return ['ok' => true];
        }

        $estado = $tipoReferido === 'pasajero' ? 'activo' : 'registrado';

        $referidoId = DB::table('referidos')->insertGetId([
            'codigo_usado'         => $code,
            'referrer_tipo'        => $resolved['type'],
            'referrer_user_id'     => $resolved['type'] === 'user' ? $resolved['user_id'] : ($resolved['user_id'] ?? null),
            'referrer_empresa_id'  => $resolved['type'] === 'empresa' ? $resolved['empresa_id'] : null,
            'referred_user_id'     => $referredUserId,
            'tipo_referido'        => $tipoReferido,
            'estado'               => $estado,
            'created_at'           => now()->toDateTimeString(),
            'updated_at'           => now()->toDateTimeString(),
        ]);

        $empresaId = null;
        if ($tipoReferido === 'conductor' && $resolved['type'] === 'empresa') {
            $empresaId = $resolved['empresa_id'];
            DB::table('conductores')
                ->where('user_id', $referredUserId)
                ->update([
                    'empresa_id' => $empresaId,
                    'updated_at' => now()->toDateTimeString(),
                ]);
        }

        $bonus = ['ok' => false];
        if ($estado === 'activo') {
            $bonus = $this->payReferralBonus((int) $referidoId);
            if (!$bonus['ok'] && empty($bonus['already_paid'])) {
                $referrerUserId = $resolved['type'] === 'user'
                    ? (int) $resolved['user_id']
                    : (int) ($resolved['user_id'] ?? 0);
                if ($referrerUserId > 0) {
                    $this->processPendingBonusesForReferrerUser($referrerUserId);
                    $bonus = $this->payReferralBonus((int) $referidoId);
                }
                if (!$bonus['ok'] && empty($bonus['already_paid'])) {
                    Log::warning('Bono referido no acreditado al registrar', [
                        'referido_id' => $referidoId,
                        'message'     => $bonus['message'] ?? 'desconocido',
                    ]);
                }
            }
        }

        return [
            'ok'          => true,
            'referido_id' => (int) $referidoId,
            'empresa_id'  => $empresaId,
            'bonus'       => $bonus,
        ];
    }

    public function activateReferral(int $referredUserId, string $tipoReferido): void
    {
        $ids = DB::table('referidos')
            ->where('referred_user_id', $referredUserId)
            ->where('tipo_referido', $tipoReferido)
            ->where('estado', 'registrado')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('referidos')
            ->whereIn('id', $ids)
            ->update([
                'estado'     => 'activo',
                'updated_at' => now()->toDateTimeString(),
            ]);

        foreach ($ids as $id) {
            $this->payReferralBonus((int) $id);
        }
    }

    /**
     * Acredita $5.000 (configurable) al referidor cuando el referido queda activo.
     *
     * @return array{ok:bool,message?:string,monto?:float,movimiento_id?:int,already_paid?:bool}
     */
    public function payReferralBonus(int $referidoId): array
    {
        if (!config('taxpiya.referrals.enabled', true)) {
            return ['ok' => false, 'message' => 'Programa de referidos deshabilitado'];
        }

        $amount = (float) config('taxpiya.referrals.bonus_amount', 5000);
        if ($amount <= 0) {
            return ['ok' => true, 'skipped' => true];
        }

        $row = DB::table('referidos')->where('id', $referidoId)->first();
        if (!$row) {
            return ['ok' => false, 'message' => 'Referido no encontrado'];
        }

        if ($row->estado !== 'activo') {
            return ['ok' => false, 'message' => 'Referido aún no confirmado'];
        }

        if ($this->isBonusAlreadyPaid($referidoId, $row)) {
            return ['ok' => true, 'already_paid' => true];
        }

        if (!Schema::hasTable('wallet_cuentas')) {
            return ['ok' => false, 'message' => 'Sistema de billetera no disponible'];
        }

        $cuentaId = $this->resolveReferrerCuentaId($row);
        if (!$cuentaId) {
            return ['ok' => false, 'message' => 'Billetera del referidor no disponible'];
        }

        try {
            $ledger = app(WalletLedgerService::class);
            $movId = $ledger->registrarMovimientoCuenta($cuentaId, [
                'sentido'        => 'credito',
                'motivo'         => 'bono_referido',
                'tipo_operacion' => 'bono_referido',
                'monto'          => $amount,
                'moneda'         => 'COP',
                'descripcion'    => 'Bono referido #' . $referidoId . ' (' . $row->tipo_referido . ')',
                'user_id'        => $row->referrer_user_id,
                'idempotencia'   => 'referido_bonus_' . $referidoId,
            ]);

            $this->markBonusPaid($referidoId, $amount);

            return ['ok' => true, 'monto' => $amount, 'movimiento_id' => $movId];
        } catch (\Throwable $e) {
            report($e);
            Log::warning('payReferralBonus falló', [
                'referido_id' => $referidoId,
                'err'         => $e->getMessage(),
            ]);

            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reintenta bonos pendientes del referidor (p. ej. billetera creada después del registro).
     */
    public function processPendingBonusesForReferrerUser(int $userId): int
    {
        if ($userId <= 0 || !Schema::hasTable('referidos')) {
            return 0;
        }

        $ledger = app(WalletLedgerService::class);
        $ledger->ensureCuenta('pasajero', $userId);

        $empresaId = DB::table('empresas')->where('user_id', $userId)->value('id');
        if ($empresaId) {
            $ledger->ensureCuenta('empresa', (int) $empresaId);
        }

        $conductorId = DB::table('conductores')->where('user_id', $userId)->value('id');
        if ($conductorId) {
            $ledger->ensureCuenta('conductor', (int) $conductorId);
        }

        $query = DB::table('referidos')->where('estado', 'activo');
        if (Schema::hasColumn('referidos', 'bonus_paid_at')) {
            $query->whereNull('bonus_paid_at');
        }

        $ids = $query->where(function ($q) use ($userId, $empresaId) {
            $q->where('referrer_user_id', $userId);
            if ($empresaId) {
                $q->orWhere('referrer_empresa_id', (int) $empresaId);
            }
        })->pluck('id');

        $paid = 0;
        foreach ($ids as $id) {
            $result = $this->payReferralBonus((int) $id);
            if (!empty($result['ok']) && empty($result['already_paid'])) {
                $paid++;
            }
        }

        return $paid;
    }

    public function backfillAllUnpaidBonuses(): int
    {
        if (!Schema::hasTable('referidos') || !Schema::hasTable('wallet_cuentas')) {
            return 0;
        }

        $query = DB::table('referidos')->where('estado', 'activo');
        if (Schema::hasColumn('referidos', 'bonus_paid_at')) {
            $query->whereNull('bonus_paid_at');
        }

        $total = 0;
        foreach ($query->pluck('id') as $id) {
            $result = $this->payReferralBonus((int) $id);
            if (!empty($result['ok']) && empty($result['already_paid'])) {
                $total++;
            }
        }

        return $total;
    }

    private function isBonusAlreadyPaid(int $referidoId, object $row): bool
    {
        if (Schema::hasColumn('referidos', 'bonus_paid_at') && !empty($row->bonus_paid_at)) {
            return true;
        }

        if (!Schema::hasTable('wallet_movimientos')) {
            return false;
        }

        return DB::table('wallet_movimientos')
            ->where('idempotencia', 'referido_bonus_' . $referidoId)
            ->where('anulado', 0)
            ->exists();
    }

    private function markBonusPaid(int $referidoId, float $amount): void
    {
        $payload = ['updated_at' => now()->toDateTimeString()];
        if (Schema::hasColumn('referidos', 'bonus_monto')) {
            $payload['bonus_monto'] = $amount;
        }
        if (Schema::hasColumn('referidos', 'bonus_paid_at')) {
            $payload['bonus_paid_at'] = now()->toDateTimeString();
        }
        if (count($payload) > 1) {
            DB::table('referidos')->where('id', $referidoId)->update($payload);
        }
    }

    private function resolveReferrerCuentaId(object $referido): ?int
    {
        $ledger = app(WalletLedgerService::class);

        if ($referido->referrer_tipo === 'empresa' && !empty($referido->referrer_empresa_id)) {
            $cuenta = $ledger->ensureCuenta('empresa', (int) $referido->referrer_empresa_id);

            return $cuenta ? (int) $cuenta->id : null;
        }

        if (empty($referido->referrer_user_id)) {
            return null;
        }

        $userId = (int) $referido->referrer_user_id;
        $user = Users::find($userId);
        if (!$user) {
            return null;
        }

        $roleId = (int) ($user->user_role_id ?? 0);

        if ($roleId === 4 || $user->hasRole('Empresa')) {
            $empresaId = DB::table('empresas')->where('user_id', $userId)->value('id');
            if ($empresaId) {
                $cuenta = $ledger->ensureCuenta('empresa', (int) $empresaId);

                return $cuenta ? (int) $cuenta->id : null;
            }
        }

        if ($roleId === 3 || $user->hasRole('Conductor')) {
            $conductorId = DB::table('conductores')->where('user_id', $userId)->value('id');
            if ($conductorId) {
                $cuenta = $ledger->ensureCuenta('conductor', (int) $conductorId);

                return $cuenta ? (int) $cuenta->id : null;
            }
        }

        $cuenta = $ledger->ensureCuenta('pasajero', $userId);

        return $cuenta ? (int) $cuenta->id : null;
    }

    public function statsForUser(int $userId): array
    {
        $code = DB::table('users')->where('id', $userId)->value('codigo_referido');
        if (!$code) {
            $user = Users::find($userId);
            if ($user) {
                $code = $this->ensureUserCode($user);
            }
        }

        $base = DB::table('referidos')->where('referrer_user_id', $userId);

        $bonusAmount = (float) config('taxpiya.referrals.bonus_amount', 5000);

        return [
            'codigo'      => $code,
            'total'       => (clone $base)->count(),
            'activos'     => (clone $base)->where('estado', 'activo')->count(),
            'pasajeros'   => (clone $base)->where('tipo_referido', 'pasajero')->count(),
            'conductores' => (clone $base)->where('tipo_referido', 'conductor')->count(),
            'empresas'    => (clone $base)->where('tipo_referido', 'empresa')->count(),
            'bonos_pagados' => (int) (clone $base)->whereNotNull('bonus_paid_at')->count(),
            'ganancia_total' => (float) (clone $base)->whereNotNull('bonus_paid_at')->sum('bonus_monto'),
            'bono_por_referido' => $bonusAmount,
        ];
    }

    public function statsForEmpresa(int $empresaId): array
    {
        $code = DB::table('empresas')->where('id', $empresaId)->value('codigo_referido');
        if (!$code) {
            $code = $this->ensureEmpresaCode($empresaId);
        }

        $base = DB::table('referidos')->where('referrer_empresa_id', $empresaId);

        $bonusAmount = (float) config('taxpiya.referrals.bonus_amount', 5000);

        return [
            'codigo'      => $code,
            'total'       => (clone $base)->count(),
            'activos'     => (clone $base)->where('estado', 'activo')->count(),
            'pasajeros'   => (clone $base)->where('tipo_referido', 'pasajero')->count(),
            'conductores' => (clone $base)->where('tipo_referido', 'conductor')->count(),
            'empresas'    => (clone $base)->where('tipo_referido', 'empresa')->count(),
            'bonos_pagados' => (int) (clone $base)->whereNotNull('bonus_paid_at')->count(),
            'ganancia_total' => (float) (clone $base)->whereNotNull('bonus_paid_at')->sum('bonus_monto'),
            'bono_por_referido' => $bonusAmount,
        ];
    }

    public function backfillCodes(): void
    {
        if (!Schema::hasColumn('users', 'codigo_referido')) {
            return;
        }

        $userIds = DB::table('users')->whereNull('codigo_referido')->orWhere('codigo_referido', '')->pluck('id');
        foreach ($userIds as $id) {
            DB::table('users')->where('id', $id)->update([
                'codigo_referido' => $this->codeForUser((int) $id),
            ]);
        }

        if (Schema::hasTable('empresas') && Schema::hasColumn('empresas', 'codigo_referido')) {
            $empresaIds = DB::table('empresas')->whereNull('codigo_referido')->orWhere('codigo_referido', '')->pluck('id');
            foreach ($empresaIds as $id) {
                DB::table('empresas')->where('id', $id)->update([
                    'codigo_referido' => $this->codeForEmpresa((int) $id),
                    'updated_at'      => now()->toDateTimeString(),
                ]);
            }
        }
    }
}
