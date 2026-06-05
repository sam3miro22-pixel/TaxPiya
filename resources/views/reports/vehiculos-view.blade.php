@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Vehiculo Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Placa</th>
            <td>{{ $record->placa }}</td>
        </tr>
        <tr>
            <th>Vin</th>
            <td>{{ $record->vin }}</td>
        </tr>
        <tr>
            <th>Marca</th>
            <td>{{ $record->marca }}</td>
        </tr>
        <tr>
            <th>Linea</th>
            <td>{{ $record->linea }}</td>
        </tr>
        <tr>
            <th>Modelo Anio</th>
            <td>{{ $record->modelo_anio }}</td>
        </tr>
        <tr>
            <th>Color</th>
            <td>{{ $record->color }}</td>
        </tr>
        <tr>
            <th>Categoria</th>
            <td>{{ $record->categoria }}</td>
        </tr>
        <tr>
            <th>Asientos</th>
            <td>{{ $record->asientos }}</td>
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
            <th>Tecnomecanica Expira</th>
            <td>{{ $record->tecnomecanica_expira }}</td>
        </tr>
        <tr>
            <th>Seguro Extracontractual Numero</th>
            <td>{{ $record->seguro_extracontractual_numero }}</td>
        </tr>
        <tr>
            <th>Seguro Extracontractual Expira</th>
            <td>{{ $record->seguro_extracontractual_expira }}</td>
        </tr>
        <tr>
            <th>Estado Vehiculo</th>
            <td>{{ $record->estado_vehiculo }}</td>
        </tr>
        <tr>
            <th>Verificacion Estado</th>
            <td>{{ $record->verificacion_estado }}</td>
        </tr>
        <tr>
            <th>Verificacion Notas</th>
            <td>{{ $record->verificacion_notas }}</td>
        </tr>
        <tr>
            <th>Foto Principal</th>
            <td>{{ $record->foto_principal }}</td>
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