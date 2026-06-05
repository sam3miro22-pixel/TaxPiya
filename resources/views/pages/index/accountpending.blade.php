@extends('layouts.info')
@section('content')
<div class="container">
    <div class="my-4 text-center p-4 card-4">
        <i class="material-icons mi-lg text-warning">account_box</i>
        <div class="h4 text-bold text-warning my-3">
            Su cuenta está esperando revisión
        </div>
        <div class="text-muted">
            Póngase en contacto con el administrador del sistema para obtener más información
        </div>
        <hr class="my-md" />
        <a href="{{ url('/') }}" class="btn btn-primary"><i class="material-icons">home</i> Continuar</a>
    </div>
</div>
@endsection