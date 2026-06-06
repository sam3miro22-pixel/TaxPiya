@php $pageTitle = 'Mi cuenta'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mi cuenta</h1>
    </header>

    <div class="txp-mobile-card txp-profile-hero">
        <div class="txp-profile-photo txp-profile-photo--placeholder txp-profile-photo--driver"><i class="fa-solid fa-id-card"></i></div>
        <h2>{{ $user->name }}</h2>
        <p class="text-muted mb-0">Conductor TaxPiya</p>
    </div>

    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>Celular</span><strong>{{ $user->telefono ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Email</span><strong>{{ $user->email ?? '—' }}</strong></div>
        @if($conductor)
            <div class="txp-info-row"><span>Estado</span><strong>{{ (int)($conductor->disponible ?? 0) === 1 ? 'Disponible' : 'Offline' }}</strong></div>
        @endif
        @if($vehiculo)
            <div class="txp-info-row"><span>Vehículo</span><strong>{{ trim(($vehiculo->marca ?? '') . ' ' . ($vehiculo->linea ?? '')) ?: '—' }}</strong></div>
            <div class="txp-info-row"><span>Placa</span><strong>{{ $vehiculo->placa ?? '—' }}</strong></div>
        @endif
    </div>

    <div class="txp-mobile-actions">
        <a href="{{ route('logout') }}" class="txp-mobile-btn txp-mobile-btn--ghost"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=1">
@endsection
