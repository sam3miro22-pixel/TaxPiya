@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Chat Mensajes</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>Remitente Id</th>
            <th>Remitente Rol</th>
            <th>Tipo</th>
            <th>Mensaje</th>
            <th>Media Url</th>
            <th>Media Tipo</th>
            <th>Reply To Id</th>
            <th>Lat</th>
            <th>Lng</th>
            <th>Leido Por Pasajero At</th>
            <th>Leido Por Conductor At</th>
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
            <td>{{ $record->remitente_id }}</td>
            <td>{{ $record->remitente_rol }}</td>
            <td>{{ $record->tipo }}</td>
            <td>{{ $record->mensaje }}</td>
            <td>{{ $record->media_url }}</td>
            <td>{{ $record->media_tipo }}</td>
            <td>{{ $record->reply_to_id }}</td>
            <td>{{ $record->lat }}</td>
            <td>{{ $record->lng }}</td>
            <td>{{ $record->leido_por_pasajero_at }}</td>
            <td>{{ $record->leido_por_conductor_at }}</td>
            <td>{{ $record->moderado }}</td>
            <td>{{ $record->moderado_motivo }}</td>
            <td>{{ $record->ip }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection