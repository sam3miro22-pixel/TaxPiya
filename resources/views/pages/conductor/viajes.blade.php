@php
$pageTitle = 'Mis viajes';
$estadoLabel = [
    'buscando' => 'Buscando pasajero',
    'asignado' => 'Asignado',
    'en_camino' => 'En camino',
    'llego' => 'Llegué al punto',
    'iniciado' => 'En curso',
    'terminado' => 'Finalizado',
    'cancelado_pasajero' => 'Cancelado por pasajero',
    'cancelado_conductor' => 'Cancelado por ti',
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
            @php $tarifa = (float)($v->tarifa_aplicada ?: $v->tarifa_final ?: $v->tarifa_estimada ?: 0); @endphp
            @if($tarifa > 0)
                <div class="txp-trip-price">${{ number_format($tarifa, 0, ',', '.') }}</div>
            @endif
        </div>
    @empty
        <div class="txp-mobile-card txp-empty">
            <i class="fa-solid fa-route fa-2x mb-3"></i>
            <p class="mb-0">Aún no tienes viajes. Conéctate y acepta solicitudes desde el mapa.</p>
            <a href="{{ route('home') }}" class="txp-mobile-btn mt-3">Ir al mapa</a>
        </div>
    @endforelse
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
@endsection
