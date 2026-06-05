@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Role Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Role Id</th>
            <td>{{ $record->role_id }}</td>
        </tr>
        <tr>
            <th>Role Name</th>
            <td>{{ $record->role_name }}</td>
        </tr>
    </tbody>
</table>
@endsection