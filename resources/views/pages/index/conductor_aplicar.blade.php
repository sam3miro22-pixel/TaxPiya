@php $pageTitle = 'Registro conductor'; @endphp
@extends('layouts.auth')
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-card" style="max-width:520px;margin:0 auto;">
    <div class="txp-auth-brand">
        @include('components.taxpiya-logo')
        <h1>Registro conductor</h1>
        <p>Completa el formulario. Tu cuenta quedará en revisión hasta aprobación del administrador.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('conductor.aplicar_store') }}" class="txp-auth-form">
        @csrf
        <h2 class="h6 mt-2">Datos personales</h2>
        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cédula</label>
            <input type="text" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Celular</label>
            <input type="tel" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="col-6">
                <label class="form-label">Confirmar</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', 'Medellín') }}" required>
        </div>

        <h2 class="h6 mt-3">Licencia y vehículo</h2>
        <div class="mb-3">
            <label class="form-label">Número de licencia</label>
            <input type="text" name="licencia_numero" class="form-control" value="{{ old('licencia_numero') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Categoría licencia</label>
            <input type="text" name="licencia_categoria" class="form-control" value="{{ old('licencia_categoria', 'C1') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Placa</label>
            <input type="text" name="placa" class="form-control" value="{{ old('placa') }}" required>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Marca</label>
                <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">Línea</label>
                <input type="text" name="linea" class="form-control" value="{{ old('linea') }}" required>
            </div>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-4">
                <label class="form-label">Año</label>
                <input type="number" name="modelo_anio" class="form-control" value="{{ old('modelo_anio', 2020) }}" min="1990" max="2100">
            </div>
            <div class="col-4">
                <label class="form-label">Color</label>
                <input type="text" name="color" class="form-control" value="{{ old('color', 'Amarillo') }}">
            </div>
            <div class="col-4">
                <label class="form-label">SOAT</label>
                <input type="text" name="soat_numero" class="form-control" value="{{ old('soat_numero') }}">
            </div>
        </div>

        @include('components.referral-code-field')

        @include('components.registration-docs-notice', [
            'roleLabel' => 'el registro',
            'documents' => [
                'Cédula (ambas caras)',
                'Licencia de conducción vigente',
                'SOAT del vehículo',
                'Tarjeta de propiedad o contrato',
                'Foto del vehículo con placa visible',
            ],
        ])

        <button type="submit" class="btn btn-primary w-100 txp-auth-submit mt-3">Enviar solicitud</button>
    </form>

    <p class="txp-auth-footer mt-3 mb-0 text-center">
        ¿Ya tienes cuenta aprobada? <a href="{{ route('conductor.login') }}">Iniciar sesión</a>
    </p>
</div>
@endsection
