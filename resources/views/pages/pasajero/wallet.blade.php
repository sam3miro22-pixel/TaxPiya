@php
    $pageTitle   = 'Mis puntos';
    $puntos      = (int) ($cuenta->saldo_actual ?? 0);  // billetera PTS del pasajero
@endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <h1>Mis Puntos</h1>
    </header>

    @if(session('wallet_ok'))<div class="txp-mobile-card txp-alert-success"><i class="fa-solid fa-circle-check me-1"></i> {{ session('wallet_ok') }}</div>@endif
    @if(session('wallet_error'))<div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('wallet_error') }}</div>@endif
    @if($errors->any())
        <div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">
            @foreach($errors->all() as $err)
                <div><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- ── Tarjeta principal de puntos ──────────────────────────────────── --}}
    <div class="txp-mobile-card txp-pts-hero">
        <div class="txp-pts-trophy"><i class="fa-solid fa-trophy"></i></div>
        <div class="txp-pts-label">Puntos acumulados</div>
        <div class="txp-pts-amount">{{ number_format($puntos, 0, ',', '.') }} <span class="txp-pts-unit">pts</span></div>
        <div class="txp-pts-meta">Cada viaje = <strong>100 pts</strong> &nbsp;|&nbsp; Acumula y gana</div>
    </div>

    {{-- ── Banner informativo fin de año ────────────────────────────────── --}}
    <div class="txp-mobile-card txp-pts-promo">
        <div class="txp-pts-promo-icon"><i class="fa-solid fa-gift"></i></div>
        <div class="txp-pts-promo-body">
            <div class="txp-pts-promo-title">¡Participa en los premios de fin de año!</div>
            <p class="txp-pts-promo-text">
                La billetera es tu sistema de puntos. Por cada viaje completado acumulas
                <strong>100 pts</strong>. Al final del año, los pasajeros con más puntos
                participan automáticamente en el sorteo de <strong>espectaculares premios</strong>
                que Taxpiya regala a su comunidad. ¡Sigue viajando y gana!
            </p>
        </div>
    </div>

    <h2 class="txp-section-title">Historial de puntos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos, 'modosPuntos' => true])
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=3">
<style>
/* ── Tarjeta de puntos hero ──────────────────────────────────── */
.txp-pts-hero {
    text-align: center;
    background: linear-gradient(135deg, #1c2541 0%, #3a506b 100%);
    border: 1px solid rgba(255, 209, 102, 0.4);
    padding: 28px 20px 22px;
    border-radius: 20px;
}
.txp-pts-trophy {
    font-size: 40px;
    color: #ffd166;
    margin-bottom: 8px;
    filter: drop-shadow(0 0 12px rgba(255,209,102,.5));
}
.txp-pts-label {
    font-size: 13px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 6px;
}
.txp-pts-amount {
    font-size: 48px;
    font-weight: 900;
    color: #ffd166;
    line-height: 1;
    margin-bottom: 6px;
}
.txp-pts-unit {
    font-size: 22px;
    font-weight: 600;
    color: #ff9f1c;
}
.txp-pts-meta {
    font-size: 13px;
    color: #cbd5e1;
}
.txp-pts-meta strong { color: #ffd166; }

/* ── Banner promo fin de año ───────────────────────────────── */
.txp-pts-promo {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    background: linear-gradient(135deg, rgba(255,159,28,.12), rgba(255,209,102,.06));
    border: 1px solid rgba(255,209,102,.3);
    border-radius: 16px;
    padding: 18px 16px;
}
.txp-pts-promo-icon {
    font-size: 28px;
    color: #ffd166;
    flex-shrink: 0;
    margin-top: 2px;
    filter: drop-shadow(0 0 8px rgba(255,209,102,.4));
}
.txp-pts-promo-title {
    font-weight: 700;
    font-size: 15px;
    color: #ffd166;
    margin-bottom: 6px;
}
.txp-pts-promo-text {
    font-size: 13px;
    color: #cbd5e1;
    line-height: 1.6;
    margin: 0;
}
.txp-pts-promo-text strong { color: #ffd166; }
</style>
@endsection
