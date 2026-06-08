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
    <link rel="stylesheet" href="{{ asset('css/bootstrap-theme-litera.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/taxpiya-auth.css') }}?v=3">
    @yield('pagecss')
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script>
        var siteAddr = "{{ url('') }}/";
        var csrfToken = "{{ csrf_token() }}";
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });
    </script>
</head>
<body class="txp-info-body" style="background:#0b1220;color:#e5e7eb;min-height:100vh;">
    <header class="txp-info-header py-3 mb-4" style="background:#111827;border-bottom:1px solid #1f2937;">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="text-decoration-none text-light fw-bold" style="font-family:Outfit,sans-serif;font-size:1.25rem;">
                <i class="fa-solid fa-taxi text-warning me-2"></i> TaxPiya
            </a>
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light">
                <i class="fa-solid fa-house"></i> Inicio
            </a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="text-center text-muted py-4 mt-5" style="border-top:1px solid #1f2937;">
        <small>&copy; {{ date('Y') }} TaxPiya</small>
    </footer>
    @yield('pagejs')
</body>
</html>
