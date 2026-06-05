@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Sos Incidente Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Actor Tipo</th>
            <td>{{ $record->actor_tipo }}</td>
        </tr>
        <tr>
            <th>Actor User Id</th>
            <td>{{ $record->actor_user_id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>Categoria</th>
            <td>{{ $record->categoria }}</td>
        </tr>
        <tr>
            <th>Severidad</th>
            <td>{{ $record->severidad }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Descripcion</th>
            <td>{{ $record->descripcion }}</td>
        </tr>
        <tr>
            <th>Telefono Contacto</th>
            <td>{{ $record->telefono_contacto }}</td>
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
            <th>Operador Id</th>
            <td>{{ $record->operador_id }}</td>
        </tr>
        <tr>
            <th>Asignado At</th>
            <td>{{ $record->asignado_at }}</td>
        </tr>
        <tr>
            <th>Reconocido At</th>
            <td>{{ $record->reconocido_at }}</td>
        </tr>
        <tr>
            <th>Atendido At</th>
            <td>{{ $record->atendido_at }}</td>
        </tr>
        <tr>
            <th>Resuelto At</th>
            <td>{{ $record->resuelto_at }}</td>
        </tr>
        <tr>
            <th>Cerrado At</th>
            <td>{{ $record->cerrado_at }}</td>
        </tr>
        <tr>
            <th>Nivel Escalamiento</th>
            <td>{{ $record->nivel_escalamiento }}</td>
        </tr>
        <tr>
            <th>Sla Minutos</th>
            <td>{{ $record->sla_minutos }}</td>
        </tr>
        <tr>
            <th>Breach At</th>
            <td>{{ $record->breach_at }}</td>
        </tr>
        <tr>
            <th>Contacto Inicial</th>
            <td>{{ $record->contacto_inicial }}</td>
        </tr>
        <tr>
            <th>Contacto Resultado</th>
            <td>{{ $record->contacto_resultado }}</td>
        </tr>
        <tr>
            <th>Evidencia Url</th>
            <td>{{ $record->evidencia_url }}</td>
        </tr>
        <tr>
            <th>Notas Operacion</th>
            <td>{{ $record->notas_operacion }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $record->created_at }}</td>
        </tr>
    </tbody>
</table>
@endsection