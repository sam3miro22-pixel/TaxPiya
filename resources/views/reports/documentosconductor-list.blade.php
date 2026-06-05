@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Documentos Conductor</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Conductor Id</th>
            <th>Tipo</th>
            <th>Numero</th>
            <th>Emisor</th>
            <th>Expedido At</th>
            <th>Expira At</th>
            <th>Archivo Url</th>
            <th>Archivo Mime</th>
            <th>Archivo Size Kb</th>
            <th>Hash Sha256</th>
            <th>Estado Verificacion</th>
            <th>Verificado Por</th>
            <th>Verificado At</th>
            <th>Rechazo Motivo</th>
            <th>Notas</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->tipo }}</td>
            <td>{{ $record->numero }}</td>
            <td>{{ $record->emisor }}</td>
            <td>{{ $record->expedido_at }}</td>
            <td>{{ $record->expira_at }}</td>
            <td>{{ $record->archivo_url }}</td>
            <td>{{ $record->archivo_mime }}</td>
            <td>{{ $record->archivo_size_kb }}</td>
            <td>{{ $record->hash_sha256 }}</td>
            <td>{{ $record->estado_verificacion }}</td>
            <td>{{ $record->verificado_por }}</td>
            <td>{{ $record->verificado_at }}</td>
            <td>{{ $record->rechazo_motivo }}</td>
            <td>{{ $record->notas }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection