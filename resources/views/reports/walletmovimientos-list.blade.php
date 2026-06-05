@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Wallet Movimientos</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Conductor Id</th>
            <th>Viaje Id</th>
            <th>Admin User Id</th>
            <th>Sentido</th>
            <th>Motivo</th>
            <th>Monto</th>
            <th>Moneda</th>
            <th>Saldo Antes</th>
            <th>Saldo Despues</th>
            <th>Descripcion</th>
            <th>Referencia Externa</th>
            <th>Idempotencia</th>
            <th>Anulado</th>
            <th>Anulado Por</th>
            <th>Anulado Motivo</th>
            <th>Anulado At</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->admin_user_id }}</td>
            <td>{{ $record->sentido }}</td>
            <td>{{ $record->motivo }}</td>
            <td>{{ $record->monto }}</td>
            <td>{{ $record->moneda }}</td>
            <td>{{ $record->saldo_antes }}</td>
            <td>{{ $record->saldo_despues }}</td>
            <td>{{ $record->descripcion }}</td>
            <td>{{ $record->referencia_externa }}</td>
            <td>{{ $record->idempotencia }}</td>
            <td>{{ $record->anulado }}</td>
            <td>{{ $record->anulado_por }}</td>
            <td>{{ $record->anulado_motivo }}</td>
            <td>{{ $record->anulado_at }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection