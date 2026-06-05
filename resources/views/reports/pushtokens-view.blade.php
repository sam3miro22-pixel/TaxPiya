@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Push Token Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Dispositivo Id</th>
            <td>{{ $record->dispositivo_id }}</td>
        </tr>
        <tr>
            <th>Provider</th>
            <td>{{ $record->provider }}</td>
        </tr>
        <tr>
            <th>Token</th>
            <td>{{ $record->token }}</td>
        </tr>
        <tr>
            <th>Token Hash</th>
            <td>{{ $record->token_hash }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Scope</th>
            <td>{{ $record->scope }}</td>
        </tr>
        <tr>
            <th>Ultimo Uso At</th>
            <td>{{ $record->ultimo_uso_at }}</td>
        </tr>
        <tr>
            <th>Invalidado At</th>
            <td>{{ $record->invalidado_at }}</td>
        </tr>
        <tr>
            <th>Motivo Invalidez</th>
            <td>{{ $record->motivo_invalidez }}</td>
        </tr>
        <tr>
            <th>Idempotencia</th>
            <td>{{ $record->idempotencia }}</td>
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