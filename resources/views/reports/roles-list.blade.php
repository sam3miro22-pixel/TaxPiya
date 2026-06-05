@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Roles</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Role Id</th>
            <th>Role Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->role_id }}</td>
            <td>{{ $record->role_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection