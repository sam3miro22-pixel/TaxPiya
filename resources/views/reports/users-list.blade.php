@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Users</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Fotoperfil</th>
            <th>Remember Token</th>
            <th>Estado</th>
            <th>Telefono</th>
            <th>User Role Id</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->name }}</td>
            <td>{{ $record->email }}</td>
            <td>{{ $record->fotoperfil }}</td>
            <td>{{ $record->remember_token }}</td>
            <td>{{ $record->estado }}</td>
            <td>{{ $record->telefono }}</td>
            <td>{{ $record->user_role_id }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection