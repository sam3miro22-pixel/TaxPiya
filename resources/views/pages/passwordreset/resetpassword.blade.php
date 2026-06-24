@extends('layouts.auth')
@section('title', 'Nueva contraseña')

@section('content')
<div class="txp-auth-scene">
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <x-taxpiya-logo />
            <h1 class="txp-auth-title">Nueva <span>contraseña</span></h1>
            <p class="txp-auth-subtitle">Elige una contraseña segura de al menos 6 caracteres.</p>
        </div>

        @if ($errors->any())
            <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first() }}</div>
        @endif

        <form class="txp-auth-form" method="POST" action="{{ route('password.resetpassword') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request()->token }}">
            <input type="hidden" name="email" value="{{ request()->email }}">
            <div class="txp-auth-field">
                <label class="txp-auth-label" for="password">Nueva contraseña</label>
                <input id="password" name="password" type="password" class="txp-auth-input" required minlength="6">
            </div>
            <div class="txp-auth-field">
                <label class="txp-auth-label" for="confirm_password">Confirmar contraseña</label>
                <input id="confirm_password" name="confirm_password" type="password" class="txp-auth-input" required minlength="6">
            </div>
            <div class="txp-auth-actions">
                <button class="txp-auth-btn txp-auth-btn--primary" type="submit">
                    <i class="fa-solid fa-check"></i> Cambiar contraseña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
