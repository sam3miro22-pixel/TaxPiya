@php $pageTitle = 'Billetera empresa'; @endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.dashboard') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Panel</a>
        <h1>Billetera</h1>
    </header>

    @if(session('wallet_ok'))<div class="txp-mobile-card txp-alert-success">{{ session('wallet_ok') }}</div>@endif
    @if(session('wallet_error'))<div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">{{ session('wallet_error') }}</div>@endif

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo empresa</div>
        <div class="txp-wallet-amount">${{ number_format((float)($cuenta->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">{{ $empresa->nombre_comercial }} · Depósitos y retiros</div>
    </div>

    @include('components.wallet-nequi-deposit', [
        'action' => route('empresa.wallet.depositar'),
        'btnClass' => 'txp-mobile-btn txp-mobile-btn--empresa w-100 border-0',
    ])

    <div class="txp-mobile-card mb-3">
        <h3 class="h6">Solicitar retiro</h3>
        <form method="post" action="{{ route('empresa.wallet.retirar') }}">
            @csrf
            <input type="number" name="monto" class="form-control txp-input-dark mb-2" min="10000" step="1000" required placeholder="Monto mín. $10.000">
            <button class="txp-mobile-btn w-100 border-0" type="submit" style="background:#334155;color:#fff;"><i class="fa-solid fa-arrow-up"></i> Solicitar retiro</button>
        </form>
        <p class="small text-muted mt-2 mb-0">Los retiros requieren aprobación del administrador.</p>
    </div>

    <p class="small text-muted px-1">Usa esta billetera para pagar a los conductores de tu flota desde el detalle de cada taxi.</p>

    @include('components.wallet-solicitudes-list', ['solicitudes' => $solicitudes ?? []])

    <h2 class="txp-section-title">Movimientos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])

    @include('pages.empresa.partials.nav')
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
