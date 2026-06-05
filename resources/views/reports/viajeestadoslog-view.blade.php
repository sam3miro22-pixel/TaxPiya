@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Viaje Estados Log Details</h1></div>
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
            <th>From Estado</th>
            <td>{{ $record->from_estado }}</td>
        </tr>
        <tr>
            <th>To Estado</th>
            <td>{{ $record->to_estado }}</td>
        </tr>
        <tr>
            <th>Actor Tipo</th>
            <td>{{ $record->actor_tipo }}</td>
        </tr>
        <tr>
            <th>Actor Id</th>
            <td>{{ $record->actor_id }}</td>
        </tr>
        <tr>
            <th>Motivo Codigo</th>
            <td>{{ $record->motivo_codigo }}</td>
        </tr>
        <tr>
            <th>Motivo Texto</th>
            <td>{{ $record->motivo_texto }}</td>
        </tr>
        <tr>
            <th>App Origen</th>
            <td>{{ $record->app_origen }}</td>
        </tr>
        <tr>
            <th>Ip</th>
            <td>{{ $record->ip }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection