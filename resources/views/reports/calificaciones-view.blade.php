@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Calificacione Details</h1></div>
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
            <th>Rater Id</th>
            <td>{{ $record->rater_id }}</td>
        </tr>
        <tr>
            <th>Rater Rol</th>
            <td>{{ $record->rater_rol }}</td>
        </tr>
        <tr>
            <th>Ratee Id</th>
            <td>{{ $record->ratee_id }}</td>
        </tr>
        <tr>
            <th>Ratee Rol</th>
            <td>{{ $record->ratee_rol }}</td>
        </tr>
        <tr>
            <th>Puntuacion</th>
            <td>{{ $record->puntuacion }}</td>
        </tr>
        <tr>
            <th>Comentario</th>
            <td>{{ $record->comentario }}</td>
        </tr>
        <tr>
            <th>Etiquetas Json</th>
            <td>{{ $record->etiquetas_json }}</td>
        </tr>
        <tr>
            <th>Visible</th>
            <td>{{ $record->visible }}</td>
        </tr>
        <tr>
            <th>Moderado</th>
            <td>{{ $record->moderado }}</td>
        </tr>
        <tr>
            <th>Moderado Motivo</th>
            <td>{{ $record->moderado_motivo }}</td>
        </tr>
        <tr>
            <th>Ip</th>
            <td>{{ $record->ip }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection