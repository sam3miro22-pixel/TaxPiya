@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Conductore Details</h1></div>
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
            <th>Estado Operitivo</th>
            <td>{{ $record->estado_operitivo }}</td>
        </tr>
        <tr>
            <th>Disponible</th>
            <td>{{ $record->disponible }}</td>
        </tr>
        <tr>
            <th>Last Online At</th>
            <td>{{ $record->last_online_at }}</td>
        </tr>
        <tr>
            <th>Rating Promedio</th>
            <td>{{ $record->rating_promedio }}</td>
        </tr>
        <tr>
            <th>Total Viajes</th>
            <td>{{ $record->total_viajes }}</td>
        </tr>
        <tr>
            <th>Licencia Numero</th>
            <td>{{ $record->licencia_numero }}</td>
        </tr>
        <tr>
            <th>Licencia Categoria</th>
            <td>{{ $record->licencia_categoria }}</td>
        </tr>
        <tr>
            <th>Licencia Expira</th>
            <td>{{ $record->licencia_expira }}</td>
        </tr>
        <tr>
            <th>Soat Numero</th>
            <td>{{ $record->soat_numero }}</td>
        </tr>
        <tr>
            <th>Soat Expira</th>
            <td>{{ $record->soat_expira }}</td>
        </tr>
        <tr>
            <th>Seguro Numero</th>
            <td>{{ $record->seguro_numero }}</td>
        </tr>
        <tr>
            <th>Verificacion Estado</th>
            <td>{{ $record->verificacion_estado }}</td>
        </tr>
        <tr>
            <th>Verificacion Nivel</th>
            <td>{{ $record->verificacion_nivel }}</td>
        </tr>
        <tr>
            <th>Verificacion Notas</th>
            <td>{{ $record->verificacion_notas }}</td>
        </tr>
        <tr>
            <th>Contacto Emergencia Nombre</th>
            <td>{{ $record->contacto_emergencia_nombre }}</td>
        </tr>
        <tr>
            <th>Contacto Emergencia Telefono</th>
            <td>{{ $record->contacto_emergencia_telefono }}</td>
        </tr>
        <tr>
            <th>Location Permission</th>
            <td>{{ $record->location_permission }}</td>
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