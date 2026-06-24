@extends('layouts.auth')
@section('title', 'Enlace enviado')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <x-taxpiya-logo />
            <h1 class="txp-auth-title">Revisa tu <span>correo</span></h1>
            <p class="txp-auth-subtitle">Si el email existe en TaxPiya, recibirás instrucciones para restablecer tu contraseña.</p>
        </div>
        <div class="txp-auth-actions">
            <a href="{{ route('login') }}" class="txp-auth-btn txp-auth-btn--primary">
                <i class="fa-solid fa-right-to-bracket"></i> Ir al login
            </a>
        </div>
    </div>
</div>
@endsection
