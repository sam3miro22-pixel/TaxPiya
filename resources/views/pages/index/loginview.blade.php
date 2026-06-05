@php
    
    $app = $app ?? null;

    $isConductor = ($app === 'conductor');
    $isPasajero  = ($app === 'pasajero');
    $isAdmin     = (!$isConductor && !$isPasajero);

    if ($isConductor) {
        $saludo   = 'Bienvenido Conductor';
        $subtitle = 'Accede con tu número de celular o correo.';
    } elseif ($isPasajero) {
        $saludo   = 'Bienvenido Pasajero';
        $subtitle = 'Ingresa para solicitar tu taxi fácilmente.';
    } else {
        $saludo   = 'Acceso al Panel Taxpiya';
        $subtitle = 'Ingreso para administradores y personal operativo.';
    }
@endphp

<div class="text-center mb-4">
    <img src="{{ asset('images/logo.png') }}"
         width="88" height="88"
         class="img-fluid rounded-3 txp-logo-glow"
         alt="Taxpiya" />
    <div class="h4 fw-bold mt-3 text-warning">{{ $saludo }}</div>
    <div class="small txp-text-70">{{ $subtitle }}</div>
</div>

@if($errors->any())
    <div class="alert alert-danger animated bounce">{{ $errors->first() }}</div>
@endif

<form name="loginForm" action="{{ route('auth.login') }}" class="needs-validation form page-form" method="post" novalidate>
    @csrf

    @if($isConductor || $isPasajero)
        <input type="hidden" name="app" value="{{ $isConductor ? 'conductor' : 'pasajero' }}"/>
    @endif

    <div class="mb-3">
        <label class="form-label txp-text-80 small fw-semibold">Celular o correo</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text txp-ipt-pre">
                <i class="fa fa-user"></i>
            </span>
            <input name="username" required type="text" class="form-control txp-ipt"/>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label txp-text-80 small fw-semibold">Contraseña</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text txp-ipt-pre">
                <i class="fa fa-lock"></i>
            </span>
            <input name="password" required type="password" class="form-control txp-ipt"/>
        </div>
    </div>

    <div class="form-check mt-2 mb-1">
        <input value="true" type="checkbox" name="rememberme" class="form-check-input" id="rememberme" />
        <label for="rememberme" class="form-check-label txp-text-80">Recuérdame</label>
    </div>

    <div class="d-grid gap-3 mt-4">
        <button class="btn btn-brand btn-lg w-100" type="submit">
            <i class="fa fa-sign-in mr-2 me-2"></i> Iniciar sesión
        </button>

        @if($isPasajero)
            <a href="{{ route('auth.register') }}" class="btn btn-outline-light btn-lg w-100 txp-btn-create">
                <i class="fa fa-user-plus mr-2 me-2"></i> Crea tu cuenta
            </a>
        @endif
    </div>
</form>

@include('components.firebase-auth-ui', ['app' => $app ?? null])

@if($isConductor)
    <div class="small txp-text-70 text-center mt-4">
        ¿Aún no tienes acceso? Solicítalo con el Administrador.
    </div>
@elseif($isAdmin)
    <div class="small txp-text-70 text-center mt-4">
        Acceso restringido para administradores y personal autorizado.
    </div>
@endif

@push('styles')
<style>
    :root{
        --txp-brand: #FFB703;
        --txp-brand-2: #FB8500;
    }

    .txp-text-70{ color: rgba(255,255,255,0.7); }
    .txp-text-80{ color: rgba(255,255,255,0.85); }

    .txp-logo-glow{
        filter: drop-shadow(0 8px 18px rgba(251,133,0,.35));
    }

    .txp-ipt{
        background: rgba(255,255,255,0.95);
        border: 0;
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        padding: 12px 14px;
    }
    .txp-ipt:focus{
        box-shadow: 0 0 0 .25rem rgba(251,133,0,.25);
    }
    .txp-ipt-pre{
        background: rgba(0,0,0,.35);
        color: #fff;
        border: 0;
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        width: 54px;
        justify-content: center;
    }

    .btn-brand{
        background: linear-gradient(135deg, var(--txp-brand), var(--txp-brand-2));
        color: #1a1a1a;
        border: 0;
        border-radius: 14px;
        font-weight: 700;
    }
    .btn-brand:hover{
        filter: brightness(.95);
        color: #111;
    }

    .txp-btn-create{
        border-radius: 14px;
        border: 2px solid rgba(255,255,255,.35) !important;
        color: #fff;
        font-weight: 600;
    }
    .txp-btn-create:hover{
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.55) !important;
    }
</style>
@endpush
