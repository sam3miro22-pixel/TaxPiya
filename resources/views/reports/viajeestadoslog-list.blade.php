@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Viaje Estados Log</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>From Estado</th>
            <th>To Estado</th>
            <th>Actor Tipo</th>
            <th>Actor Id</th>
            <th>Motivo Codigo</th>
            <th>Motivo Texto</th>
            <th>App Origen</th>
            <th>Ip</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->from_estado }}</td>
            <td>{{ $record->to_estado }}</td>
            <td>{{ $record->actor_tipo }}</td>
            <td>{{ $record->actor_id }}</td>
            <td>{{ $record->motivo_codigo }}</td>
            <td>{{ $record->motivo_texto }}</td>
            <td>{{ $record->app_origen }}</td>
            <td>{{ $record->ip }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection