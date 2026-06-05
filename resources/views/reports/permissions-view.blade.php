@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Permission Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Permission Id</th>
            <td>{{ $record->permission_id }}</td>
        </tr>
        <tr>
            <th>Permission</th>
            <td>{{ $record->permission }}</td>
        </tr>
        <tr>
            <th>Role Id</th>
            <td>{{ $record->role_id }}</td>
        </tr>
    </tbody>
</table>
@endsection