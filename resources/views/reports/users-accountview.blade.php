@extends('layouts.report')
@section('content')
<div id="report-title"><h1>User Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{ $record->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $record->email }}</td>
        </tr>
        <tr>
            <th>Remember Token</th>
            <td>{{ $record->remember_token }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $record->estado }}</td>
        </tr>
        <tr>
            <th>Telefono</th>
            <td>{{ $record->telefono }}</td>
        </tr>
        <tr>
            <th>User Role Id</th>
            <td>{{ $record->user_role_id }}</td>
        </tr>
    </tbody>
</table>
@endsection