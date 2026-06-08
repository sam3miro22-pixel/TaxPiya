@php $pageTitle = 'Solicitud enviada'; @endphp
@extends('layouts.auth')
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-card text-center">
    <i class="fa-solid fa-clock fa-3x mb-3" style="color:#ffd166;"></i>
    <h1>Solicitud en revisión</h1>
    <p class="text-muted">Recibimos tu registro como conductor. No podrás iniciar sesión hasta que un administrador apruebe tu cuenta.</p>

    @include('components.registration-docs-notice', [
        'roleLabel' => 'el formulario',
        'documents' => [
            'Cédula (ambas caras)',
            'Licencia de conducción vigente',
            'SOAT del vehículo',
            'Tarjeta de propiedad o contrato del taxi',
            'Foto del vehículo con placa visible',
        ],
    ])

    <a href="{{ route('conductor.login') }}" class="btn btn-primary w-100 mt-3">Ir al login</a>
</div>
@endsection
