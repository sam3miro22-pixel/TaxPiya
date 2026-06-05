@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Asignacione Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Ofertado At</th>
            <td>{{ $record->ofertado_at }}</td>
        </tr>
        <tr>
            <th>Expira At</th>
            <td>{{ $record->expira_at }}</td>
        </tr>
        <tr>
            <th>Respondido At</th>
            <td>{{ $record->respondido_at }}</td>
        </tr>
        <tr>
            <th>Motivo Rechazo</th>
            <td>{{ $record->motivo_rechazo }}</td>
        </tr>
        <tr>
            <th>Distancia M</th>
            <td>{{ $record->distancia_m }}</td>
        </tr>
        <tr>
            <th>Eta Min</th>
            <td>{{ $record->eta_min }}</td>
        </tr>
        <tr>
            <th>Radio Usado M</th>
            <td>{{ $record->radio_usado_m }}</td>
        </tr>
        <tr>
            <th>Metodo</th>
            <td>{{ $record->metodo }}</td>
        </tr>
        <tr>
            <th>Intento</th>
            <td>{{ $record->intento }}</td>
        </tr>
        <tr>
            <th>Prioridad</th>
            <td>{{ $record->prioridad }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection