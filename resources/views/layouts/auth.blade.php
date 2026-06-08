<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'TaxPiya')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070b18">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ secure_asset('css/taxpiya-auth.css') }}?v=3">
    <link rel="stylesheet" href="{{ secure_asset('css/dropzone.min.css') }}">
    @yield('pagecss')
    <script src="{{ secure_asset('js/jquery-3.3.1.min.js') }}"></script>
    <script>
        var siteAddr = "{{ url('') }}/";
        var csrfToken = "{{ csrf_token() }}";
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });
    </script>
</head>
<body class="txp-auth-body @yield('auth_body_class')">
    @yield('content')
    @if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
        @include('components.firebase-auth')
    @endif
    @stack('firebase-scripts')
    <script src="{{ secure_asset('js/plugins/dropzone.min.js') }}"></script>
    <script src="{{ secure_asset('js/page-scripts.js') }}"></script>
    <script src="{{ secure_asset('js/form-page-scripts.js') }}"></script>
    @yield('pagejs')
    @include('components.firebase-session-guard', ['firebaseApp' => request()->is('conductor*') ? 'conductor' : 'pasajero'])
</body>
</html>
