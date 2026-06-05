<?php 
	$user_id = $id ?? request()->id;
?>
@extends('layouts.info')
@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card card-body my-5">
				<div class="card-header"><i class="fa fa-envelope"></i> {{ __('Verify Your Email Address') }}</div>
				<div class="card-body">
					
					<?php Html::display_page_errors($errors); ?>
				
					@if (!empty($message))
					<div class="alert alert-success animated bounce" role="alert">
						{{ $message }}
					</div>
					@endif
					
					Verifique su dirección de correo electrónico siguiendo el enlace en su casilla de correo

					<hr />
					<div class="text-center">
						<a class="btn btn-sm btn-info" href="{{ route('verification.resend', ['id' => $user_id]) }}">
							<i class="fa fa-envelope"></i> Reenviar email
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection