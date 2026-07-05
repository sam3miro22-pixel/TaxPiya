<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WalletService
{
    public function getSaldo(int $conductorId): ?object
    {
        if (!Schema::hasTable('wallet_saldos')) {
            return null;
        }

        return DB::table('wallet_saldos')->where('conductor_id', $conductorId)->first();
    }

    public function ensureSaldoRow(int $conductorId, string $moneda = 'COP'): void
    {
        if (!Schema::hasTable('wallet_saldos')) {
            return;
        }

        $exists = DB::table('wallet_saldos')->where('conductor_id', $conductorId)->exists();
        if ($exists) {
            return;
        }

        DB::table('wallet_saldos')->insert([
            'conductor_id'       => $conductorId,
            'saldo_actual'       => 0,
            'saldo_reservado'    => 0,
            'min_operativo'      => (float) config('taxpiya.wallet.default_min_operativo', 5000),
            'moneda'             => $moneda,
            'bloqueado'          => 0,
            'created_at'         => now()->format('Y-m-d H:i:s'),
            'updated_at'         => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function canOperate(int $conductorId): array
    {
        if (!Schema::hasTable('wallet_saldos')) {
            return ['ok' => true, 'saldo' => 0, 'min' => 0];
        }

        $this->ensureSaldoRow($conductorId);
        $saldo = $this->getSaldo($conductorId);

        if ($saldo && (int) ($saldo->bloqueado ?? 0) === 1) {
            return ['ok' => false, 'message' => 'Tu wallet está bloqueada. Contacta soporte.'];
        }

        $minConfig  = (float) ($saldo->min_operativo ?? config('taxpiya.wallet.default_min_operativo', 5000));
        // Mínimo absoluto: siempre al menos $200 COP (cargo por viaje de plataforma)
        $minPlatform = 200.0;
        $min    = max($minConfig, $minPlatform);
        $actual = (float) ($saldo->saldo_actual ?? 0);

        if ($actual < $min) {
            return [
                'ok'      => false,
                'message' => 'Saldo insuficiente. Necesitas al menos $' . number_format($min, 0, ',', '.') . ' COP para operar. Saldo actual: $' . number_format($actual, 0, ',', '.'),
                'saldo'   => $actual,
                'min'     => $min,
            ];
        }

        return ['ok' => true, 'saldo' => $actual, 'min' => $min];
    }

    public function registrarMovimiento(array $data): ?int
    {
        if (!Schema::hasTable('wallet_movimientos')) {
            return null;
        }

        $conductorId = (int) $data['conductor_id'];
        $idempotencia = $data['idempotencia'] ?? null;

        if ($idempotencia) {
            $existingId = DB::table('wallet_movimientos')
                ->where('idempotencia', $idempotencia)
                ->where('anulado', 0)
                ->value('id');

            if ($existingId) {
                return (int) $existingId;
            }
        }

        $this->ensureSaldoRow($conductorId, $data['moneda'] ?? 'COP');

        $movId = null;

        DB::transaction(function () use ($data, $conductorId, &$movId) {
            $saldo = DB::table('wallet_saldos')
                ->where('conductor_id', $conductorId)
                ->lockForUpdate()
                ->first();

            $saldoAntes = (float) ($saldo->saldo_actual ?? 0);
            $monto = round((float) $data['monto'], 2);
            $sentido = $data['sentido'];
            $delta = $sentido === 'credito' ? $monto : -$monto;
            $saldoDespues = round($saldoAntes + $delta, 2);

            if ($sentido === 'debito' && $saldoDespues < 0 && !($data['allow_negative'] ?? false)) {
                throw new RuntimeException('Saldo insuficiente para el débito.');
            }

            $now = now()->format('Y-m-d H:i:s');

            $movId = DB::table('wallet_movimientos')->insertGetId([
                'conductor_id'       => $conductorId,
                'viaje_id'           => $data['viaje_id'] ?? null,
                'admin_user_id'      => $data['admin_user_id'] ?? null,
                'sentido'            => $sentido,
                'motivo'             => $data['motivo'],
                'monto'              => $monto,
                'moneda'             => $data['moneda'] ?? 'COP',
                'saldo_antes'        => $saldoAntes,
                'saldo_despues'      => $saldoDespues,
                'descripcion'        => $data['descripcion'] ?? null,
                'referencia_externa' => $data['referencia_externa'] ?? null,
                'idempotencia'       => $data['idempotencia'] ?? null,
                'anulado'            => 0,
                'created_at'         => $now,
            ]);

            DB::table('wallet_saldos')->where('conductor_id', $conductorId)->update([
                'saldo_actual'       => $saldoDespues,
                'last_movimiento_id' => $movId,
                'last_movimiento_at' => $now,
                'updated_at'         => $now,
            ]);
        });

        if ($movId && Schema::hasTable('wallet_cuentas')) {
            try {
                $ledger = app(WalletLedgerService::class);
                $cuenta = $ledger->ensureCuenta('conductor', $conductorId);
                if ($cuenta) {
                    DB::table('wallet_movimientos')->where('id', $movId)->update([
                        'cuenta_id'      => $cuenta->id,
                        'tipo_operacion' => $data['motivo'],
                        'estado'         => 'completado',
                    ]);
                    DB::table('wallet_cuentas')->where('id', $cuenta->id)->update([
                        'saldo_actual'       => DB::table('wallet_saldos')->where('conductor_id', $conductorId)->value('saldo_actual'),
                        'last_movimiento_id' => $movId,
                        'last_movimiento_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at'         => now()->format('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $movId;
    }

    public function debitoTerminoViaje(int $conductorId, int $viajeId, float $tarifa, string $moneda = 'COP'): ?int
    {
        $pct = (float) config('taxpiya.wallet.commission_percent', 10);
        $minFee = (float) config('taxpiya.wallet.commission_min', 500);
        $fee = max($minFee, round($tarifa * $pct / 100, 2));

        if ($fee <= 0) {
            return null;
        }

        return $this->registrarMovimiento([
            'conductor_id' => $conductorId,
            'viaje_id'     => $viajeId,
            'sentido'      => 'debito',
            'motivo'       => 'debito_termino',
            'monto'        => $fee,
            'moneda'       => $moneda,
            'descripcion'  => "Comisión viaje #{$viajeId} ({$pct}%)",
            'idempotencia' => "debito_termino_viaje_{$viajeId}",
        ]);
    }

    public function debitoAceptacion(int $conductorId, int $viajeId, string $moneda = 'COP'): ?int
    {
        $fee = (float) config('taxpiya.wallet.fee_accept', 0);

        if ($fee <= 0) {
            return null;
        }

        return $this->registrarMovimiento([
            'conductor_id' => $conductorId,
            'viaje_id'     => $viajeId,
            'sentido'      => 'debito',
            'motivo'       => 'debito_aceptacion',
            'monto'        => $fee,
            'moneda'       => $moneda,
            'descripcion'  => "Tarifa aceptación viaje #{$viajeId}",
            'idempotencia' => "debito_aceptacion_viaje_{$viajeId}",
        ]);
    }

    public function recargaInicial(int $conductorId, float $monto, string $descripcion = 'Saldo inicial demo'): ?int
    {
        return $this->registrarMovimiento([
            'conductor_id' => $conductorId,
            'sentido'      => 'credito',
            'motivo'       => 'recarga',
            'monto'        => $monto,
            'moneda'       => 'COP',
            'descripcion'  => $descripcion,
            'idempotencia' => "recarga_inicial_{$conductorId}",
        ]);
    }
}
