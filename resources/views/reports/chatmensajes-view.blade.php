@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Chat Mensaje Details</h1></div>
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
            <th>Remitente Id</th>
            <td>{{ $record->remitente_id }}</td>
        </tr>
        <tr>
            <th>Remitente Rol</th>
            <td>{{ $record->remitente_rol }}</td>
        </tr>
        <tr>
            <th>Tipo</th>
            <td>{{ $record->tipo }}</td>
        </tr>
        <tr>
            <th>Mensaje</th>
            <td>{{ $record->mensaje }}</td>
        </tr>
        <tr>
            <th>Media Url</th>
            <td>{{ $record->media_url }}</td>
        </tr>
        <tr>
            <th>Media Tipo</th>
            <td>{{ $record->media_tipo }}</td>
        </tr>
        <tr>
            <th>Reply To Id</th>
            <td>{{ $record->reply_to_id }}</td>
        </tr>
        <tr>
            <th>Lat</th>
            <td>{{ $record->lat }}</td>
        </tr>
        <tr>
            <th>Lng</th>
            <td>{{ $record->lng }}</td>
        </tr>
        <tr>
            <th>Leido Por Pasajero At</th>
            <td>{{ $record->leido_por_pasajero_at }}</td>
        </tr>
        <tr>
            <th>Leido Por Conductor At</th>
            <td>{{ $record->leido_por_conductor_at }}</td>
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