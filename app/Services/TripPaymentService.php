<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TripPaymentService
{
    /** Cargo fijo al conductor por uso de la plataforma (COP) */
    const DRIVER_PLATFORM_FEE = 200;

    /** Puntos acreditados al pasajero por viaje completado */
    const PASSENGER_POINTS_PER_TRIP = 100;

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
        $pct    = $this->commissionPercent($conductorId);
        $minFee = (float) config('taxpiya.wallet.commission_min', 500);

        return max($minFee, round($tarifa * $pct / 100, 2));
    }

    /**
     * Liquida un viaje:
     *  - Pasajero: viaje GRATIS ($0) + 100 PTS acreditados a su billetera.
     *  - Conductor: se debitan $200 COP por uso de la plataforma.
     */
    public function settleCompletedTrip(object $viaje, int $conductorId): array
    {
        $viajeId    = (int) $viaje->id;
        $tarifa     = (float) ($viaje->tarifa_aplicada ?? 0);
        $moneda     = $viaje->moneda ?? 'COP';
        $pasajeroId = (int) ($viaje->pasajero_id ?? 0);

        if ($conductorId <= 0) {
            return ['ok' => false, 'message' => 'Conductor inválido'];
        }

        DB::transaction(function () use ($viajeId, $tarifa, $moneda, $pasajeroId, $conductorId) {

            // ── PASAJERO ─────────────────────────────────────────────────────
            if ($pasajeroId > 0) {
                // Cobro $0 — el viaje es gratuito
                $this->ledger->debitoPagoViaje($pasajeroId, $viajeId, 0, $moneda);

                // Acreditar 100 PTS en la cuenta del pasajero
                $cuentaPasajero = $this->ledger->ensureCuenta('pasajero', $pasajeroId);
                if ($cuentaPasajero) {
                    $idempPts = "puntos_viaje_{$viajeId}_p{$pasajeroId}";
                    // Solo acreditar si no fue ya procesado
                    $yaAcreditado = DB::table('wallet_movimientos')
                        ->where('idempotencia', $idempPts)
                        ->where('anulado', 0)
                        ->exists();

                    if (!$yaAcreditado) {
                        $this->ledger->registrarMovimientoCuenta((int) $cuentaPasajero->id, [
                            'sentido'      => 'credito',
                            'motivo'       => 'puntos_viaje',
                            'tipo_operacion'=> 'puntos_viaje',
                            'monto'        => self::PASSENGER_POINTS_PER_TRIP,
                            'moneda'       => 'PTS',
                            'viaje_id'     => $viajeId,
                            'descripcion'  => "Puntos por viaje #{$viajeId}",
                            'idempotencia' => $idempPts,
                        ]);
                    }
                }
            }

            // ── CONDUCTOR ─────────────────────────────────────────────────────
            // Descontar $200 COP por uso de la plataforma (idempotente)
            $cuentaConductor = $this->ledger->ensureCuenta('conductor', $conductorId);
            if ($cuentaConductor) {
                $idempFee = "cargo_plataforma_viaje_{$viajeId}";
                $yaDebito = DB::table('wallet_movimientos')
                    ->where('idempotencia', $idempFee)
                    ->where('anulado', 0)
                    ->exists();

                if (!$yaDebito) {
                    $this->ledger->registrarMovimientoCuenta((int) $cuentaConductor->id, [
                        'sentido'        => 'debito',
                        'motivo'         => 'debito_termino',
                        'tipo_operacion' => 'debito_termino',
                        'monto'          => self::DRIVER_PLATFORM_FEE,
                        'moneda'         => 'COP',
                        'viaje_id'       => $viajeId,
                        'descripcion'    => "Cargo plataforma viaje #{$viajeId}",
                        'idempotencia'   => $idempFee,
                        'allow_negative' => false,
                    ]);
                }
            }

            // ── Marcar viaje como pagado ──────────────────────────────────────
            $updates = [
                'valor_pagado'    => $tarifa,
                'pago_registrado' => 1,
            ];
            if (Schema::hasColumn('viajes', 'comision_plataforma')) {
                $updates['comision_plataforma'] = self::DRIVER_PLATFORM_FEE;
            }
            if (Schema::hasColumn('viajes', 'updated_at')) {
                $updates['updated_at'] = now()->format('Y-m-d H:i:s');
            }

            DB::table('viajes')->where('id', $viajeId)->update($updates);
        });

        return [
            'ok'              => true,
            'tarifa'          => $tarifa,
            'cargo_conductor' => self::DRIVER_PLATFORM_FEE,
            'puntos_pasajero' => self::PASSENGER_POINTS_PER_TRIP,
        ];
    }

    public function passengerCanAfford(int $pasajeroUserId, float $monto): bool
    {
        return true; // Pasajero siempre puede viajar (viaje gratuito)
    }
}
