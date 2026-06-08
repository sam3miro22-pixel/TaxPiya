<?php $pageTitle = 'Mi cuenta'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page">
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-user me-2"></i>Mi cuenta</h5>
                        <a href="{{ url('account/edit') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Nombre</dt>
                            <dd class="col-sm-8">{{ $data['name'] ?? '—' }}</dd>
                            <dt class="col-sm-4">Correo</dt>
                            <dd class="col-sm-8">{{ $data['email'] ?? '—' }}</dd>
                            <dt class="col-sm-4">Teléfono</dt>
                            <dd class="col-sm-8">{{ $data['telefono'] ?? '—' }}</dd>
                            <dt class="col-sm-4">Estado</dt>
                            <dd class="col-sm-8">
                                @if(($data['estado'] ?? 0) == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
