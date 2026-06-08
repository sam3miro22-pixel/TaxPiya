@extends('layouts.info')
@section('title', 'Cuenta inactiva')
@section('content')
<div class="container">
    <div class="my-4 text-center p-4 card bg-dark border-secondary rounded-3">
        <i class="fa-solid fa-ban fa-3x text-danger mb-3"></i>
        <div class="h4 fw-bold text-danger my-3">Tu cuenta no está activa</div>
        <div class="text-muted">
            Contacta al administrador del sistema para más información.
        </div>
        <hr class="my-4" />
        <a href="{{ url('/') }}" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ir al inicio</a>
    </div>
</div>
@endsection