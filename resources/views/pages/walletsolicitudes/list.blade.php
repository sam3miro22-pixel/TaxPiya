<?php $pageTitle = 'Aprobar depósitos y retiros'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-admin-wrap">
    <div class="container-fluid py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1"><i class="fa fa-check-circle me-2 text-warning"></i>{{ $pageTitle }}</h4>
                <p class="text-muted small mb-0">Revisa comprobantes NEQUI y aprueba o rechaza depósitos y retiros de pasajeros, conductores y empresas.</p>
            </div>
            <a href="{{ url('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3"><i class="fa fa-arrow-left me-1"></i> Panel</a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="txp-card txp-card-kpi p-3 text-center">
                    <div class="txp-kpi-label">Pendientes</div>
                    <div class="txp-kpi-value text-warning">{{ $stats['pendientes'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="txp-card txp-card-kpi p-3 text-center">
                    <div class="txp-kpi-label">Acreditadas</div>
                    <div class="txp-kpi-value text-success">{{ $stats['completadas'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="txp-card txp-card-kpi p-3 text-center">
                    <div class="txp-kpi-label">Rechazadas</div>
                    <div class="txp-kpi-value text-danger">{{ $stats['rechazadas'] }}</div>
                </div>
            </div>
        </div>

        <div class="alert alert-secondary small mb-3">
            <strong>NEQUI Taxpiya:</strong> {{ config('taxpiya.wallet.nequi.numero') }} · {{ config('taxpiya.wallet.nequi.titular') }} · CC {{ config('taxpiya.wallet.nequi.cedula') }}
        </div>

        <div class="txp-card p-3 mb-3">
            <form class="row g-2 align-items-end" method="get">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Referencia, nombre, teléfono..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="pendiente" @selected(request('estado', 'pendiente')==='pendiente')>Pendientes</option>
                        <option value="completado" @selected(request('estado')==='completado')>Acreditadas</option>
                        <option value="rechazada" @selected(request('estado')==='rechazada')>Rechazadas</option>
                        <option value="" @selected(request()->has('estado') && request('estado')==='')>Todas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Operación</label>
                    <select name="operacion" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="deposito" @selected(request('operacion')==='deposito')>Depósito</option>
                        <option value="retiro" @selected(request('operacion')==='retiro')>Retiro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm w-100" type="submit"><i class="fa fa-filter me-1"></i> Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('walletsolicitudes') }}" class="btn btn-outline-light btn-sm w-100">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="txp-card">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-uppercase text-muted">
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Rol</th>
                            <th>Usuario</th>
                            <th>Operación</th>
                            <th>Monto</th>
                            <th>Ref. NEQUI</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr>
                                <td class="fw-semibold">#{{ $row->id }}</td>
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
                                <td class="text-end">
                                    @if($row->estado === 'pendiente')
                                        <a class="btn btn-sm btn-success rounded-pill px-3" href="{{ route('walletsolicitudes.view', $row->id) }}">
                                            <i class="fa fa-check me-1"></i> Revisar
                                        </a>
                                    @else
                                        <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="{{ route('walletsolicitudes.view', $row->id) }}">Ver</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    No hay solicitudes con estos filtros.<br>
                                    <small>Las recargas NEQUI enviadas por usuarios aparecerán aquí para aprobación.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
                <div class="p-3 border-top border-secondary">{{ $records->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
