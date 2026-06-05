@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Auditoria Evento Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Actor User Id</th>
            <td>{{ $record->actor_user_id }}</td>
        </tr>
        <tr>
            <th>Actor Rol</th>
            <td>{{ $record->actor_rol }}</td>
        </tr>
        <tr>
            <th>Origen</th>
            <td>{{ $record->origen }}</td>
        </tr>
        <tr>
            <th>Accion</th>
            <td>{{ $record->accion }}</td>
        </tr>
        <tr>
            <th>Tabla Objetivo</th>
            <td>{{ $record->tabla_objetivo }}</td>
        </tr>
        <tr>
            <th>Registro Pk</th>
            <td>{{ $record->registro_pk }}</td>
        </tr>
        <tr>
            <th>Detalles</th>
            <td>{{ $record->detalles }}</td>
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
            <th>Before Json</th>
            <td>{{ $record->before_json }}</td>
        </tr>
        <tr>
            <th>After Json</th>
            <td>{{ $record->after_json }}</td>
        </tr>
        <tr>
            <th>Ip</th>
            <td>{{ $record->ip }}</td>
        </tr>
        <tr>
            <th>User Agent</th>
            <td>{{ $record->user_agent }}</td>
        </tr>
        <tr>
            <th>Request Id</th>
            <td>{{ $record->request_id }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection