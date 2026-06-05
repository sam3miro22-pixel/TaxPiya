@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Vehiculos</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Conductor Id</th>
            <th>Placa</th>
            <th>Vin</th>
            <th>Marca</th>
            <th>Linea</th>
            <th>Modelo Anio</th>
            <th>Color</th>
            <th>Categoria</th>
            <th>Asientos</th>
            <th>Soat Numero</th>
            <th>Soat Expira</th>
            <th>Tecnomecanica Expira</th>
            <th>Seguro Extracontractual Numero</th>
            <th>Seguro Extracontractual Expira</th>
            <th>Estado Vehiculo</th>
            <th>Verificacion Estado</th>
            <th>Verificacion Notas</th>
            <th>Foto Principal</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->placa }}</td>
            <td>{{ $record->vin }}</td>
            <td>{{ $record->marca }}</td>
            <td>{{ $record->linea }}</td>
            <td>{{ $record->modelo_anio }}</td>
            <td>{{ $record->color }}</td>
            <td>{{ $record->categoria }}</td>
            <td>{{ $record->asientos }}</td>
            <td>{{ $record->soat_numero }}</td>
            <td>{{ $record->soat_expira }}</td>
            <td>{{ $record->tecnomecanica_expira }}</td>
            <td>{{ $record->seguro_extracontractual_numero }}</td>
            <td>{{ $record->seguro_extracontractual_expira }}</td>
            <td>{{ $record->estado_vehiculo }}</td>
            <td>{{ $record->verificacion_estado }}</td>
            <td>{{ $record->verificacion_notas }}</td>
            <td>{{ $record->foto_principal }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection