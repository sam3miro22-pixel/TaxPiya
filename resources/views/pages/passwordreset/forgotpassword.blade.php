@php
    $app = $app ?? request('app');
    $loginRoutes = [
        'pasajero'  => route('pasajero.login'),
        'conductor' => route('conductor.login'),
        'empresa'   => route('empresa.login'),
        'admin'     => route('login'),
    ];
    $loginBack = $loginRoutes[$app] ?? route('login');
@endphp
@extends('layouts.auth')
@section('title', 'Recuperar contraseña')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <div class="txp-auth-logo-wrap">
                <x-taxpiya-logo />
            </div>
            <h1 class="txp-auth-title">¿Olvidaste tu <span>contraseña</span>?</h1>
            <p class="txp-auth-subtitle">Te enviaremos un enlace a tu correo para restablecerla.</p>
        </div>

        @if (session('status'))
            <div class="txp-auth-alert txp-auth-alert--success">{{ session('status') }}</div>
        @endif
        @if ($errors->has('email'))
            <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first('email') }}</div>
        @endif

        <form class="txp-auth-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            @if($app)
                <input type="hidden" name="app" value="{{ $app }}">
            @endif
            <div class="txp-auth-field">
                <label class="txp-auth-label" for="email">Correo electrónico</label>
                <div class="txp-auth-input-wrap">
                    <i class="fa-solid fa-envelope txp-auth-input-icon"></i>
                    <input id="email" name="email" type="email" class="txp-auth-input"
                           placeholder="correo@ejemplo.com" value="{{ old('email') }}" required autocomplete="email">
                </div>
            </div>
            <div class="txp-auth-actions">
                <button class="txp-auth-btn txp-auth-btn--primary" type="submit">
                    <i class="fa-solid fa-paper-plane"></i> Enviar enlace
                </button>
                <a href="{{ $loginBack }}" class="txp-auth-btn txp-auth-btn--ghost">
                    <i class="fa-solid fa-arrow-left"></i> Volver al login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
