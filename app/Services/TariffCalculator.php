<?php

namespace App\Services;

use App\Support\GeoDistance;

final class TariffCalculator
{
    /**
     * @return array{monto: float, km: float, desglose: string}
     */
    public static function calculate(object $tarifa, ?float $oLat, ?float $oLng, ?float $dLat, ?float $dLng): array
    {
        $km = 0.0;
        if ($oLat !== null && $oLng !== null && $dLat !== null && $dLng !== null) {
            $km = GeoDistance::km($oLat, $oLng, $dLat, $dLng);
        }

        $fijo = (float) ($tarifa->monto_fijo ?? 0);
        $base = (float) ($tarifa->tarifa_base ?? $fijo);
        $porKm = (float) ($tarifa->tarifa_por_km ?? 0);
        $minima = (float) ($tarifa->tarifa_minima ?? 0);

        if ($porKm > 0 && $km > 0) {
            $monto = $base + ($km * $porKm);
            if ($minima > 0) {
                $monto = max($minima, $monto);
            }
            $desglose = sprintf('Base $%s + %.1f km × $%s', number_format($base, 0, ',', '.'), $km, number_format($porKm, 0, ',', '.'));
        } else {
            $monto = $fijo > 0 ? $fijo : $base;
            $desglose = 'Tarifa fija';
        }

        return [
            'monto'     => round($monto, 2),
            'km'        => round($km, 2),
            'desglose'  => $desglose,
        ];
    }

    public static function findActiveTariff(string $categoria, ?string $ciudad): ?object
    {
        $hoy = now()->toDateString();
        $base = \Illuminate\Support\Facades\DB::table('tarifas')
            ->where('categoria', $categoria)
            ->where('activa', 1)
            ->where('vigente_desde', '<=', $hoy)
            ->where(function ($w) use ($hoy) {
                $w->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>=', $hoy);
            })
            ->orderBy('prioridad')
            ->orderByDesc('version');

        $tarifa = null;
        if ($ciudad) {
            $tarifa = (clone $base)->where('scope', 'ciudad')->where('ciudad', $ciudad)->first();
        }
        if (!$tarifa) {
            $tarifa = (clone $base)->where('scope', 'global')->first();
        }
        if (!$tarifa) {
            $tarifa = (clone $base)->first();
        }

        return $tarifa;
    }
}
