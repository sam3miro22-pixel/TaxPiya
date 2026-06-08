@php $pageTitle = 'Solicitud enviada'; @endphp
@extends('layouts.auth')

@section('title', $pageTitle)
@section('auth_body_class', 'txp-auth-body--empresa')

@section('content')
<div class="txp-auth-scene txp-auth-scene--empresa">
    <div class="txp-auth-card text-center">
        <i class="fa-solid fa-circle-check fa-3x mb-3" style="color:#a78bfa;"></i>
        <h1 class="txp-auth-title">Solicitud <span>recibida</span></h1>
        <p class="txp-auth-subtitle">Tu afiliación está en revisión. No podrás usar la cuenta hasta que el administrador la apruebe.</p>
        @include('components.registration-docs-notice', [
            'roleLabel' => 'la solicitud',
            'documents' => [
                'Cámara de comercio o RUT',
                'NIT y documento del representante legal',
                'Licencia de transporte / permiso operación',
            ],
        ])
        <a href="{{ route('empresa.login') }}" class="txp-auth-btn txp-auth-btn--primary mt-3">Ir al login</a>
    </div>
</div>
@endsection
