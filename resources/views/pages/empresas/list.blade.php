<?php
$pageTitle = 'Empresas / Flotas';
$can_view = $user->canAccess('empresas/view');
$can_edit = $user->canAccess('empresas/edit');
$pendientes = $records->where('estado', 'pendiente')->count();
?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <x-admin-back />
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1"><i class="fa fa-building me-2"></i>{{ $pageTitle }}</h4>
            <p class="text-muted mb-0">Afiliaciones y gestión de flotas</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('empresas/index/estado/pendiente') }}" class="btn btn-sm btn-warning">
                Pendientes ({{ $pendientes }})
            </a>
            <a href="{{ url('empresas') }}" class="btn btn-sm btn-secondary">Todas</a>
        </div>
    </div>

    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar empresa, NIT, contacto..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Empresa</th>
                    <th>Contacto</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Verificación</th>
                    <th>Registro</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>
                            <strong>{{ $row->nombre_comercial }}</strong><br>
                            <small class="text-muted">{{ $row->email }}</small>
                        </td>
                        <td>{{ $row->contacto }}</td>
                        <td>{{ $row->ciudad }}</td>
                        <td>
                            @php
                                $badge = match($row->estado) {
                                    'activa' => 'success',
                                    'pendiente' => 'warning',
                                    'suspendida', 'rechazada' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $row->estado }}</span>
                        </td>
                        <td>{{ $row->verificacion_estado }}</td>
                        <td><small>{{ $row->created_at }}</small></td>
                        <td class="text-end">
                            @if($can_view)
                                <a href="{{ url('empresas/view/' . $row->id) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            @endif
                            @if($can_edit)
                                <a href="{{ url('empresas/edit/' . $row->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay empresas registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="mt-3">{{ $records->links() }}</div>
    @endif
</div>
@endsection
