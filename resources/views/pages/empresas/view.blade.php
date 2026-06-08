<?php
$pageTitle = 'Ver empresa';
$can_edit = $user->canAccess('empresas/edit');
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa fa-building me-2"></i>{{ $data->nombre_comercial }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ url('empresas') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Volver</a>
            @if($can_edit)
                <a href="{{ url('empresas/edit/' . $rec_id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Gestionar</a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-header">Datos de la empresa</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Razón social</dt><dd class="col-sm-8">{{ $data->razon_social ?? '—' }}</dd>
                        <dt class="col-sm-4">NIT</dt><dd class="col-sm-8">{{ $data->nit ?? '—' }}</dd>
                        <dt class="col-sm-4">Ciudad</dt><dd class="col-sm-8">{{ $data->ciudad }}</dd>
                        <dt class="col-sm-4">Dirección</dt><dd class="col-sm-8">{{ $data->direccion ?? '—' }}</dd>
                        <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">{{ $data->telefono ?? '—' }}</dd>
                        <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">{{ $data->email ?? '—' }}</dd>
                        <dt class="col-sm-4">Estado</dt><dd class="col-sm-8"><span class="badge bg-info">{{ $data->estado }}</span></dd>
                        <dt class="col-sm-4">Verificación</dt><dd class="col-sm-8">{{ $data->verificacion_estado }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-header">Cuenta y operación</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Contacto</dt><dd class="col-sm-8">{{ $data->contacto }}</dd>
                        <dt class="col-sm-4">Usuario email</dt><dd class="col-sm-8">{{ $data->user_email }}</dd>
                        <dt class="col-sm-4">Usuario tel.</dt><dd class="col-sm-8">{{ $data->user_telefono ?? '—' }}</dd>
                        <dt class="col-sm-4">Taxis en flota</dt><dd class="col-sm-8">{{ $data->flota_count }}</dd>
                        <dt class="col-sm-4">Viajes totales</dt><dd class="col-sm-8">{{ $data->viajes_count }}</dd>
                        <dt class="col-sm-4">Notas</dt><dd class="col-sm-8">{{ $data->notas ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
