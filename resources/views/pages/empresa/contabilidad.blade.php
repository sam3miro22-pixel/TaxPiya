@php $pageTitle = 'Contabilidad'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.dashboard') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Panel</a>
        <h1>Contabilidad</h1>
        <p class="txp-empresa-sub">Resumen financiero sincronizado con Taxpiya</p>
    </header>

    <div class="txp-empresa-stats">
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">{{ $stats['viajes_mes'] }}</span>
            <span class="txp-empresa-stat__lbl">Viajes mes</span>
        </div>
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">${{ number_format($stats['ingresos_mes'], 0, ',', '.') }}</span>
            <span class="txp-empresa-stat__lbl">Ingresos mes</span>
        </div>
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">${{ number_format($stats['wallet_total'], 0, ',', '.') }}</span>
            <span class="txp-empresa-stat__lbl">Wallet flota</span>
        </div>
    </div>

    <div class="txp-mobile-card">
        <div class="txp-wallet-label">Viajes hoy</div>
        <div class="txp-empresa-wallet__amount">{{ $stats['viajes_hoy'] }}</div>
        <div class="txp-wallet-meta">{{ $stats['conductores'] }} conductores · Comisiones mes: ${{ number_format($stats['comisiones_mes'], 0, ',', '.') }}</div>
    </div>

    <h2 class="txp-section-title">Últimos viajes</h2>
    @forelse($viajes as $v)
        <div class="txp-mobile-card txp-empresa-taxi">
            <div class="txp-empresa-taxi__top">
                <strong>#{{ $v->id }}</strong>
                <span class="txp-empresa-badge">{{ $v->estado }}</span>
            </div>
            <div class="txp-empresa-taxi__meta">{{ $v->conductor }} → {{ $v->pasajero }}</div>
            <div class="txp-info-row"><span>Tarifa</span><strong>${{ number_format((float)($v->tarifa_aplicada ?? 0), 0, ',', '.') }} {{ $v->moneda ?? 'COP' }}</strong></div>
            <div class="txp-info-row"><span>Fecha</span><strong>{{ $v->created_at }}</strong></div>
        </div>
    @empty
        <div class="txp-empty">Sin viajes registrados este periodo.</div>
    @endforelse

    <h2 class="txp-section-title mt-4">Movimientos wallet</h2>
    @forelse($movimientos as $m)
        <div class="txp-mobile-card">
            <div class="txp-info-row"><span>{{ $m->conductor_nombre }}</span><strong class="{{ $m->sentido === 'credito' ? 'text-success' : 'text-danger' }}">{{ $m->sentido === 'credito' ? '+' : '-' }}${{ number_format((float)$m->monto, 0, ',', '.') }}</strong></div>
            <div class="txp-info-row"><span>{{ $m->motivo }}</span><strong>{{ $m->created_at }}</strong></div>
        </div>
    @empty
        <div class="txp-empty">Sin movimientos recientes.</div>
    @endforelse

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
