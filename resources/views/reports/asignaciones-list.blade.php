@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Asignaciones</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>Conductor Id</th>
            <th>Estado</th>
            <th>Ofertado At</th>
            <th>Expira At</th>
            <th>Respondido At</th>
            <th>Motivo Rechazo</th>
            <th>Distancia M</th>
            <th>Eta Min</th>
            <th>Radio Usado M</th>
            <th>Metodo</th>
            <th>Intento</th>
            <th>Prioridad</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->ofertado_at }}</td>
            <td>{{ $record->expira_at }}</td>
            <td>{{ $record->respondido_at }}</td>
            <td>{{ $record->motivo_rechazo }}</td>
            <td>{{ $record->distancia_m }}</td>
            <td>{{ $record->eta_min }}</td>
            <td>{{ $record->radio_usado_m }}</td>
            <td>{{ $record->metodo }}</td>
            <td>{{ $record->intento }}</td>
            <td>{{ $record->prioridad }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection