@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Notificacione Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>User Id</th>
            <td>{{ $record->user_id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Canal</th>
            <td>{{ $record->canal }}</td>
        </tr>
        <tr>
            <th>Proveedor</th>
            <td>{{ $record->proveedor }}</td>
        </tr>
        <tr>
            <th>Titulo</th>
            <td>{{ $record->titulo }}</td>
        </tr>
        <tr>
            <th>Cuerpo</th>
            <td>{{ $record->cuerpo }}</td>
        </tr>
        <tr>
            <th>Data Json</th>
            <td>{{ $record->data_json }}</td>
        </tr>
        <tr>
            <th>Device Token Snapshot</th>
            <td>{{ $record->device_token_snapshot }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Programada At</th>
            <td>{{ $record->programada_at }}</td>
        </tr>
        <tr>
            <th>Enviada At</th>
            <td>{{ $record->enviada_at }}</td>
        </tr>
        <tr>
            <th>Entregada At</th>
            <td>{{ $record->entregada_at }}</td>
        </tr>
        <tr>
            <th>Abierta At</th>
            <td>{{ $record->abierta_at }}</td>
        </tr>
        <tr>
            <th>Fallida At</th>
            <td>{{ $record->fallida_at }}</td>
        </tr>
        <tr>
            <th>Provider Message Id</th>
            <td>{{ $record->provider_message_id }}</td>
        </tr>
        <tr>
            <th>Error Code</th>
            <td>{{ $record->error_code }}</td>
        </tr>
        <tr>
            <th>Error Message</th>
            <td>{{ $record->error_message }}</td>
        </tr>
        <tr>
            <th>Idempotencia</th>
            <td>{{ $record->idempotencia }}</td>
        </tr>
        <tr>
            <th>Prioridad</th>
            <td>{{ $record->prioridad }}</td>
        </tr>
        <tr>
            <th>Origen Evento</th>
            <td>{{ $record->origen_evento }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection