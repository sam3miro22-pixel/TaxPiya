@extends('layouts.info')
@section('content')
<div class="container mt-4">
	<div class="row justify-content-center">
		<div class="col-sm-6">
			<div class="card card-body">
				<h4><i class="fa fa-check-circle"></i> Administrador de restablecimiento de contraseña</h4>
				<hr />	
				<h5 class="animated bounce text-success">
					Tu contraseña ha sido restablecida
				</h5>
				<hr />
				<div class="text-center">
					<a href="<?php print_link("/"); ?>" class="btn btn-info">Haga clic aquí para ingresar</a>
				</div>
			</div>
	
			
		</div>
	</div>
</div>
@endsection