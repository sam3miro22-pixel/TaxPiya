@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Conductor Posicion Actual Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Lat</th>
            <td>{{ $record->lat }}</td>
        </tr>
        <tr>
            <th>Lng</th>
            <td>{{ $record->lng }}</td>
        </tr>
        <tr>
            <th>Ubicacion</th>
            <td>{{ $record->ubicacion }}</td>
        </tr>
        <tr>
            <th>Precision M</th>
            <td>{{ $record->precision_m }}</td>
        </tr>
        <tr>
            <th>Velocidad Kmh</th>
            <td>{{ $record->velocidad_kmh }}</td>
        </tr>
        <tr>
            <th>Heading</th>
            <td>{{ $record->heading }}</td>
        </tr>
        <tr>
            <th>Origen</th>
            <td>{{ $record->origen }}</td>
        </tr>
        <tr>
            <th>Provider</th>
            <td>{{ $record->provider }}</td>
        </tr>
        <tr>
            <th>Bateria</th>
            <td>{{ $record->bateria }}</td>
        </tr>
        <tr>
            <th>App Estado</th>
            <td>{{ $record->app_estado }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
        <tr>
            <th>Actualizada At</th>
            <td>{{ $record->actualizada_at }}</td>
        </tr>
    </tbody>
</table>
@endsection