@extends('layouts.info')
@section('content')
    <div class="container mt-4">
       
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-body my-5">
					<h3>Administrador de restablecimiento de contraseña</h3>
					<hr />
                    <section>
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form role="form" method="POST" action="{{ route('password.resetpassword') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ request()->token }}">
                            <input type="hidden" name="email" value="{{ request()->email }}">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label>Nueva contraseña</label>
                                <input required type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" />
                            </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label>Confirmar nueva contraseña</label>
                                <input required type="password" class="form-control" id="confirm_password" name="confirm_password"
                                    placeholder="Confirm Password" />
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-success" type="submit">Cambia la contraseña</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
