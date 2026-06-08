@php $pageTitle = 'Mi billetera'; @endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <h1>Mi billetera</h1>
    </header>

    @if(session('wallet_ok'))<div class="txp-mobile-card txp-alert-success">{{ session('wallet_ok') }}</div>@endif
    @if(session('wallet_error'))<div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">{{ session('wallet_error') }}</div>@endif

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo disponible</div>
        <div class="txp-wallet-amount">${{ number_format((float)($cuenta->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">{{ $cuenta->moneda ?? 'COP' }} · Solo depósitos</div>
    </div>

    @if($cuenta && (int)$cuenta->puede_depositar)
    <div class="txp-mobile-card">
        <h2 class="txp-section-title mt-0">Depositar</h2>
        <form method="post" action="{{ route('pasajero.wallet.depositar') }}">
            @csrf
            <div class="txp-profile-edit-field mb-3">
                <label>Monto (COP)</label>
                <input type="number" name="monto" class="form-control txp-input-dark" min="1000" step="1000" required placeholder="Ej: 50000">
            </div>
            <button type="submit" class="txp-mobile-btn w-100 border-0"><i class="fa-solid fa-plus"></i> Depositar</button>
        </form>
        <p class="small text-muted mt-2 mb-0">La pasarela de pago se conectará próximamente. Por ahora el depósito queda registrado en tu historial.</p>
    </div>
    @endif

    <h2 class="txp-section-title">Movimientos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
@endsection
