@php $pageTitle = 'Billetera conductor'; @endphp
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.flota') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Flota</a>
        <h1>{{ $conductor->name }}</h1>
        <p class="txp-empresa-sub mb-0">{{ $conductor->placa ?? '—' }} · {{ $conductor->telefono }}</p>
    </header>

    @if(session('wallet_ok'))<div class="txp-mobile-card txp-alert-success">{{ session('wallet_ok') }}</div>@endif
    @if(session('wallet_error'))<div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">{{ session('wallet_error') }}</div>@endif

    <div class="txp-mobile-card txp-wallet-hero">
        <div class="txp-wallet-label">Saldo del conductor</div>
        <div class="txp-wallet-amount">${{ number_format((float)($cuenta->saldo_actual ?? 0), 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">Solo lectura para el conductor · Tú gestionas los pagos</div>
    </div>

    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>Ingresos por viajes</span><strong>${{ number_format($resumen['ingresos_viajes'] ?? 0, 0, ',', '.') }}</strong></div>
        <div class="txp-info-row"><span>Pagos de empresa</span><strong>${{ number_format($resumen['pagos_empresa'] ?? 0, 0, ',', '.') }}</strong></div>
        <div class="txp-info-row"><span>Comisiones</span><strong>${{ number_format($resumen['comisiones'] ?? 0, 0, ',', '.') }}</strong></div>
    </div>

    <div class="txp-mobile-card">
        <h2 class="txp-section-title mt-0">Pagar al conductor</h2>
        <form method="post" action="{{ route('empresa.flota.pagar', $conductor->id) }}">
            @csrf
            <div class="txp-profile-edit-field mb-2">
                <label>Monto a pagar (COP)</label>
                <input type="number" name="monto" class="form-control txp-input-dark" min="1000" step="1000" required>
            </div>
            <div class="txp-profile-edit-field mb-3">
                <label>Nota (opcional)</label>
                <input type="text" name="nota" class="form-control txp-input-dark" maxlength="255" placeholder="Ej: Pago semana 24">
            </div>
            <button type="submit" class="txp-mobile-btn txp-mobile-btn--empresa w-100 border-0"><i class="fa-solid fa-money-bill-transfer"></i> Registrar pago</button>
        </form>
        <p class="small text-muted mt-2 mb-0">El monto se debita de la billetera de la empresa y se acredita al conductor.</p>
    </div>

    <h2 class="txp-section-title">Viajes terminados</h2>
    @forelse($viajes as $v)
        <div class="txp-mobile-card">
            <div class="txp-info-row"><span>Viaje #{{ $v->id }}</span><strong>${{ number_format((float)($v->tarifa_aplicada ?? 0), 0, ',', '.') }}</strong></div>
            <div class="txp-mov-sub">{{ $v->created_at }}</div>
        </div>
    @empty
        <div class="txp-empty">Sin viajes terminados.</div>
    @endforelse

    <h2 class="txp-section-title">Historial billetera</h2>
    @include('components.wallet-movimientos-list', ['movimientos' => $movimientos])

    @include('pages.empresa.partials.nav')
</div>
@endsection
@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
