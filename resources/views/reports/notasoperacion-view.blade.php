@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Notas Operacion Details</h1></div>
<table class="table table-sm table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ $record->id }}</td>
        </tr>
        <tr>
            <th>Entity Type</th>
            <td>{{ $record->entity_type }}</td>
        </tr>
        <tr>
            <th>Entity Id</th>
            <td>{{ $record->entity_id }}</td>
        </tr>
        <tr>
            <th>Viaje Id</th>
            <td>{{ $record->viaje_id }}</td>
        </tr>
        <tr>
            <th>Conductor Id</th>
            <td>{{ $record->conductor_id }}</td>
        </tr>
        <tr>
            <th>User Id</th>
            <td>{{ $record->user_id }}</td>
        </tr>
        <tr>
            <th>Titulo</th>
            <td>{{ $record->titulo }}</td>
        </tr>
        <tr>
            <th>Nota</th>
            <td>{{ $record->nota }}</td>
        </tr>
        <tr>
            <th>Adjunto Url</th>
            <td>{{ $record->adjunto_url }}</td>
        </tr>
        <tr>
            <th>Adjunto Mime</th>
            <td>{{ $record->adjunto_mime }}</td>
        </tr>
        <tr>
            <th>Visibilidad</th>
            <td>{{ $record->visibilidad }}</td>
        </tr>
        <tr>
            <th>Pinned</th>
            <td>{{ $record->pinned }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>{{ $record->created_by }}</td>
        </tr>
        <tr>
            <th>Created By Rol</th>
            <td>{{ $record->created_by_rol }}</td>
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