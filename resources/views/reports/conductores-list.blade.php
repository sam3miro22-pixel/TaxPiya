@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Conductores</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>User Id</th>
            <th>Estado Operitivo</th>
            <th>Disponible</th>
            <th>Last Online At</th>
            <th>Rating Promedio</th>
            <th>Total Viajes</th>
            <th>Licencia Numero</th>
            <th>Licencia Categoria</th>
            <th>Licencia Expira</th>
            <th>Soat Numero</th>
            <th>Soat Expira</th>
            <th>Seguro Numero</th>
            <th>Verificacion Estado</th>
            <th>Verificacion Nivel</th>
            <th>Verificacion Notas</th>
            <th>Contacto Emergencia Nombre</th>
            <th>Contacto Emergencia Telefono</th>
            <th>Location Permission</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->user_id }}</td>
            <td>{{ $record->estado_operitivo }}</td>
            <td>{{ $record->disponible }}</td>
            <td>{{ $record->last_online_at }}</td>
            <td>{{ $record->rating_promedio }}</td>
            <td>{{ $record->total_viajes }}</td>
            <td>{{ $record->licencia_numero }}</td>
            <td>{{ $record->licencia_categoria }}</td>
            <td>{{ $record->licencia_expira }}</td>
            <td>{{ $record->soat_numero }}</td>
            <td>{{ $record->soat_expira }}</td>
            <td>{{ $record->seguro_numero }}</td>
            <td>{{ $record->verificacion_estado }}</td>
            <td>{{ $record->verificacion_nivel }}</td>
            <td>{{ $record->verificacion_notas }}</td>
            <td>{{ $record->contacto_emergencia_nombre }}</td>
            <td>{{ $record->contacto_emergencia_telefono }}</td>
            <td>{{ $record->location_permission }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection