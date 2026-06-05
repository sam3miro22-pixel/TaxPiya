@extends('layouts.info')
@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6 col-12">
			<div class="my-5 card-body">
				@if(session()->has('message'))
					<div class="alert alert-info animated bounce">
					<i class="text-success fa fa-check-circle"></i> {{ session()->get('message') }}
					</div>
				@endif
				
				<div class="row">
					<div class="col-md-auto col-12 text-center">
						<i class="mi-lg text-success fa fa-check-circle"></i>
					</div>
					<div class="col">
						<h3 class="text-primary mb-2 font-weight-bold">Verificación OTP</h3>
						<p class="text-muted">
							Se ha enviado OTP a su número de teléfono a través de SMS
						</p>
					</div>
					<div class="col-auto">
						<div id="countdown-timer" class="h4 text-bold text-info">--:--</div>
					</div>
				</div>
				<?php Html::display_page_errors($errors); ?>
				<form method="POST" action="{{ route('auth.validateotp') }}">
					@csrf
					<div class="form-group mb-3">
						<input name="otp_code" type="text" class="form-control otp-input" required autofocus placeholder="Ingresar OTP">
					</div>

					<div class="row justify-content-between">
						<div class="col">
							<span class="text-muted">Didn't receive a message</span> ?
							<a id="btn-resendotp" class="btn btn-link disabled" href="{{route('auth.resendotp')}}">
								Resend...
							</a>
						</div>
						<div class="col-auto">
							<button type="submit" class="btn btn-primary">
							Verificar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection



@section('pagecss')
<style>
	.otp-input {
		font-weight: bold;
		font-size: 20px;
		text-align: center;
		letter-spacing: 10px;
	}

	.otp-input::placeholder {
		font-weight: normal;
		font-size: 14px;
		text-align: center;
		letter-spacing: 1px;
	}
</style>
@endsection

@section('pagejs')
<script>
	$(document).ready(function(){
		startCountDown();
	});

	function startCountDown(){
		let duration = <?php echo  config("auth.otp_duration") ?>;
		let minutes = 60 * duration;
		let timer = minutes;
		let seconds;
		const interval = setInterval(function() {
			minutes = parseInt(timer / 60, 10);
			seconds = parseInt(timer % 60, 10);
			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;
			$('#countdown-timer').text(minutes + ":" + seconds);
			if (--timer < 0) {
				clearInterval(interval);
				$('#btn-resendotp').removeClass('disabled');
			}
		}, 1000);
	}
</script>
@endsection