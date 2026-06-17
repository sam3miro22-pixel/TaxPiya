@php $pageTitle = 'Mi billetera'; @endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <h1>Mi billetera</h1>
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

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo disponible</div>
        <div class="txp-wallet-amount">${{ number_format((float)($cuenta->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">{{ $cuenta->moneda ?? 'COP' }} · Solo depósitos</div>
    </div>

    @if($cuenta && (int)$cuenta->puede_depositar)
        @include('components.wallet-nequi-deposit', ['action' => route('pasajero.wallet.depositar')])
    @endif

    @include('components.wallet-solicitudes-list', ['solicitudes' => $solicitudes ?? []])

    <h2 class="txp-section-title">Movimientos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
<style>
.txp-nequi-card { border: 1px solid rgba(218, 0, 130, 0.35); }
.txp-nequi-info { background: rgba(218, 0, 130, 0.08); border-radius: 12px; padding: 12px; }
.txp-nequi-info .txp-info-row strong { color: #f9a8d4; }
</style>
@endsection
