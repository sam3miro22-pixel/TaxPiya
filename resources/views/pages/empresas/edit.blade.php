<?php $pageTitle = 'Gestionar empresa'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary">
                <div class="card-header">
                    <h5 class="mb-0">Gestionar: {{ $data->nombre_comercial }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ url('empresas/edit/' . $rec_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="estado">Estado de la empresa</label>
                            <select class="form-select" id="estado" name="estado" required>
                                @foreach(['pendiente','activa','suspendida','rechazada'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('estado', $data->estado) === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Usa <strong>activa</strong> para aprobar afiliaciones pendientes.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="verificacion_estado">Verificación</label>
                            <select class="form-select" id="verificacion_estado" name="verificacion_estado" required>
                                @foreach(['pendiente','verificado','rechazado'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('verificacion_estado', $data->verificacion_estado) === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="notas">Notas internas</label>
                            <textarea class="form-control" id="notas" name="notas" rows="4">{{ old('notas', $data->notas) }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Guardar</button>
                            <a href="{{ url('empresas/view/' . $rec_id) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
