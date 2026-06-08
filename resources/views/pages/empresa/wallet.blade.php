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

    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="txp-mobile-card h-100">
                <h3 class="h6">Depositar</h3>
                <form method="post" action="{{ route('empresa.wallet.depositar') }}">
                    @csrf
                    <input type="number" name="monto" class="form-control txp-input-dark mb-2" min="1000" step="1000" required placeholder="Monto">
                    <button class="txp-mobile-btn txp-mobile-btn--empresa w-100 border-0" type="submit"><i class="fa-solid fa-plus"></i> Depositar</button>
                </form>
            </div>
        </div>
        <div class="col-6">
            <div class="txp-mobile-card h-100">
                <h3 class="h6">Retirar</h3>
                <form method="post" action="{{ route('empresa.wallet.retirar') }}">
                    @csrf
                    <input type="number" name="monto" class="form-control txp-input-dark mb-2" min="10000" step="1000" required placeholder="Monto">
                    <button class="txp-mobile-btn w-100 border-0" type="submit" style="background:#334155;color:#fff;"><i class="fa-solid fa-arrow-up"></i> Retirar</button>
                </form>
            </div>
        </div>
    </div>

    <p class="small text-muted px-1">Usa esta billetera para pagar a los conductores de tu flota desde el detalle de cada taxi.</p>

    <h2 class="txp-section-title">Movimientos</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])

    @include('pages.empresa.partials.nav')
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
