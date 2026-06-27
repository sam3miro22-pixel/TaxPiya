<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TripPaymentService
{
    public function __construct(
        private WalletLedgerService $ledger,
        private WalletService $wallet,
    ) {
    }

    public function commissionPercent(int $conductorId): float
    {
        if (Schema::hasColumn('conductores', 'comision_plataforma_percent')) {
            $raw = DB::table('conductores')->where('id', $conductorId)->value('comision_plataforma_percent');
            if ($raw !== null && $raw !== '') {
                return max(0.0, min(100.0, (float) $raw));
            }
        }

        return max(0.0, (float) config('taxpiya.wallet.commission_percent', 10));
    }

    public function commissionAmount(float $tarifa, int $conductorId): float
    {
        $pct = $this->commissionPercent($conductorId);
        $minFee = (float) config('taxpiya.wallet.commission_min', 500);

        return max($minFee, round($tarifa * $pct / 100, 2));
    }

    /**
     * Liquida un viaje: descuenta pasajero, acredita conductor neto (tarifa - comisión plataforma).
     */
    public function settleCompletedTrip(object $viaje, int $conductorId): array
    {
        $viajeId = (int) $viaje->id;
        $tarifa = (float) ($viaje->tarifa_aplicada ?? 0);
        $moneda = $viaje->moneda ?? 'COP';
        $pasajeroId = (int) ($viaje->pasajero_id ?? 0);

        if ($tarifa <= 0 || $pasajeroId <= 0) {
            return ['ok' => false, 'message' => 'Tarifa o pasajero inválido'];
        }

        $fee = $this->commissionAmount($tarifa, $conductorId);
        $neto = round(max(0, $tarifa - $fee), 2);
        $pct = $this->commissionPercent($conductorId);

        DB::transaction(function () use ($viajeId, $tarifa, $neto, $fee, $pct, $moneda, $pasajeroId, $conductorId) {
            $this->ledger->debitoPagoViaje($pasajeroId, $viajeId, $tarifa, $moneda);

            if ($neto > 0) {
                $this->ledger->creditoIngresoViaje($conductorId, $viajeId, $neto, $moneda, "Ingreso neto viaje #{$viajeId} (comisión {$pct}%)");
            }

            $updates = [
                'valor_pagado'    => $tarifa,
                'pago_registrado' => 1,
            ];
            if (Schema::hasColumn('viajes', 'comision_plataforma')) {
                $updates['comision_plataforma'] = $fee;
            }
            if (Schema::hasColumn('viajes', 'updated_at')) {
                $updates['updated_at'] = now()->format('Y-m-d H:i:s');
            }

            DB::table('viajes')->where('id', $viajeId)->update($updates);
        });

        return [
            'ok'        => true,
            'tarifa'    => $tarifa,
            'comision'  => $fee,
            'neto'      => $neto,
            'pct'       => $pct,
        ];
    }

    public function passengerCanAfford(int $pasajeroUserId, float $monto): bool
    {
        return $this->ledger->getSaldoPasajero($pasajeroUserId) >= $monto;
    }
}
