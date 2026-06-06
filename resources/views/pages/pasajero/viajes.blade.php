@php
$pageTitle = 'Mis viajes';
$estadoLabel = [
    'buscando' => 'Buscando conductor',
    'asignado' => 'Conductor asignado',
    'en_camino' => 'En camino',
    'llego' => 'Conductor llegó',
    'iniciado' => 'En curso',
    'terminado' => 'Finalizado',
    'cancelado_pasajero' => 'Cancelado por ti',
    'cancelado_conductor' => 'Cancelado por conductor',
    'cancelado_sistema' => 'Cancelado',
    'no_show' => 'No show',
];
@endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mis viajes</h1>
    </header>

    @forelse($viajes as $v)
        <div class="txp-mobile-card txp-trip-card">
            <div class="txp-trip-top">
                <span class="txp-trip-badge">{{ $estadoLabel[$v->estado] ?? ucfirst(str_replace('_', ' ', $v->estado ?? '—')) }}</span>
                <span class="txp-trip-date">{{ $v->created_at ?? '' }}</span>
            </div>
            <div class="txp-trip-line"><i class="fa-solid fa-circle-dot text-success"></i> {{ $v->origen_texto ?? 'Origen' }}</div>
            <div class="txp-trip-line"><i class="fa-solid fa-location-dot text-danger"></i> {{ $v->destino_texto ?? 'Destino' }}</div>
            @if(!empty($v->tarifa_final) || !empty($v->tarifa_estimada))
                <div class="txp-trip-price">${{ number_format((float)($v->tarifa_final ?: $v->tarifa_estimada), 0, ',', '.') }}</div>
            @endif
        </div>
    @empty
        <div class="txp-mobile-card txp-empty">
            <i class="fa-solid fa-taxi fa-2x mb-3"></i>
            <p class="mb-0">Aún no tienes viajes. ¡Pide tu primer taxi desde el mapa!</p>
            <a href="{{ route('home') }}" class="txp-mobile-btn mt-3">Ir al mapa</a>
        </div>
    @endforelse
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=1">
@endsection
