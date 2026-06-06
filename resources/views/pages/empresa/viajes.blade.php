@php $pageTitle = 'Viajes de la flota'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.dashboard') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Panel</a>
        <h1>Viajes de la flota</h1>
    </header>

    @forelse($viajes as $v)
        <div class="txp-mobile-card txp-trip-card">
            <div class="txp-trip-top">
                <span class="txp-trip-badge txp-trip-badge--empresa">{{ ucfirst(str_replace('_', ' ', $v->estado ?? '')) }}</span>
                <span class="txp-trip-date">{{ $v->created_at }}</span>
            </div>
            <div class="txp-trip-line"><i class="fa-solid fa-user me-1"></i> {{ $v->conductor_nombre }} · {{ $v->placa ?? '—' }}</div>
            <div class="txp-trip-line"><i class="fa-solid fa-location-dot me-1"></i> {{ Str::limit($v->origen_direccion ?? 'Origen', 45) }}</div>
            <div class="txp-trip-line"><i class="fa-solid fa-flag-checkered me-1"></i> {{ Str::limit($v->destino_direccion ?? 'Destino', 45) }}</div>
            @if($v->estado === 'terminado')
                <div class="txp-trip-price txp-trip-price--empresa">${{ number_format((float)($v->tarifa_aplicada ?? 0), 0, ',', '.') }}</div>
            @endif
        </div>
    @empty
        <div class="txp-empty">Aún no hay viajes registrados para tu flota.</div>
    @endforelse

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
