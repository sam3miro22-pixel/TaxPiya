@php $pageTitle = 'Registrar taxi'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page txp-mobile-page--empresa">
    <header class="txp-mobile-head">
        <a href="{{ route('empresa.flota') }}" class="txp-mobile-back txp-mobile-back--empresa"><i class="fa-solid fa-arrow-left"></i> Flota</a>
        <h1>Registrar taxi</h1>
        <p class="txp-empresa-sub mb-0">Crea el conductor y su vehículo bajo {{ $empresa->nombre_comercial }}</p>
    </header>

    @if($errors->any())
        <div class="txp-mobile-card" style="border-color:#f87171;color:#fecaca;">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('empresa.flota.store') }}" class="txp-mobile-card">
        @csrf
        <h2 class="txp-section-title mt-0">Conductor</h2>

        <div class="txp-profile-edit-field mb-3">
            <label>Nombre completo</label>
            <input type="text" name="nombre" class="form-control txp-input-dark" value="{{ old('nombre') }}" required>
        </div>
        <div class="txp-profile-edit-field mb-3">
            <label>Celular (login del conductor)</label>
            <input type="text" name="telefono" class="form-control txp-input-dark" value="{{ old('telefono') }}" placeholder="320 123 4567" required>
        </div>
        <div class="txp-profile-edit-field mb-3">
            <label>Correo (opcional)</label>
            <input type="email" name="email" class="form-control txp-input-dark" value="{{ old('email') }}">
        </div>
        <div class="txp-profile-edit-field mb-2">
            <label>Contraseña inicial</label>
            <input type="password" name="password" class="form-control txp-input-dark" required minlength="6">
        </div>
        <p class="small text-muted mb-4">El conductor ingresa en <strong>/conductor/login</strong> con su celular o correo y esta contraseña. También queda registrado en Firebase Auth.</p>

        <h2 class="txp-section-title">Vehículo</h2>

        <div class="txp-profile-edit-field mb-3">
            <label>Placa</label>
            <input type="text" name="placa" class="form-control txp-input-dark" value="{{ old('placa') }}" required maxlength="12">
        </div>
        <div class="txp-profile-edit-field mb-3">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control txp-input-dark" value="{{ old('marca') }}" placeholder="Hyundai">
        </div>
        <div class="txp-profile-edit-field mb-3">
            <label>Línea</label>
            <input type="text" name="linea" class="form-control txp-input-dark" value="{{ old('linea') }}" placeholder="Grand i10">
        </div>
        <div class="txp-profile-edit-field mb-3">
            <label>Año</label>
            <input type="number" name="modelo_anio" class="form-control txp-input-dark" value="{{ old('modelo_anio') }}" min="1990" max="2030">
        </div>
        <div class="txp-profile-edit-field mb-4">
            <label>Color</label>
            <input type="text" name="color" class="form-control txp-input-dark" value="{{ old('color', 'Amarillo') }}">
        </div>

        <button type="submit" class="txp-mobile-btn txp-mobile-btn--empresa w-100 border-0">
            <i class="fa-solid fa-check"></i> Guardar taxi
        </button>
    </form>

    @include('pages.empresa.partials.nav')
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-mobile-pages.css') }}?v=empresa1">
@endsection
