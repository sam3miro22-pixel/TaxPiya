@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Usuario Dispositivo Details</h1></div>
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
            <th>Device Uuid</th>
            <td>{{ $record->device_uuid }}</td>
        </tr>
        <tr>
            <th>Plataforma</th>
            <td>{{ $record->plataforma }}</td>
        </tr>
        <tr>
            <th>App Version</th>
            <td>{{ $record->app_version }}</td>
        </tr>
        <tr>
            <th>Os Version</th>
            <td>{{ $record->os_version }}</td>
        </tr>
        <tr>
            <th>Idioma</th>
            <td>{{ $record->idioma }}</td>
        </tr>
        <tr>
            <th>Zona Horaria</th>
            <td>{{ $record->zona_horaria }}</td>
        </tr>
        <tr>
            <th>Fabricante</th>
            <td>{{ $record->fabricante }}</td>
        </tr>
        <tr>
            <th>Modelo</th>
            <td>{{ $record->modelo }}</td>
        </tr>
        <tr>
            <th>Notificaciones Activas</th>
            <td>{{ $record->notificaciones_activas }}</td>
        </tr>
        <tr>
            <th>Activo</th>
            <td>{{ $record->activo }}</td>
        </tr>
        <tr>
            <th>Is Emulador</th>
            <td>{{ $record->is_emulador }}</td>
        </tr>
        <tr>
            <th>Root Jailbreak</th>
            <td>{{ $record->root_jailbreak }}</td>
        </tr>
        <tr>
            <th>Installed At</th>
            <td>{{ $record->installed_at }}</td>
        </tr>
        <tr>
            <th>Last Seen At</th>
            <td>{{ $record->last_seen_at }}</td>
        </tr>
        <tr>
            <th>Last Ip</th>
            <td>{{ $record->last_ip }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $record->updated_at }}</td>
        </tr>
    </tbody>
</table>
@endsection