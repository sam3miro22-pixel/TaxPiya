@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Calificaciones</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>Rater Id</th>
            <th>Rater Rol</th>
            <th>Ratee Id</th>
            <th>Ratee Rol</th>
            <th>Puntuacion</th>
            <th>Comentario</th>
            <th>Etiquetas Json</th>
            <th>Visible</th>
            <th>Moderado</th>
            <th>Moderado Motivo</th>
            <th>Ip</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->rater_id }}</td>
            <td>{{ $record->rater_rol }}</td>
            <td>{{ $record->ratee_id }}</td>
            <td>{{ $record->ratee_rol }}</td>
            <td>{{ $record->puntuacion }}</td>
            <td>{{ $record->comentario }}</td>
            <td>{{ $record->etiquetas_json }}</td>
            <td>{{ $record->visible }}</td>
            <td>{{ $record->moderado }}</td>
            <td>{{ $record->moderado_motivo }}</td>
            <td>{{ $record->ip }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection