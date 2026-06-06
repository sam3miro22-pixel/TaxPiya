@php $pageTitle = 'Afiliación pendiente'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <div class="txp-mobile-card text-center py-5">
        <i class="fa-solid fa-hourglass-half fa-3x mb-3" style="color:#a78bfa;"></i>
        <h1 class="h4">Afiliación en revisión</h1>
        <p class="txp-empresa-sub">{{ $empresa->nombre_comercial }} está pendiente de aprobación por el equipo TaxPiya.</p>
        <a href="{{ route('logout') }}" class="txp-mobile-btn txp-mobile-btn--ghost mt-3">Cerrar sesión</a>
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
