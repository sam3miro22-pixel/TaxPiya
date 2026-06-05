@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Tarifa Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $record->nombre }}</td>
        </tr>
        <tr>
            <th>Descripcion</th>
            <td>{{ $record->descripcion }}</td>
        </tr>
        <tr>
            <th>Scope</th>
            <td>{{ $record->scope }}</td>
        </tr>
        <tr>
            <th>Ciudad</th>
            <td>{{ $record->ciudad }}</td>
        </tr>
        <tr>
            <th>Categoria</th>
            <td>{{ $record->categoria }}</td>
        </tr>
        <tr>
            <th>Horario</th>
            <td>{{ $record->horario }}</td>
        </tr>
        <tr>
            <th>Origen Ref</th>
            <td>{{ $record->origen_ref }}</td>
        </tr>
        <tr>
            <th>Destino Ref</th>
            <td>{{ $record->destino_ref }}</td>
        </tr>
        <tr>
            <th>Moneda</th>
            <td>{{ $record->moneda }}</td>
        </tr>
        <tr>
            <th>Monto Fijo</th>
            <td>{{ $record->monto_fijo }}</td>
        </tr>
        <tr>
            <th>Recargo Nocturno</th>
            <td>{{ $record->recargo_nocturno }}</td>
        </tr>
        <tr>
            <th>Recargo Festivo</th>
            <td>{{ $record->recargo_festivo }}</td>
        </tr>
        <tr>
            <th>Recargo Aeropuerto</th>
            <td>{{ $record->recargo_aeropuerto }}</td>
        </tr>
        <tr>
            <th>Incluye Peajes</th>
            <td>{{ $record->incluye_peajes }}</td>
        </tr>
        <tr>
            <th>Minutos Espera Incluidos</th>
            <td>{{ $record->minutos_espera_incluidos }}</td>
        </tr>
        <tr>
            <th>Valor Minuto Espera</th>
            <td>{{ $record->valor_minuto_espera }}</td>
        </tr>
        <tr>
            <th>Vigente Desde</th>
            <td>{{ $record->vigente_desde }}</td>
        </tr>
        <tr>
            <th>Vigente Hasta</th>
            <td>{{ $record->vigente_hasta }}</td>
        </tr>
        <tr>
            <th>Activa</th>
            <td>{{ $record->activa }}</td>
        </tr>
        <tr>
            <th>Prioridad</th>
            <td>{{ $record->prioridad }}</td>
        </tr>
        <tr>
            <th>Version</th>
            <td>{{ $record->version }}</td>
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