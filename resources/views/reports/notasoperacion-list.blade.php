@extends('layouts.report')
@section('content')
<div id="report-title"><h1>Notas Operacion</h1></div>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Entity Type</th>
            <th>Entity Id</th>
            <th>Viaje Id</th>
            <th>Conductor Id</th>
            <th>User Id</th>
            <th>Titulo</th>
            <th>Nota</th>
            <th>Adjunto Url</th>
            <th>Adjunto Mime</th>
            <th>Visibilidad</th>
            <th>Pinned</th>
            <th>Created By</th>
            <th>Created By Rol</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->entity_type }}</td>
            <td>{{ $record->entity_id }}</td>
            <td>{{ $record->viaje_id }}</td>
            <td>{{ $record->conductor_id }}</td>
            <td>{{ $record->user_id }}</td>
            <td>{{ $record->titulo }}</td>
            <td>{{ $record->nota }}</td>
            <td>{{ $record->adjunto_url }}</td>
            <td>{{ $record->adjunto_mime }}</td>
            <td>{{ $record->visibilidad }}</td>
            <td>{{ $record->pinned }}</td>
            <td>{{ $record->created_by }}</td>
            <td>{{ $record->created_by_rol }}</td>
            <td>{{ $record->created_at }}</td>
            <td>{{ $record->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection