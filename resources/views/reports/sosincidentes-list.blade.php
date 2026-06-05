@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Sos Incidentes</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Viaje Id</th>
            <th>Actor Tipo</th>
            <th>Actor User Id</th>
            <th>Conductor Id</th>
            <th>Categoria</th>
            <th>Severidad</th>
            <th>Estado</th>
            <th>Descripcion</th>
            <th>Telefono Contacto</th>
            <th>Lat</th>
            <th>Lng</th>
            <th>Ubicacion</th>
            <th>Operador Id</th>
            <th>Asignado At</th>
            <th>Reconocido At</th>
            <th>Atendido At</th>
            <th>Resuelto At</th>
            <th>Cerrado At</th>
            <th>Nivel Escalamiento</th>
            <th>Sla Minutos</th>
            <th>Breach At</th>
            <th>Contacto Inicial</th>
            <th>Contacto Resultado</th>
            <th>Evidencia Url</th>
            <th>Notas Operacion</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->actor_tipo }}</td>
            <td>{{ $record->actor_user_id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->categoria }}</td>
            <td>{{ $record->severidad }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->descripcion }}</td>
            <td>{{ $record->telefono_contacto }}</td>
            <td>{{ $record->lat }}</td>
            <td>{{ $record->lng }}</td>
            <td>{{ $record->ubicacion }}</td>
            <td>{{ $record->operador_id }}</td>
            <td>{{ $record->asignado_at }}</td>
            <td>{{ $record->reconocido_at }}</td>
            <td>{{ $record->atendido_at }}</td>
            <td>{{ $record->resuelto_at }}</td>
            <td>{{ $record->cerrado_at }}</td>
            <td>{{ $record->nivel_escalamiento }}</td>
            <td>{{ $record->sla_minutos }}</td>
            <td>{{ $record->breach_at }}</td>
            <td>{{ $record->contacto_inicial }}</td>
            <td>{{ $record->contacto_resultado }}</td>
            <td>{{ $record->evidencia_url }}</td>
            <td>{{ $record->notas_operacion }}</td>
            <td>{{ $record->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection