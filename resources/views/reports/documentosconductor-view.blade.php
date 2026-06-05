@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Documentos Conductor Details</h1></div>
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
            <th>Tipo</th>
            <td>{{ $record->tipo }}</td>
        </tr>
        <tr>
            <th>Numero</th>
            <td>{{ $record->numero }}</td>
        </tr>
        <tr>
            <th>Emisor</th>
            <td>{{ $record->emisor }}</td>
        </tr>
        <tr>
            <th>Expedido At</th>
            <td>{{ $record->expedido_at }}</td>
        </tr>
        <tr>
            <th>Expira At</th>
            <td>{{ $record->expira_at }}</td>
        </tr>
        <tr>
            <th>Archivo Url</th>
            <td>{{ $record->archivo_url }}</td>
        </tr>
        <tr>
            <th>Archivo Mime</th>
            <td>{{ $record->archivo_mime }}</td>
        </tr>
        <tr>
            <th>Archivo Size Kb</th>
            <td>{{ $record->archivo_size_kb }}</td>
        </tr>
        <tr>
            <th>Hash Sha256</th>
            <td>{{ $record->hash_sha256 }}</td>
        </tr>
        <tr>
            <th>Estado Verificacion</th>
            <td>{{ $record->estado_verificacion }}</td>
        </tr>
        <tr>
            <th>Verificado Por</th>
            <td>{{ $record->verificado_por }}</td>
        </tr>
        <tr>
            <th>Verificado At</th>
            <td>{{ $record->verificado_at }}</td>
        </tr>
        <tr>
            <th>Rechazo Motivo</th>
            <td>{{ $record->rechazo_motivo }}</td>
        </tr>
        <tr>
            <th>Notas</th>
            <td>{{ $record->notas }}</td>
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