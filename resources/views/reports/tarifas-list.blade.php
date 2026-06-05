@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Tarifas</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Scope</th>
            <th>Ciudad</th>
            <th>Categoria</th>
            <th>Horario</th>
            <th>Origen Ref</th>
            <th>Destino Ref</th>
            <th>Moneda</th>
            <th>Monto Fijo</th>
            <th>Recargo Nocturno</th>
            <th>Recargo Festivo</th>
            <th>Recargo Aeropuerto</th>
            <th>Incluye Peajes</th>
            <th>Minutos Espera Incluidos</th>
            <th>Valor Minuto Espera</th>
            <th>Vigente Desde</th>
            <th>Vigente Hasta</th>
            <th>Activa</th>
            <th>Prioridad</th>
            <th>Version</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->nombre }}</td>
            <td>{{ $record->descripcion }}</td>
            <td>{{ $record->scope }}</td>
            <td>{{ $record->ciudad }}</td>
            <td>{{ $record->categoria }}</td>
            <td>{{ $record->horario }}</td>
            <td>{{ $record->origen_ref }}</td>
            <td>{{ $record->destino_ref }}</td>
            <td>{{ $record->moneda }}</td>
            <td>{{ $record->monto_fijo }}</td>
            <td>{{ $record->recargo_nocturno }}</td>
            <td>{{ $record->recargo_festivo }}</td>
            <td>{{ $record->recargo_aeropuerto }}</td>
            <td>{{ $record->incluye_peajes }}</td>
            <td>{{ $record->minutos_espera_incluidos }}</td>
            <td>{{ $record->valor_minuto_espera }}</td>
            <td>{{ $record->vigente_desde }}</td>
            <td>{{ $record->vigente_hasta }}</td>
            <td>{{ $record->activa }}</td>
            <td>{{ $record->prioridad }}</td>
            <td>{{ $record->version }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection