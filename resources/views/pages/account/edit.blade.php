<?php $pageTitle = 'Editar mi cuenta'; ?>
@extends($layout)
@section('title', $pageTitle)
@section('content')
<section class="page">
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-user-edit me-2"></i>Editar mi cuenta</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="post" action="{{ url('account/edit') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="name">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $data['name'] ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $data['telefono'] ?? '') }}">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar
                                </button>
                                <a href="{{ url('account') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                        <hr class="my-4 border-secondary">
                        <h6 class="mb-3">Cambiar contraseña</h6>
                        <form method="post" action="{{ route('account.changepassword') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="oldpassword">Contraseña actual</label>
                                <input type="password" class="form-control" id="oldpassword" name="oldpassword" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="newpassword">Nueva contraseña</label>
                                <input type="password" class="form-control" id="newpassword" name="newpassword" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="confirmpassword">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-key"></i> Cambiar contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
