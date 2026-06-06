@php
    $app = $app ?? null;
    $isConductor = ($app === 'conductor');
    $isPasajero  = ($app === 'pasajero');
    $isAdmin     = (!$isConductor && !$isPasajero);

    if ($isConductor) {
        $roleLabel = 'Conductor';
        $subtitle  = 'Accede con tu celular o correo para recibir viajes.';
    } elseif ($isPasajero) {
        $roleLabel = 'Pasajero';
        $subtitle  = 'Ingresa para solicitar tu taxi fácilmente.';
    } else {
        $roleLabel = 'Admin';
        $subtitle  = 'Acceso para administradores y personal operativo.';
    }
@endphp

<div class="txp-auth-header">
    <div class="txp-auth-logo-wrap">
        <img src="{{ asset('images/logo.png') }}" width="82" height="82" class="txp-auth-logo" alt="Taxpiya">
    </div>
    <h1 class="txp-auth-title">Bienvenido <span>{{ $roleLabel }}</span></h1>
    <p class="txp-auth-subtitle">{{ $subtitle }}</p>
</div>

@if($errors->any())
    <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first() }}</div>
@endif

<form name="loginForm" action="{{ route('auth.login') }}" class="txp-auth-form page-form" method="post" novalidate>
    @csrf

    @if($isConductor || $isPasajero)
        <input type="hidden" name="app" value="{{ $isConductor ? 'conductor' : 'pasajero' }}">
    @endif

    <div class="txp-auth-field">
        <label class="txp-auth-label" for="txp-username">Celular o correo</label>
        <div class="txp-auth-input-wrap">
            <i class="fa-solid fa-user txp-auth-input-icon"></i>
            <input id="txp-username" name="username" type="text" class="txp-auth-input" placeholder="300 123 4567 o correo@email.com" required autocomplete="username">
        </div>
    </div>

    <div class="txp-auth-field">
        <label class="txp-auth-label" for="txp-password">Contraseña</label>
        <div class="txp-auth-input-wrap">
            <i class="fa-solid fa-lock txp-auth-input-icon"></i>
            <input id="txp-password" name="password" type="password" class="txp-auth-input" placeholder="Tu contraseña" required autocomplete="current-password">
            <button type="button" class="txp-auth-eye txp-toggle-pass" aria-label="Ver contraseña" tabindex="-1">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="txp-auth-check">
        <input value="true" type="checkbox" name="rememberme" id="rememberme">
        <label for="rememberme">Recuérdame</label>
    </div>

    <div class="txp-auth-actions">
        <button class="txp-auth-btn txp-auth-btn--primary" type="submit">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
        </button>

        @if($isPasajero)
            <a href="{{ route('auth.register') }}" class="txp-auth-btn txp-auth-btn--ghost">
                <i class="fa-solid fa-user-plus"></i> Crear cuenta
            </a>
        @endif
    </div>
</form>

@include('components.firebase-auth-ui', ['app' => $app ?? null])

@if($isConductor)
    <p class="txp-auth-foot">
        ¿Sin acceso? Solicítalo al administrador de Taxpiya.
    </p>
@elseif($isAdmin)
    <p class="txp-auth-foot">Acceso restringido a personal autorizado.</p>
@endif

<script>
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.txp-toggle-pass');
  if (!btn) return;
  const wrap = btn.closest('.txp-auth-input-wrap');
  const input = wrap?.querySelector('input');
  const icon = btn.querySelector('i');
  if (!input || !icon) return;
  const show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  icon.classList.toggle('fa-eye', !show);
  icon.classList.toggle('fa-eye-slash', show);
});
</script>
