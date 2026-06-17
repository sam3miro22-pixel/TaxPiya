@php $pageTitle = 'Mi flota'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.dashboard') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Panel</a>
        <h1>Mi flota</h1>
    </header>

    @if(session('flota_ok'))
        <div class="txp-mobile-card txp-alert-success">{{ session('flota_ok') }}</div>
    @endif

    <div class="txp-mobile-actions mb-3">
        <a href="{{ route('empresa.flota.nuevo') }}" class="txp-mobile-btn txp-mobile-btn--empresa">
            <i class="fa-solid fa-plus"></i> Agregar taxi
        </a>
    </div>

    @forelse($items as $t)
        <div class="txp-mobile-card txp-empresa-taxi">
            <div class="txp-empresa-taxi__top">
                <strong>{{ $t->placa ?? '—' }}</strong>
                @if((int)$t->disponible === 1)
                    <span class="txp-empresa-badge txp-empresa-badge--online">En línea</span>
                @else
                    <span class="txp-empresa-badge">Offline</span>
                @endif
            </div>
            <div class="txp-empresa-taxi__meta">{{ $t->nombre }}</div>
            <div class="txp-info-row"><span>Vehículo</span><strong>{{ trim(($t->marca ?? '') . ' ' . ($t->linea ?? '')) ?: '—' }}</strong></div>
            <div class="txp-info-row"><span>Celular</span><strong>{{ $t->telefono ?? '—' }}</strong></div>
            <div class="txp-info-row"><span>Viajes</span><strong>{{ (int)$t->total_viajes }}</strong></div>
            <div class="txp-info-row"><span>Wallet</span><strong>${{ number_format((float)$t->saldo, 0, ',', '.') }}</strong></div>
            <a href="{{ route('empresa.flota.wallet', $t->id) }}" class="txp-mobile-btn txp-mobile-btn--empresa w-100 mt-3" style="text-align:center;display:inline-block;text-decoration:none;">
                <i class="fa-solid fa-chart-line"></i> Ver ingresos y pagar
            </a>
        </div>
    @empty
        <div class="txp-empty">No hay taxis en tu flota todavía.</div>
    @endforelse

    @if(isset($vehiculos) && $vehiculos->isNotEmpty())
        <h2 class="txp-section-title mt-4">Agregar conductor a un taxi</h2>
        <div class="txp-mobile-card">
            <p class="small text-muted mb-3">Un mismo taxi puede tener varios conductores (turnos). Registra aquí un conductor adicional.</p>
            @foreach($vehiculos as $veh)
                <details class="mb-3">
                    <summary class="fw-bold">{{ $veh->placa }} · {{ trim(($veh->marca ?? '') . ' ' . ($veh->linea ?? '')) ?: 'Taxi' }}</summary>
                    <form method="post" action="{{ route('empresa.flota.asignar', $veh->id) }}" class="mt-2">
                        @csrf
                        <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre conductor" required>
                        <input type="tel" name="telefono" class="form-control mb-2" placeholder="Celular" required>
                        <input type="email" name="email" class="form-control mb-2" placeholder="Correo (opcional)">
                        <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required minlength="6">
                        <button type="submit" class="txp-mobile-btn txp-mobile-btn--empresa w-100">
                            <i class="fa-solid fa-user-plus"></i> Agregar conductor
                        </button>
                    </form>
                </details>
            @endforeach
        </div>
    @endif

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
