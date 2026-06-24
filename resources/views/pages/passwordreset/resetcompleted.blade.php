@extends('layouts.auth')
@section('title', 'Contraseña actualizada')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <x-taxpiya-logo />
            <h1 class="txp-auth-title">¡Listo!</h1>
            <p class="txp-auth-subtitle">Tu contraseña fue restablecida. Ya puedes iniciar sesión.</p>
        </div>
        <div class="txp-auth-actions">
            <a href="{{ route('login') }}" class="txp-auth-btn txp-auth-btn--primary">
                <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
            </a>
        </div>
    </div>
</div>
@endsection
