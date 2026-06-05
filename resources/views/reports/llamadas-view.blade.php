@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Llamada Details</h1></div>
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
            <th>Llamador User Id</th>
            <td>{{ $record->llamador_user_id }}</td>
        </tr>
        <tr>
            <th>Llamador Rol</th>
            <td>{{ $record->llamador_rol }}</td>
        </tr>
        <tr>
            <th>Receptor User Id</th>
            <td>{{ $record->receptor_user_id }}</td>
        </tr>
        <tr>
            <th>Receptor Rol</th>
            <td>{{ $record->receptor_rol }}</td>
        </tr>
        <tr>
            <th>Tipo</th>
            <td>{{ $record->tipo }}</td>
        </tr>
        <tr>
            <th>Provider</th>
            <td>{{ $record->provider }}</td>
        </tr>
        <tr>
            <th>Provider Call Id</th>
            <td>{{ $record->provider_call_id }}</td>
        </tr>
        <tr>
            <th>Provider Room Id</th>
            <td>{{ $record->provider_room_id }}</td>
        </tr>
        <tr>
            <th>Caller Phone Snapshot</th>
            <td>{{ $record->caller_phone_snapshot }}</td>
        </tr>
        <tr>
            <th>Callee Phone Snapshot</th>
            <td>{{ $record->callee_phone_snapshot }}</td>
        </tr>
        <tr>
            <th>Proxy Number</th>
            <td>{{ $record->proxy_number }}</td>
        </tr>
        <tr>
            <th>Masked</th>
            <td>{{ $record->masked }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Call Start At</th>
            <td>{{ $record->call_start_at }}</td>
        </tr>
        <tr>
            <th>Ring Start At</th>
            <td>{{ $record->ring_start_at }}</td>
        </tr>
        <tr>
            <th>Connected At</th>
            <td>{{ $record->connected_at }}</td>
        </tr>
        <tr>
            <th>Ended At</th>
            <td>{{ $record->ended_at }}</td>
        </tr>
        <tr>
            <th>Duracion Seg</th>
            <td>{{ $record->duracion_seg }}</td>
        </tr>
        <tr>
            <th>Dispositivo Id</th>
            <td>{{ $record->dispositivo_id }}</td>
        </tr>
        <tr>
            <th>Ip</th>
            <td>{{ $record->ip }}</td>
        </tr>
        <tr>
            <th>Idempotencia</th>
            <td>{{ $record->idempotencia }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection