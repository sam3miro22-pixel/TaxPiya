@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Notificaciones</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>User Id</th>
            <th>Viaje Id</th>
            <th>Canal</th>
            <th>Proveedor</th>
            <th>Titulo</th>
            <th>Cuerpo</th>
            <th>Data Json</th>
            <th>Device Token Snapshot</th>
            <th>Estado</th>
            <th>Programada At</th>
            <th>Enviada At</th>
            <th>Entregada At</th>
            <th>Abierta At</th>
            <th>Fallida At</th>
            <th>Provider Message Id</th>
            <th>Error Code</th>
            <th>Error Message</th>
            <th>Idempotencia</th>
            <th>Prioridad</th>
            <th>Origen Evento</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->user_id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->canal }}</td>
            <td>{{ $record->proveedor }}</td>
            <td>{{ $record->titulo }}</td>
            <td>{{ $record->cuerpo }}</td>
            <td>{{ $record->data_json }}</td>
            <td>{{ $record->device_token_snapshot }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->programada_at }}</td>
            <td>{{ $record->enviada_at }}</td>
            <td>{{ $record->entregada_at }}</td>
            <td>{{ $record->abierta_at }}</td>
            <td>{{ $record->fallida_at }}</td>
            <td>{{ $record->provider_message_id }}</td>
            <td>{{ $record->error_code }}</td>
            <td>{{ $record->error_message }}</td>
            <td>{{ $record->idempotencia }}</td>
            <td>{{ $record->prioridad }}</td>
            <td>{{ $record->origen_evento }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection