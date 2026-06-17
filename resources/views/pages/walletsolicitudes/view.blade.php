<?php $pageTitle = 'Solicitud #' . $rec_id; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="txp-admin-wrap">
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h4 class="mb-1"><i class="fa fa-receipt me-2"></i>{{ $pageTitle }}</h4>
                <p class="text-muted small mb-0">Revisa el comprobante y la referencia antes de acreditar o rechazar.</p>
            </div>
            <a href="{{ route('walletsolicitudes') }}" class="btn btn-outline-light btn-sm rounded-pill px-3"><i class="fa fa-arrow-left me-1"></i> Volver al listado</a>
        </div>

        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="txp-card p-0 overflow-hidden">
                    <div class="txp-card-header px-3 py-2 border-bottom border-secondary">Datos de la solicitud</div>
                    <div class="p-3">
                        <table class="table table-dark table-sm mb-0">
                            <tr>
                                <th class="text-muted" style="width:38%">Estado</th>
                                <td>
                                    @php
                                        $badge = match($data->estado) {
                                            'pendiente' => 'warning',
                                            'completado' => 'success',
                                            'rechazada' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $data->estado }}</span>
                                </td>
                            </tr>
                            <tr><th class="text-muted">Operación</th><td>{{ ucfirst($data->operacion) }}</td></tr>
                            <tr><th class="text-muted">Monto</th><td><strong>${{ number_format((float)$data->monto, 0, ',', '.') }} {{ $data->moneda }}</strong></td></tr>
                            <tr><th class="text-muted">Método</th><td>{{ strtoupper($data->metodo_pago ?? 'nequi') }}</td></tr>
                            <tr><th class="text-muted">Referencia NEQUI</th><td><code>{{ $data->referencia_pago ?: '—' }}</code></td></tr>
                            <tr><th class="text-muted">Rol</th><td>{{ $data->titular['tipo'] ?? '—' }}</td></tr>
                            <tr><th class="text-muted">Usuario</th><td>{{ $data->titular['nombre'] ?? '—' }}<br><small class="text-muted">{{ $data->titular['detalle'] ?? '' }}</small></td></tr>
                            <tr><th class="text-muted">Solicitado</th><td>{{ $data->created_at }}</td></tr>
                            @if($data->procesado_por)
                                <tr><th class="text-muted">Procesado</th><td>{{ $data->updated_at }}</td></tr>
                            @endif
                            @if($data->notas)
                                <tr><th class="text-muted">Notas</th><td>{{ $data->notas }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($data->estado === 'pendiente')
                <div class="txp-card p-0 overflow-hidden mt-3">
                    <div class="txp-card-header px-3 py-2 border-bottom border-secondary">Acciones de administrador</div>
                    <div class="p-3 d-grid gap-2">
                        <form method="post" action="{{ route('walletsolicitudes.aprobar', $rec_id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2" onclick="return confirm('¿Confirmar que el pago NEQUI fue recibido y acreditar el saldo?')">
                                <i class="fa fa-check me-1"></i> Aprobar y acreditar saldo
                            </button>
                        </form>
                        <form method="post" action="{{ route('walletsolicitudes.rechazar', $rec_id) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small text-muted">Motivo del rechazo (opcional)</label>
                                <textarea name="notas" class="form-control form-control-sm" rows="2" placeholder="Ej: Comprobante ilegible o monto no coincide"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 py-2" onclick="return confirm('¿Rechazar esta solicitud?')">
                                <i class="fa fa-times me-1"></i> Rechazar solicitud
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-7">
                <div class="txp-card p-0 overflow-hidden h-100">
                    <div class="txp-card-header px-3 py-2 border-bottom border-secondary">Comprobante de pago</div>
                    <div class="p-3 text-center">
                        @if($data->comprobante_url)
                            <a href="{{ $data->comprobante_url }}" target="_blank" rel="noopener" class="d-inline-block">
                                <img src="{{ $data->comprobante_url }}" alt="Comprobante NEQUI" class="img-fluid rounded border border-secondary" style="max-height:70vh;" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                <div class="text-muted py-5" style="display:none">
                                    <i class="fa fa-image fa-2x mb-2 d-block opacity-50"></i>
                                    No se pudo cargar la imagen del comprobante.<br>
                                    <a href="{{ $data->comprobante_url }}" target="_blank" class="btn btn-outline-light btn-sm mt-2">Intentar abrir enlace</a>
                                </div>
                            </a>
                            <p class="mt-3 mb-0">
                                <a href="{{ $data->comprobante_url }}" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                    <i class="fa fa-external-link me-1"></i> Abrir imagen completa
                                </a>
                            </p>
                        @else
                            <div class="text-muted py-5 mb-0">
                                <i class="fa fa-image fa-2x mb-2 d-block opacity-50"></i>
                                Sin comprobante adjunto
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
