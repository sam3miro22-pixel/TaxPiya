@php $pageTitle = 'Mi perfil'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mi perfil</h1>
    </header>

    <div class="txp-mobile-card txp-profile-hero">
        @if(!empty($user->fotoperfil))
            <img src="{{ asset($user->fotoperfil) }}" alt="Foto" class="txp-profile-photo">
        @else
            <div class="txp-profile-photo txp-profile-photo--placeholder"><i class="fa-solid fa-user"></i></div>
        @endif
        <h2>{{ $user->name }}</h2>
        <p class="text-muted mb-0">Pasajero TaxPiya</p>
    </div>

    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>Celular</span><strong>{{ $user->telefono ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Email</span><strong>{{ $user->email ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Estado</span><strong>{{ (int)($user->estado ?? 1) === 1 ? 'Activo' : 'Inactivo' }}</strong></div>
    </div>

    <div class="txp-mobile-actions">
        <a href="{{ route('pasajero.viajes') }}" class="txp-mobile-btn"><i class="fa-solid fa-route"></i> Mis viajes</a>
        <a href="{{ route('logout') }}" class="txp-mobile-btn txp-mobile-btn--ghost"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=1">
@endsection
