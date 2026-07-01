
<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title')</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<link rel="shortcut icon" href="{{ asset(config('app.logo')) }}" />

		<meta name="theme-color" content="#000000" />

		<meta name="author" content="" />
		<meta name="keyword" content="" />
		<meta name="description" content="" />

		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
		<link rel="stylesheet" href="{{ asset('css/mobile-app.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
		<link rel="stylesheet" href="{{ asset('css/animate.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/blueimp-gallery.css') }}" />
		<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">


		@if(config('settings.csslib') && count(config('settings.csslib')) > 0)
			@foreach(config('settings.csslib') as $name => $link)
				@if(filter_var($link, FILTER_VALIDATE_URL)) {{-- Check if it's a valid web URL --}}
					<link rel="stylesheet" href="{{ $link }}" type="text/css" media="all" crossorigin="anonymous" />
				@else {{-- Treat as a relative file path --}}
					<link rel="stylesheet" href="{{ asset($link) }}" type="text/css" media="all" crossorigin="anonymous" />
				@endif
			@endforeach
		@endif
<link rel="stylesheet" href="{{ asset('css/bootstrap-theme-litera.css') }}" />
	<link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/custom-style.css') }}" />

		<script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
		



		@yield('pagecss')
		@stack('styles')
		@yield('plugins')
		<script>
			var siteAddr = "{{ url('') }}/";
			var defaultPageLimit = 20;
			var csrfToken = "{{ csrf_token() }}";
			var requestErrorMessage = "Unable to complete request";
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': csrfToken
				}
			});
			
		</script>
		
		<style>
		
    :root{
        --txp-bg-1: #0b132b;
        --txp-bg-2: #1c2541;
        --txp-bg-3: #3a506b;
        --txp-brand: #ffd166;
        --txp-primary: #4c6ef5; /* azul moderno para botones */
        --txp-white: #ffffff;
    }
    html, body{ height: 100%; }
    body{
        font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        background: var(--txp-bg-1);
        color: var(--txp-white);
    }
    /* Helpers */
    .btn-pill{ border-radius: 999px; }
    .shadow-xl{ box-shadow: 0 25px 70px rgba(0,0,0,.45) !important; }
	[style*="padding-top:"] { padding-top: 0 !important; }
	
</style>
	</head>
	<?php
		$body_id = "index";
		if(auth()->check()){
			$body_id = "main";
		}
		$page_name = request()->segment(1) ?? 'index';
		$page_action = request()->segment(2) ?? 'index';
		$body_class = "$page_name-$page_action";
		$auth_routes = ['pasajero.login', 'conductor.login', 'auth.register', 'pasajero.register'];
		$is_auth_page = request()->routeIs($auth_routes)
			|| ($page_name === 'index' && in_array($page_action, ['login', 'register'], true));
		$body_extra = $is_auth_page ? ' txp-auth-page' : '';
		$isAdmin = auth()->check() && ((int) auth()->user()->user_role_id === 1);
		$withLoginClass = $isAdmin ? '' : 'with-login';
	?>
	<body id="<?php echo $body_id ?>" class="<?php echo $withLoginClass ?> <?php echo $body_class ?><?php echo $body_extra ?>">

		<div id="page-wrapper">
			
			<div id="ajax-progress-bar" class="progress"  style="display:none">
				<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0"></div>
			</div>
			@include('appheader')
			<div id="main-content">
				
					<div id="page-content">
						@yield('content')
					</div>
				
				@include('appfooter')
			
				<div id="main-page-modal" class="modal right fade" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body reset-grids inline-page">

							</div>
							<div style="top: 15px; right:5px; z-index: 999;" class="position-absolute">
								<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="modal"
							aria-label="Close"></button>

							</div>
						</div>
					</div>
				</div>

			
				<div class="offcanvas offcanvas-end" tabindex="-1" id="sidedrawer-page-modal">
					<div class="position-absolute" style="top: 20px; right:15px; z-index: 999;">
						<button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
					</div>
					<div class="offcanvas-body reset-grids inline-page">

					</div>
				</div>
			
				<div class="modal fade" id="delete-record-modal-confirm" tabindex="-1" role="dialog" aria-labelledby="delete-record-modal-confirm" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Eliminar el registro</h5>

								<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="modal"
							aria-label="Close"></button>
							</div>
							<div id="delete-record-modal-msg" class="modal-body"></div>

							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
								<a href="" id="delete-record-modal-btn" class="btn btn-primary">Borrar</a>
							</div>

						</div>
					</div>
				</div>

				
				<div id="preview-img-modal" class="modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered mx-auto modal-lg">
						<div class="modal-content mx-auto" style="width:auto;">
							<div class="modal-body p-0 d-flex position-relative">
								<img style="width:auto; max-width:100%; max-height:90vh;" class="mx-auto img" />
								<button style="top: 10px; right:10px; z-index: 999;" type="button" class="btn-close btn-close-white m-2 position-absolute" data-bs-dismiss="modal"></button>
							</div>
						</div>
					</div>
				</div>
				<template id="saving-indicator">
					<div class="p-2 text-center m-2 text-muted">
						<div class="lds-dual-ring"></div>
						<h4 class="p-3 mt-2 font-weight-light">Guardando registro</h4>
					</div>
				</template>

				<template id="loading-indicator">
					<div class="p-2 text-center d-flex justify-content-center align-items-center">
						<span class="loader mr-3"></span>
						<span class="px-2 text-muted font-weight-light">Cargando...</span>
					</div>
				</template>
			</div>

			<div class="toast-container fixed-alert top-0 start-50 translate-middle-x pt-3" style="z-index: 3000;">

				<div id="app-toast-success" data-bs-autohide="true" data-bs-delay="5000" class="animated bounceIn toast text-bg-success position-relative" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="toast-message">
						<div class="toast-header">
							<strong class="me-auto">@yield('title')</strong>
							<small>
								<span class="badge rounded-pill bg-success">
									@lang('validation.messages.success')
								</span>
							</small>
							<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
						</div>
						<div class="toast-body">
							{{ Session::get('success') }}
						</div>
					</div>
				</div>

				<div id="app-toast-danger" data-bs-autohide="true" data-bs-delay="5000" class="animated bounceIn toast text-bg-danger position-relative" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="toast-message">
						<div class="toast-header">
							<strong class="me-auto">@yield('title')</strong>
							<small>
								<span class="badge rounded-pill bg-danger">
									@lang('validation.messages.error')
								</span>
							</small>
							<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
						</div>
						<div class="toast-body">
							{{ Session::get('danger') }}
						</div>
					</div>
				</div>

			</div>
		</div>

		<script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/plugins/app-plugins.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/plugins/flatpickr.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/locale/flatpickr/spanish.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/plugins/dropzone.min.js') }}"></script>
		@if(config('settings.jslib') && count(config('settings.jslib')) > 0)
			@foreach(config('settings.jslib') as $name => $link)
				@if(filter_var($link, FILTER_VALIDATE_URL)) {{-- Check if it's a valid web URL --}}
					<script src="{{ $link }}" type="text/javascript" defer crossorigin="anonymous"></script>
				@else {{-- Treat as a relative file path --}}
					<script src="{{ asset($link) }}" type="text/javascript" defer crossorigin="anonymous"></script>
				@endif
			@endforeach
		@endif

		<script type="text/javascript" src="{{ asset('js/page-scripts.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/form-page-scripts.js') }}"></script>
		@yield('pagejs')
		@auth
			@include('components.assistant-chat')
		@endauth
		<script>
	$('#sidebarCollapse').on('click', function () {
		$('#sidebar, #main-content').toggleClass('active');
	});
	$(function () {
		if ($('body').hasClass('txp-auth-page') || $('.auth-wrap').length) {
			document.body.style.paddingTop = '0';
			return;
		}
		if ($('.topbar:visible, .navbar:visible').length && $('#topbar').length) {
			var navTopHeight = $('#topbar').outerHeight() + 'px';
			document.body.style.paddingTop = navTopHeight;
			$('#sidebar').css('top', navTopHeight);
			$('#sidebar').css('height', 'calc(100vh - ' +  navTopHeight + ')');
		}
	});
</script>
		<script>
			window.onload = (event) => {
				if (document.body.classList.contains('txp-auth-page')) return;
				@if (Session::has('success'))
					let successAlert = document.getElementById('app-toast-success');
					let bsAlert = new bootstrap.Toast(successAlert);
					bsAlert.show();
				@endif

				@if (Session::has('danger'))
					let errorAlert = document.getElementById('app-toast-danger');
					let bsAlert = new bootstrap.Toast(errorAlert);
					bsAlert.show();
				@endif
			}
		</script>
	</body>
</html>
