@php $pageTitle = 'Afiliar mi empresa'; @endphp
@extends('layouts.auth')

@section('title', $pageTitle)
@section('auth_body_class', 'txp-auth-body--empresa')

@section('content')
<div class="txp-auth-scene txp-auth-scene--empresa">
    <div class="txp-auth-card" style="max-width:520px;width:100%;">
        <div class="txp-auth-header">
            <h1 class="txp-auth-title">Afiliar <span>mini empresa</span></h1>
            <p class="txp-auth-subtitle">Registra tu cooperativa o flota de taxis y administra conductores desde un solo panel.</p>
        </div>

        @if($errors->any())
            <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('empresa.afiliarse.store') }}" class="txp-auth-form">
            @csrf
            <div class="txp-auth-field">
                <label class="txp-auth-label">Nombre comercial</label>
                <input name="nombre_comercial" class="txp-auth-input" value="{{ old('nombre_comercial') }}" required>
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Razón social</label>
                <input name="razon_social" class="txp-auth-input" value="{{ old('razon_social') }}">
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">NIT (opcional)</label>
                <input name="nit" class="txp-auth-input" value="{{ old('nit') }}">
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Ciudad</label>
                <input name="ciudad" class="txp-auth-input" value="{{ old('ciudad', 'Medellín') }}" required>
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Dirección</label>
                <input name="direccion" class="txp-auth-input" value="{{ old('direccion') }}">
            </div>
            <hr style="border-color:rgba(255,255,255,.1);margin:1rem 0;">
            <div class="txp-auth-field">
                <label class="txp-auth-label">Nombre del responsable</label>
                <input name="contacto_nombre" class="txp-auth-input" value="{{ old('contacto_nombre') }}" required>
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Celular</label>
                <input name="telefono" class="txp-auth-input" value="{{ old('telefono') }}" required>
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Correo</label>
                <input type="email" name="email" class="txp-auth-input" value="{{ old('email') }}" required>
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Contraseña</label>
                <input type="password" name="password" class="txp-auth-input" required minlength="6">
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="txp-auth-input" required>
            </div>
            @include('components.referral-code-field')

            @include('components.registration-docs-notice', [
                'roleLabel' => 'la afiliación',
                'documents' => [
                    'Cámara de comercio o RUT',
                    'NIT y documento del representante legal',
                    'Licencia de transporte / permiso operación',
                    'Listado inicial de vehículos y conductores (si aplica)',
                ],
            ])

            <div class="txp-auth-check mb-3">
                <input type="checkbox" name="acepta" id="acepta" value="1" required>
                <label for="acepta">Acepto los términos y autorizo la verificación de mi empresa</label>
            </div>
            <button type="submit" class="txp-auth-btn txp-auth-btn--primary w-100">Enviar solicitud</button>
        </form>

        <p class="txp-auth-foot mt-3">
            ¿Ya tienes cuenta? <a href="{{ route('empresa.login') }}">Iniciar sesión</a>
        </p>
    </div>
</div>
@endsection
