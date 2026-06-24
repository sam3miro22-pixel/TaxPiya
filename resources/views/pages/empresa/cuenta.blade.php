@php $pageTitle = 'Mi empresa'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.dashboard') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Panel</a>
        <h1>Mi empresa</h1>
    </header>

    <div class="txp-mobile-card txp-profile-hero">
        <div class="txp-profile-photo txp-profile-photo--empresa mx-auto">
            <i class="fa-solid fa-building"></i>
        </div>
        <h2 class="h5 mb-1">{{ $empresa->nombre_comercial }}</h2>
        <p class="txp-empresa-sub mb-0">{{ $empresa->razon_social ?? '' }}</p>
    </div>

    @include('components.referral-share-card', ['referral' => $referral ?? [], 'referralShareUrl' => $referralShareUrl ?? null])

    <div class="txp-mobile-card">
        <div class="txp-info-row"><span>NIT</span><strong>{{ $empresa->nit ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Ciudad</span><strong>{{ $empresa->ciudad ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Teléfono</span><strong>{{ $empresa->telefono ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Correo</span><strong>{{ $empresa->email ?? '—' }}</strong></div>
        <div class="txp-info-row"><span>Estado</span><strong>{{ ucfirst($empresa->estado ?? '') }}</strong></div>
        <div class="txp-info-row"><span>Contacto</span><strong>{{ $user->name }}</strong></div>
    </div>

    @include('components.change-password-form')

    <div class="txp-mobile-actions">
        <a href="{{ route('logout') }}" class="txp-mobile-btn txp-mobile-btn--ghost">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
        </a>
    </div>

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
