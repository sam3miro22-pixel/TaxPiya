@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Wallet Saldos</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Conductor Id</th>
            <th>Saldo Actual</th>
            <th>Saldo Reservado</th>
            <th>Min Operativo</th>
            <th>Moneda</th>
            <th>Last Movimiento Id</th>
            <th>Last Movimiento At</th>
            <th>Bloqueado</th>
            <th>Motivo Bloqueo</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->saldo_actual }}</td>
            <td>{{ $record->saldo_reservado }}</td>
            <td>{{ $record->min_operativo }}</td>
            <td>{{ $record->moneda }}</td>
            <td>{{ $record->last_movimiento_id }}</td>
            <td>{{ $record->last_movimiento_at }}</td>
            <td>{{ $record->bloqueado }}</td>
            <td>{{ $record->motivo_bloqueo }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection