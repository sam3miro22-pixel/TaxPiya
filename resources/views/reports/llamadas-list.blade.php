@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Llamadas</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>Llamador User Id</th>
            <th>Llamador Rol</th>
            <th>Receptor User Id</th>
            <th>Receptor Rol</th>
            <th>Tipo</th>
            <th>Provider</th>
            <th>Provider Call Id</th>
            <th>Provider Room Id</th>
            <th>Caller Phone Snapshot</th>
            <th>Callee Phone Snapshot</th>
            <th>Proxy Number</th>
            <th>Masked</th>
            <th>Estado</th>
            <th>Call Start At</th>
            <th>Ring Start At</th>
            <th>Connected At</th>
            <th>Ended At</th>
            <th>Duracion Seg</th>
            <th>Dispositivo Id</th>
            <th>Ip</th>
            <th>Idempotencia</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->llamador_user_id }}</td>
            <td>{{ $record->llamador_rol }}</td>
            <td>{{ $record->receptor_user_id }}</td>
            <td>{{ $record->receptor_rol }}</td>
            <td>{{ $record->tipo }}</td>
            <td>{{ $record->provider }}</td>
            <td>{{ $record->provider_call_id }}</td>
            <td>{{ $record->provider_room_id }}</td>
            <td>{{ $record->caller_phone_snapshot }}</td>
            <td>{{ $record->callee_phone_snapshot }}</td>
            <td>{{ $record->proxy_number }}</td>
            <td>{{ $record->masked }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->call_start_at }}</td>
            <td>{{ $record->ring_start_at }}</td>
            <td>{{ $record->connected_at }}</td>
            <td>{{ $record->ended_at }}</td>
            <td>{{ $record->duracion_seg }}</td>
            <td>{{ $record->dispositivo_id }}</td>
            <td>{{ $record->ip }}</td>
            <td>{{ $record->idempotencia }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection