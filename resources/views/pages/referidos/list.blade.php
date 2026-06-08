<?php $pageTitle = 'Programa de referidos'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<div class="container-fluid py-3">
    <h4 class="mb-3"><i class="fa fa-gift me-2"></i>{{ $pageTitle }}</h4>

    <div class="row g-3 mb-3">
        <div class="col-md-2"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-muted small">Total</div><strong>{{ $stats['total'] }}</strong></div></div>
        <div class="col-md-2"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-muted small">Activos</div><strong>{{ $stats['activos'] }}</strong></div></div>
        <div class="col-md-2"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-muted small">Pasajeros</div><strong>{{ $stats['pasajeros'] }}</strong></div></div>
        <div class="col-md-2"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-muted small">Conductores</div><strong>{{ $stats['conductores'] }}</strong></div></div>
        <div class="col-md-2"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-muted small">Empresas</div><strong>{{ $stats['empresas'] }}</strong></div></div>
    </div>

    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Buscar código, nombre..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="estado" class="form-select">
                <option value="">Estado</option>
                <option value="registrado" @selected(request('estado')==='registrado')>registrado</option>
                <option value="activo" @selected(request('estado')==='activo')>activo</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="tipo" class="form-select">
                <option value="">Tipo</option>
                <option value="pasajero" @selected(request('tipo')==='pasajero')>pasajero</option>
                <option value="conductor" @selected(request('tipo')==='conductor')>conductor</option>
                <option value="empresa" @selected(request('tipo')==='empresa')>empresa</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Filtrar</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código usado</th>
                    <th>Referidor</th>
                    <th>Referido</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td><code>{{ $row->codigo_usado }}</code></td>
                        <td>
                            @if($row->referrer_empresa)
                                <span class="badge bg-info">Empresa</span> {{ $row->referrer_empresa }}
                            @else
                                {{ $row->referrer_name ?? '—' }}
                            @endif
                        </td>
                        <td>{{ $row->referred_name }}<br><small class="text-muted">{{ $row->referred_email }}</small></td>
                        <td>{{ $row->tipo_referido }}</td>
                        <td><span class="badge bg-{{ $row->estado === 'activo' ? 'success' : 'secondary' }}">{{ $row->estado }}</span></td>
                        <td><small>{{ $row->created_at }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin referidos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</div>
@endsection
