@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Wallet Movimiento Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Admin User Id</th>
            <td>{{ $record->admin_user_id }}</td>
        </tr>
        <tr>
            <th>Sentido</th>
            <td>{{ $record->sentido }}</td>
        </tr>
        <tr>
            <th>Motivo</th>
            <td>{{ $record->motivo }}</td>
        </tr>
        <tr>
            <th>Monto</th>
            <td>{{ $record->monto }}</td>
        </tr>
        <tr>
            <th>Moneda</th>
            <td>{{ $record->moneda }}</td>
        </tr>
        <tr>
            <th>Saldo Antes</th>
            <td>{{ $record->saldo_antes }}</td>
        </tr>
        <tr>
            <th>Saldo Despues</th>
            <td>{{ $record->saldo_despues }}</td>
        </tr>
        <tr>
            <th>Descripcion</th>
            <td>{{ $record->descripcion }}</td>
        </tr>
        <tr>
            <th>Referencia Externa</th>
            <td>{{ $record->referencia_externa }}</td>
        </tr>
        <tr>
            <th>Idempotencia</th>
            <td>{{ $record->idempotencia }}</td>
        </tr>
        <tr>
            <th>Anulado</th>
            <td>{{ $record->anulado }}</td>
        </tr>
        <tr>
            <th>Anulado Por</th>
            <td>{{ $record->anulado_por }}</td>
        </tr>
        <tr>
            <th>Anulado Motivo</th>
            <td>{{ $record->anulado_motivo }}</td>
        </tr>
        <tr>
            <th>Anulado At</th>
            <td>{{ $record->anulado_at }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection