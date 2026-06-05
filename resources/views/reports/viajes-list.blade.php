@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Viajes</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Pasajero Id</th>
            <th>Conductor Id</th>
            <th>Vehiculo Id</th>
            <th>Origen Lat</th>
            <th>Origen Lng</th>
            <th>Origen Ubicacion</th>
            <th>Origen Texto</th>
            <th>Destino Lat</th>
            <th>Destino Lng</th>
            <th>Destino Ubicacion</th>
            <th>Destino Texto</th>
            <th>Estado</th>
            <th>Created At</th>
            <th>Asignado At</th>
            <th>Aceptar Hasta</th>
            <th>Aceptado At</th>
            <th>En Camino At</th>
            <th>Llego At</th>
            <th>Iniciado At</th>
            <th>Terminado At</th>
            <th>Cancelado At</th>
            <th>Cancelado Por</th>
            <th>Cancelacion Motivo</th>
            <th>Metodo Asignacion</th>
            <th>Radio Busqueda M</th>
            <th>Eta Min Estimada</th>
            <th>Distancia Km Estimada</th>
            <th>Duracion Min Estimada</th>
            <th>Distancia Km Real</th>
            <th>Duracion Min Real</th>
            <th>Tarifa Id</th>
            <th>Moneda</th>
            <th>Tarifa Aplicada</th>
            <th>Valor Pagado</th>
            <th>Pago Registrado</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->pasajero_id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->vehiculo_id }}</td>
            <td>{{ $record->origen_lat }}</td>
            <td>{{ $record->origen_lng }}</td>
            <td>{{ $record->origen_ubicacion }}</td>
            <td>{{ $record->origen_texto }}</td>
            <td>{{ $record->destino_lat }}</td>
            <td>{{ $record->destino_lng }}</td>
            <td>{{ $record->destino_ubicacion }}</td>
            <td>{{ $record->destino_texto }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->asignado_at }}</td>
            <td>{{ $record->aceptar_hasta }}</td>
            <td>{{ $record->aceptado_at }}</td>
            <td>{{ $record->en_camino_at }}</td>
            <td>{{ $record->llego_at }}</td>
            <td>{{ $record->iniciado_at }}</td>
            <td>{{ $record->terminado_at }}</td>
            <td>{{ $record->cancelado_at }}</td>
            <td>{{ $record->cancelado_por }}</td>
            <td>{{ $record->cancelacion_motivo }}</td>
            <td>{{ $record->metodo_asignacion }}</td>
            <td>{{ $record->radio_busqueda_m }}</td>
            <td>{{ $record->eta_min_estimada }}</td>
            <td>{{ $record->distancia_km_estimada }}</td>
            <td>{{ $record->duracion_min_estimada }}</td>
            <td>{{ $record->distancia_km_real }}</td>
            <td>{{ $record->duracion_min_real }}</td>
            <td>{{ $record->tarifa_id }}</td>
            <td>{{ $record->moneda }}</td>
            <td>{{ $record->tarifa_aplicada }}</td>
            <td>{{ $record->valor_pagado }}</td>
            <td>{{ $record->pago_registrado }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection