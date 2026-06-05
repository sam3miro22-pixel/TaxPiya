@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Conductor Posicion Actual</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Conductor Id</th>
            <th>Viaje Id</th>
            <th>Lat</th>
            <th>Lng</th>
            <th>Ubicacion</th>
            <th>Precision M</th>
            <th>Velocidad Kmh</th>
            <th>Heading</th>
            <th>Origen</th>
            <th>Provider</th>
            <th>Bateria</th>
            <th>App Estado</th>
            <th>Created At</th>
            <th>Actualizada At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->lat }}</td>
            <td>{{ $record->lng }}</td>
            <td>{{ $record->ubicacion }}</td>
            <td>{{ $record->precision_m }}</td>
            <td>{{ $record->velocidad_kmh }}</td>
            <td>{{ $record->heading }}</td>
            <td>{{ $record->origen }}</td>
            <td>{{ $record->provider }}</td>
            <td>{{ $record->bateria }}</td>
            <td>{{ $record->app_estado }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->actualizada_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection