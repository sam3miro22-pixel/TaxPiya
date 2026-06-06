@php $pageTitle = 'Panel de flota'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head txp-empresa-head">
        <div class="txp-empresa-brand">
            <img src="{{ asset('images/logo.png') }}" alt="TaxPiya" class="txp-empresa-logo">
            <div>
                <div class="txp-empresa-tag">Mini empresa · Flota</div>
                <h1>{{ $empresa->nombre_comercial }}</h1>
            </div>
        </div>
        <p class="txp-empresa-sub">{{ $empresa->ciudad ?? 'Colombia' }} · {{ $stats['total_taxis'] }} taxi{{ $stats['total_taxis'] !== 1 ? 's' : '' }} registrado{{ $stats['total_taxis'] !== 1 ? 's' : '' }}</p>
    </header>

    <div class="txp-empresa-stats">
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">{{ $stats['disponibles'] }}</span>
            <span class="txp-empresa-stat__lbl">En línea</span>
        </div>
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">{{ $stats['viajes_hoy'] }}</span>
            <span class="txp-empresa-stat__lbl">Viajes hoy</span>
        </div>
        <div class="txp-empresa-stat">
            <span class="txp-empresa-stat__val">{{ $stats['viajes_mes'] }}</span>
            <span class="txp-empresa-stat__lbl">Este mes</span>
        </div>
    </div>

    <div class="txp-mobile-card txp-empresa-wallet">
        <div class="txp-wallet-label">Saldo wallet total de la flota</div>
        <div class="txp-empresa-wallet__amount">${{ number_format($stats['wallet_total'], 0, ',', '.') }}</div>
        <div class="txp-wallet-meta">Ingresos del mes: ${{ number_format($stats['ingresos_mes'], 0, ',', '.') }} COP</div>
    </div>

    <div class="txp-mobile-actions">
        <a href="{{ route('empresa.flota.nuevo') }}" class="txp-mobile-btn txp-mobile-btn--empresa">
            <i class="fa-solid fa-plus"></i> Registrar taxi y conductor
        </a>
        <a href="{{ route('empresa.flota') }}" class="txp-mobile-btn txp-mobile-btn--ghost">
            <i class="fa-solid fa-list"></i> Ver flota completa
        </a>
    </div>

    <h2 class="txp-section-title">Flota reciente</h2>

    @forelse($flotaReciente as $t)
        <div class="txp-mobile-card txp-empresa-taxi">
            <div class="txp-empresa-taxi__top">
                <strong>{{ $t->placa ?? 'Sin placa' }}</strong>
                @if((int)$t->disponible === 1 && (int)$t->estado_operitivo === 1)
                    <span class="txp-empresa-badge txp-empresa-badge--online">En línea</span>
                @elseif((int)$t->estado_operitivo === 1)
                    <span class="txp-empresa-badge">Offline</span>
                @else
                    <span class="txp-empresa-badge txp-empresa-badge--off">Inactivo</span>
                @endif
            </div>
            <div class="txp-empresa-taxi__meta">{{ $t->nombre }} · {{ trim(($t->marca ?? '') . ' ' . ($t->linea ?? '')) ?: 'Vehículo' }}</div>
        </div>
    @empty
        <div class="txp-empty">
            <i class="fa-solid fa-taxi fa-2x mb-3 d-block"></i>
            Aún no tienes taxis registrados.<br>
            <a href="{{ route('empresa.flota.nuevo') }}" class="txp-mobile-back">Registrar el primero</a>
        </div>
    @endforelse

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
