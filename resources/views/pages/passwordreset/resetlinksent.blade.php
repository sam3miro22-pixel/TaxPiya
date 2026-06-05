@extends('layouts.info')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-body my-5">
                    <h4><i class="fa fa-envelope"></i> Administrador de restablecimiento de contraseña</h4>
                    <hr />
                    <div class="">
                        <h5 class="text-info">
                            Se ha enviado un mensaje a su correo electrónico. Siga amablemente el enlace para restablecer su contraseña
                        </h5>
                        <hr />
                        <div class="text-center">
                            <a href="<?php print_link('/'); ?>" class="btn btn-info">Haga clic aquí para ingresar</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
