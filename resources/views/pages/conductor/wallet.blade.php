@php
$pageTitle = 'Mi wallet';
$motivoLabel = [
    'recarga' => 'Recarga',
    'ajuste' => 'Ajuste',
    'debito_asignacion' => 'Asignación',
    'debito_aceptacion' => 'Aceptación viaje',
    'debito_inicio' => 'Inicio viaje',
    'debito_termino' => 'Comisión viaje',
    'reversa' => 'Reversa',
    'bono' => 'Bono',
    'penalidad' => 'Penalidad',
];
@endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mi wallet</h1>
    </header>

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo disponible</div>
        <div class="txp-wallet-amount">${{ number_format((float)($saldo->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">
            Mínimo operativo: ${{ number_format((float)($saldo->min_operativo ?? 0), 0, ',', '.') }}
            · {{ $saldo->moneda ?? 'COP' }}
        </div>
        @if((int)($saldo->bloqueado ?? 0) === 1)
            <div class="txp-wallet-alert"><i class="fa-solid fa-lock"></i> Wallet bloqueada</div>
        @endif
    </div>

    <div class="txp-mobile-card">
        <p class="txp-wallet-info mb-0">
            <i class="fa-solid fa-circle-info me-2"></i>
            La comisión se descuenta automáticamente al finalizar cada viaje.
            Para recargar saldo, contacta al administrador TaxPiya.
        </p>
    </div>

    <h2 class="txp-section-title">Últimos movimientos</h2>

    @forelse($movimientos as $m)
        <div class="txp-mobile-card txp-mov-card">
            <div class="txp-mov-top">
                <span class="txp-mov-badge txp-mov-badge--{{ $m->sentido }}">
                    {{ $m->sentido === 'credito' ? '+' : '−' }}${{ number_format((float)$m->monto, 0, ',', '.') }}
                </span>
                <span class="txp-trip-date">{{ $m->created_at ?? '' }}</span>
            </div>
            <div class="txp-mov-desc">{{ $motivoLabel[$m->motivo] ?? $m->motivo }}</div>
            @if(!empty($m->descripcion))
                <div class="txp-mov-sub">{{ $m->descripcion }}</div>
            @endif
            @if(!is_null($m->saldo_despues))
                <div class="txp-mov-sub">Saldo: ${{ number_format((float)$m->saldo_despues, 0, ',', '.') }}</div>
            @endif
        </div>
    @empty
        <div class="txp-mobile-card txp-empty">
            <p class="mb-0">Sin movimientos aún.</p>
        </div>
    @endforelse
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
@endsection
