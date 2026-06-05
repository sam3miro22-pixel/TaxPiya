@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Wallet Saldo Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Saldo Actual</th>
            <td>{{ $record->saldo_actual }}</td>
        </tr>
        <tr>
            <th>Saldo Reservado</th>
            <td>{{ $record->saldo_reservado }}</td>
        </tr>
        <tr>
            <th>Min Operativo</th>
            <td>{{ $record->min_operativo }}</td>
        </tr>
        <tr>
            <th>Moneda</th>
            <td>{{ $record->moneda }}</td>
        </tr>
        <tr>
            <th>Last Movimiento Id</th>
            <td>{{ $record->last_movimiento_id }}</td>
        </tr>
        <tr>
            <th>Last Movimiento At</th>
            <td>{{ $record->last_movimiento_at }}</td>
        </tr>
        <tr>
            <th>Bloqueado</th>
            <td>{{ $record->bloqueado }}</td>
        </tr>
        <tr>
            <th>Motivo Bloqueo</th>
            <td>{{ $record->motivo_bloqueo }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $record->updated_at }}</td>
        </tr>
    </tbody>
</table>
@endsection