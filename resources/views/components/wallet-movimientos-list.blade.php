@php
$modosPuntos = $modosPuntos ?? false;

$motivoLabel = [
    'recarga'          => 'Recarga',
    'deposito'         => 'Depósito',
    'retiro'           => 'Retiro',
    'ajuste'           => 'Ajuste',
    'ingreso_viaje'    => 'Ingreso viaje',
    'pago_empresa'     => 'Pago de empresa',
    'pago_conductor'   => 'Pago a conductor',
    'debito_aceptacion'=> 'Aceptación viaje',
    'debito_termino'   => 'Comisión viaje',
    'debito_asignacion'=> 'Asignación',
    'puntos_viaje'     => 'Puntos por viaje',
    'bono'             => 'Bono',
    'bono_referido'    => 'Bono por referido',
    'penalidad'        => 'Penalidad',
];
@endphp
@forelse($movimientos as $m)
    @php
        $esPuntos = $modosPuntos && (($m->moneda ?? 'COP') === 'PTS' || ($m->motivo ?? '') === 'puntos_viaje');
        $simbolo  = $esPuntos ? '' : '$';
        $unidad   = $esPuntos ? ' pts' : '';
        $signo    = $m->sentido === 'credito' ? '+' : '−';
        $monto    = number_format((float)$m->monto, 0, ',', '.');
    @endphp
    <div class="txp-mobile-card txp-mov-card">
        <div class="txp-mov-top">
            <span class="txp-mov-badge txp-mov-badge--{{ $m->sentido }}">
                {{ $signo }}{{ $simbolo }}{{ $monto }}{{ $unidad }}
            </span>
            <span class="txp-trip-date">{{ $m->created_at ?? '' }}</span>
        </div>
        <div class="txp-mov-desc">{{ $motivoLabel[$m->motivo] ?? $m->motivo }}</div>
        @if(!empty($m->descripcion))
            <div class="txp-mov-sub">{{ $m->descripcion }}</div>
        @endif
        @if(!empty($m->estado) && $m->estado !== 'completado')
            <div class="txp-mov-sub">Estado: {{ $m->estado }}</div>
        @endif
        @if(!$esPuntos && !is_null($m->saldo_despues))
            <div class="txp-mov-sub">Saldo: ${{ number_format((float)$m->saldo_despues, 0, ',', '.') }}</div>
        @endif
        @if($esPuntos && !is_null($m->saldo_despues))
            <div class="txp-mov-sub">Total acumulado: {{ number_format((float)$m->saldo_despues, 0, ',', '.') }} pts</div>
        @endif
    </div>
@empty
    <div class="txp-mobile-card txp-empty"><p class="mb-0">Sin movimientos aún.</p></div>
@endforelse
