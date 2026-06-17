<?php $pageTitle = 'Depósitos y recargas'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <h4 class="mb-3"><i class="fa fa-mobile me-2"></i>{{ $pageTitle }}</h4>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card bg-dark border-warning p-3 text-center"><div class="text-muted small">Pendientes</div><strong class="text-warning">{{ $stats['pendientes'] }}</strong></div></div>
        <div class="col-md-3"><div class="card bg-dark border-success p-3 text-center"><div class="text-muted small">Acreditadas</div><strong class="text-success">{{ $stats['completadas'] }}</strong></div></div>
        <div class="col-md-3"><div class="card bg-dark border-danger p-3 text-center"><div class="text-muted small">Rechazadas</div><strong class="text-danger">{{ $stats['rechazadas'] }}</strong></div></div>
    </div>

    <div class="alert alert-secondary small">
        <strong>NEQUI Taxpiya:</strong> {{ config('taxpiya.wallet.nequi.numero') }} · {{ config('taxpiya.wallet.nequi.titular') }} · CC {{ config('taxpiya.wallet.nequi.cedula') }}
    </div>

    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Referencia, nombre, teléfono..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="estado" class="form-select">
                <option value="pendiente" @selected(request('estado', 'pendiente')==='pendiente')>Pendientes</option>
                <option value="completado" @selected(request('estado')==='completado')>Acreditadas</option>
                <option value="rechazada" @selected(request('estado')==='rechazada')>Rechazadas</option>
                <option value="" @selected(request('estado')==='')>Todas</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="operacion" class="form-select">
                <option value="">Operación</option>
                <option value="deposito" @selected(request('operacion')==='deposito')>Depósito</option>
                <option value="retiro" @selected(request('operacion')==='retiro')>Retiro</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Filtrar</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Rol</th>
                    <th>Usuario</th>
                    <th>Operación</th>
                    <th>Monto</th>
                    <th>Ref. NEQUI</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td><small>{{ $row->created_at }}</small></td>
                        <td><span class="badge bg-info">{{ $row->titular['tipo'] ?? $row->cuenta_tipo }}</span></td>
                        <td>
                            <strong>{{ $row->titular['nombre'] ?? $row->solicitante_nombre }}</strong><br>
                            <small class="text-muted">{{ $row->titular['detalle'] ?? $row->solicitante_email }}</small>
                        </td>
                        <td>{{ ucfirst($row->operacion) }}</td>
                        <td><strong>${{ number_format((float)$row->monto, 0, ',', '.') }}</strong></td>
                        <td><code>{{ $row->referencia_pago ?: '—' }}</code></td>
                        <td>
                            @php
                                $badge = match($row->estado) {
                                    'pendiente' => 'warning',
                                    'completado' => 'success',
                                    'rechazada' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $row->estado }}</span>
                        </td>
                        <td><a class="btn btn-sm btn-outline-light" href="{{ route('walletsolicitudes.view', $row->id) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay solicitudes con estos filtros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</div>
@endsection
