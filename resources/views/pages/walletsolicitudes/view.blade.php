<?php $pageTitle = 'Solicitud #' . $rec_id; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa fa-receipt me-2"></i>{{ $pageTitle }}</h4>
        <a href="{{ route('walletsolicitudes') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header">Datos de la solicitud</div>
                <div class="card-body">
                    <table class="table table-dark table-sm mb-0">
                        <tr><th>Estado</th><td><span class="badge bg-secondary">{{ $data->estado }}</span></td></tr>
                        <tr><th>Operación</th><td>{{ ucfirst($data->operacion) }}</td></tr>
                        <tr><th>Monto</th><td><strong>${{ number_format((float)$data->monto, 0, ',', '.') }} {{ $data->moneda }}</strong></td></tr>
                        <tr><th>Método</th><td>{{ strtoupper($data->metodo_pago ?? 'manual') }}</td></tr>
                        <tr><th>Referencia NEQUI</th><td><code>{{ $data->referencia_pago ?: '—' }}</code></td></tr>
                        <tr><th>Rol</th><td>{{ $data->titular['tipo'] ?? '—' }}</td></tr>
                        <tr><th>Usuario</th><td>{{ $data->titular['nombre'] ?? '—' }}<br><small class="text-muted">{{ $data->titular['detalle'] ?? '' }}</small></td></tr>
                        <tr><th>Solicitado</th><td>{{ $data->created_at }}</td></tr>
                        @if($data->procesado_por)
                            <tr><th>Procesado</th><td>{{ $data->updated_at }}</td></tr>
                        @endif
                        @if($data->notas)
                            <tr><th>Notas</th><td>{{ $data->notas }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($data->estado === 'pendiente')
            <div class="card bg-dark border-secondary mt-3">
                <div class="card-header">Acciones</div>
                <div class="card-body d-grid gap-2">
                    <form method="post" action="{{ route('walletsolicitudes.aprobar', $rec_id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Confirmar que el pago NEQUI fue recibido y acreditar el saldo?')">
                            <i class="fa fa-check"></i> Aprobar y acreditar saldo
                        </button>
                    </form>
                    <form method="post" action="{{ route('walletsolicitudes.rechazar', $rec_id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Motivo del rechazo (opcional)</label>
                            <textarea name="notas" class="form-control form-control-sm" rows="2" placeholder="Ej: Comprobante ilegible o monto no coincide"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Rechazar esta solicitud?')">
                            <i class="fa fa-times"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-7">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-header">Comprobante de pago</div>
                <div class="card-body text-center">
                    @if($data->comprobante_url)
                        <a href="{{ $data->comprobante_url }}" target="_blank" rel="noopener">
                            <img src="{{ $data->comprobante_url }}" alt="Comprobante NEQUI" class="img-fluid rounded border" style="max-height:70vh;">
                        </a>
                        <p class="mt-2 mb-0"><a href="{{ $data->comprobante_url }}" target="_blank" class="btn btn-outline-light btn-sm">Abrir imagen completa</a></p>
                    @else
                        <p class="text-muted py-5 mb-0">Sin comprobante adjunto</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
