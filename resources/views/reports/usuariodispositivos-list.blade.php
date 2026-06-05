@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Usuario Dispositivos</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>User Id</th>
            <th>Device Uuid</th>
            <th>Plataforma</th>
            <th>App Version</th>
            <th>Os Version</th>
            <th>Idioma</th>
            <th>Zona Horaria</th>
            <th>Fabricante</th>
            <th>Modelo</th>
            <th>Notificaciones Activas</th>
            <th>Activo</th>
            <th>Is Emulador</th>
            <th>Root Jailbreak</th>
            <th>Installed At</th>
            <th>Last Seen At</th>
            <th>Last Ip</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->user_id }}</td>
            <td>{{ $record->device_uuid }}</td>
            <td>{{ $record->plataforma }}</td>
            <td>{{ $record->app_version }}</td>
            <td>{{ $record->os_version }}</td>
            <td>{{ $record->idioma }}</td>
            <td>{{ $record->zona_horaria }}</td>
            <td>{{ $record->fabricante }}</td>
            <td>{{ $record->modelo }}</td>
            <td>{{ $record->notificaciones_activas }}</td>
            <td>{{ $record->activo }}</td>
            <td>{{ $record->is_emulador }}</td>
            <td>{{ $record->root_jailbreak }}</td>
            <td>{{ $record->installed_at }}</td>
            <td>{{ $record->last_seen_at }}</td>
            <td>{{ $record->last_ip }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection