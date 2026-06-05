@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Push Tokens</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Dispositivo Id</th>
            <th>Provider</th>
            <th>Token</th>
            <th>Token Hash</th>
            <th>Estado</th>
            <th>Scope</th>
            <th>Ultimo Uso At</th>
            <th>Invalidado At</th>
            <th>Motivo Invalidez</th>
            <th>Idempotencia</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->dispositivo_id }}</td>
            <td>{{ $record->provider }}</td>
            <td>{{ $record->token }}</td>
            <td>{{ $record->token_hash }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->scope }}</td>
            <td>{{ $record->ultimo_uso_at }}</td>
            <td>{{ $record->invalidado_at }}</td>
            <td>{{ $record->motivo_invalidez }}</td>
            <td>{{ $record->idempotencia }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection