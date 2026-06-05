@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Viaje Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Pasajero Id</th>
            <td>{{ $record->pasajero_id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Vehiculo Id</th>
            <td>{{ $record->vehiculo_id }}</td>
        </tr>
        <tr>
            <th>Origen Lat</th>
            <td>{{ $record->origen_lat }}</td>
        </tr>
        <tr>
            <th>Origen Lng</th>
            <td>{{ $record->origen_lng }}</td>
        </tr>
        <tr>
            <th>Origen Ubicacion</th>
            <td>{{ $record->origen_ubicacion }}</td>
        </tr>
        <tr>
            <th>Origen Texto</th>
            <td>{{ $record->origen_texto }}</td>
        </tr>
        <tr>
            <th>Destino Lat</th>
            <td>{{ $record->destino_lat }}</td>
        </tr>
        <tr>
            <th>Destino Lng</th>
            <td>{{ $record->destino_lng }}</td>
        </tr>
        <tr>
            <th>Destino Ubicacion</th>
            <td>{{ $record->destino_ubicacion }}</td>
        </tr>
        <tr>
            <th>Destino Texto</th>
            <td>{{ $record->destino_texto }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
        <tr>
            <th>Asignado At</th>
            <td>{{ $record->asignado_at }}</td>
        </tr>
        <tr>
            <th>Aceptar Hasta</th>
            <td>{{ $record->aceptar_hasta }}</td>
        </tr>
        <tr>
            <th>Aceptado At</th>
            <td>{{ $record->aceptado_at }}</td>
        </tr>
        <tr>
            <th>En Camino At</th>
            <td>{{ $record->en_camino_at }}</td>
        </tr>
        <tr>
            <th>Llego At</th>
            <td>{{ $record->llego_at }}</td>
        </tr>
        <tr>
            <th>Iniciado At</th>
            <td>{{ $record->iniciado_at }}</td>
        </tr>
        <tr>
            <th>Terminado At</th>
            <td>{{ $record->terminado_at }}</td>
        </tr>
        <tr>
            <th>Cancelado At</th>
            <td>{{ $record->cancelado_at }}</td>
        </tr>
        <tr>
            <th>Cancelado Por</th>
            <td>{{ $record->cancelado_por }}</td>
        </tr>
        <tr>
            <th>Cancelacion Motivo</th>
            <td>{{ $record->cancelacion_motivo }}</td>
        </tr>
        <tr>
            <th>Metodo Asignacion</th>
            <td>{{ $record->metodo_asignacion }}</td>
        </tr>
        <tr>
            <th>Radio Busqueda M</th>
            <td>{{ $record->radio_busqueda_m }}</td>
        </tr>
        <tr>
            <th>Eta Min Estimada</th>
            <td>{{ $record->eta_min_estimada }}</td>
        </tr>
        <tr>
            <th>Distancia Km Estimada</th>
            <td>{{ $record->distancia_km_estimada }}</td>
        </tr>
        <tr>
            <th>Duracion Min Estimada</th>
            <td>{{ $record->duracion_min_estimada }}</td>
        </tr>
        <tr>
            <th>Distancia Km Real</th>
            <td>{{ $record->distancia_km_real }}</td>
        </tr>
        <tr>
            <th>Duracion Min Real</th>
            <td>{{ $record->duracion_min_real }}</td>
        </tr>
        <tr>
            <th>Tarifa Id</th>
            <td>{{ $record->tarifa_id }}</td>
        </tr>
        <tr>
            <th>Moneda</th>
            <td>{{ $record->moneda }}</td>
        </tr>
        <tr>
            <th>Tarifa Aplicada</th>
            <td>{{ $record->tarifa_aplicada }}</td>
        </tr>
        <tr>
            <th>Valor Pagado</th>
            <td>{{ $record->valor_pagado }}</td>
        </tr>
        <tr>
            <th>Pago Registrado</th>
            <td>{{ $record->pago_registrado }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $record->updated_at }}</td>
        </tr>
    </tbody>
</table>
@endsection