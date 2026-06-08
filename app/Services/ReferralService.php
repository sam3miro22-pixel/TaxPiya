<?php

namespace App\Services;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
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

        return [
            'ok'          => true,
            'referido_id' => (int) $referidoId,
            'empresa_id'  => $empresaId,
        ];
    }

    public function activateReferral(int $referredUserId, string $tipoReferido): void
    {
        DB::table('referidos')
            ->where('referred_user_id', $referredUserId)
            ->where('tipo_referido', $tipoReferido)
            ->where('estado', 'registrado')
            ->update([
                'estado'     => 'activo',
                'updated_at' => now()->toDateTimeString(),
            ]);
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

        return [
            'codigo'     => $code,
            'total'      => (clone $base)->count(),
            'activos'    => (clone $base)->where('estado', 'activo')->count(),
            'pasajeros'  => (clone $base)->where('tipo_referido', 'pasajero')->count(),
            'conductores'=> (clone $base)->where('tipo_referido', 'conductor')->count(),
            'empresas'   => (clone $base)->where('tipo_referido', 'empresa')->count(),
        ];
    }

    public function statsForEmpresa(int $empresaId): array
    {
        $code = DB::table('empresas')->where('id', $empresaId)->value('codigo_referido');
        if (!$code) {
            $code = $this->ensureEmpresaCode($empresaId);
        }

        $base = DB::table('referidos')->where('referrer_empresa_id', $empresaId);

        return [
            'codigo'     => $code,
            'total'      => (clone $base)->count(),
            'activos'    => (clone $base)->where('estado', 'activo')->count(),
            'pasajeros'  => (clone $base)->where('tipo_referido', 'pasajero')->count(),
            'conductores'=> (clone $base)->where('tipo_referido', 'conductor')->count(),
            'empresas'   => (clone $base)->where('tipo_referido', 'empresa')->count(),
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
