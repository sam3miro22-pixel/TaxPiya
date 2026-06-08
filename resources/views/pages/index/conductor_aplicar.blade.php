@php $pageTitle = 'Solicitar ser conductor'; @endphp
@extends('layouts.auth')
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-card">
    <div class="txp-auth-brand">
        @include('components.taxpiya-logo')
        <h1>Conductor TaxPiya</h1>
        <p>Envía tu solicitud. Un administrador revisará tus datos.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('conductor.aplicar_store') }}" class="txp-auth-form">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Celular</label>
            <input type="tel" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email (opcional)</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Número de licencia</label>
            <input type="text" name="licencia_numero" class="form-control" value="{{ old('licencia_numero') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Placa del vehículo</label>
            <input type="text" name="placa" class="form-control" value="{{ old('placa') }}" placeholder="ABC123">
        </div>
        @include('components.referral-code-field')

        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Marca</label>
                <input type="text" name="marca" class="form-control" value="{{ old('marca') }}">
            </div>
            <div class="col-6">
                <label class="form-label">Línea</label>
                <input type="text" name="linea" class="form-control" value="{{ old('linea') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 txp-auth-submit">Enviar solicitud</button>
    </form>

    <p class="txp-auth-footer mt-3 mb-0 text-center">
        <a href="{{ route('conductor.login') }}">Ya tengo cuenta — Iniciar sesión</a>
    </p>
</div>
@endsection
