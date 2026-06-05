@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Auditoria Eventos</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Actor User Id</th>
            <th>Actor Rol</th>
            <th>Origen</th>
            <th>Accion</th>
            <th>Tabla Objetivo</th>
            <th>Registro Pk</th>
            <th>Detalles</th>
            <th>Viaje Id</th>
            <th>Conductor Id</th>
            <th>Before Json</th>
            <th>After Json</th>
            <th>Ip</th>
            <th>User Agent</th>
            <th>Request Id</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->actor_user_id }}</td>
            <td>{{ $record->actor_rol }}</td>
            <td>{{ $record->origen }}</td>
            <td>{{ $record->accion }}</td>
            <td>{{ $record->tabla_objetivo }}</td>
            <td>{{ $record->registro_pk }}</td>
            <td>{{ $record->detalles }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->before_json }}</td>
            <td>{{ $record->after_json }}</td>
            <td>{{ $record->ip }}</td>
            <td>{{ $record->user_agent }}</td>
            <td>{{ $record->request_id }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection