@inject('comp_model', 'App\Models\ComponentsData')
@php
    $pageTitle = 'Crear cuenta (Pasajero)';
@endphp

@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-scene txp-auth-scene--scroll">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <div class="txp-auth-logo-wrap">
                <img src="{{ asset('images/logo.png') }}" width="82" height="82" class="txp-auth-logo" alt="Taxpiya">
            </div>
            <h1 class="txp-auth-title">Únete como <span>Pasajero</span></h1>
            <p class="txp-auth-subtitle">Crea tu cuenta y pide taxi en segundos.</p>
        </div>

        @if($errors->any())
            <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first() }}</div>
        @endif

        <form id="users-userregister-form" role="form" novalidate enctype="multipart/form-data"
              class="form page-form" action="{{ route('auth.register_store') }}" method="post">
            @csrf

            <div class="txp-auth-field">
                <label class="txp-auth-label" for="ctrl-telefono">Móvil <span class="req">*</span></label>
                <div class="txp-auth-input-wrap">
                    <i class="fa-solid fa-phone txp-auth-input-icon"></i>
                    <input id="ctrl-telefono" name="telefono" type="tel" class="txp-auth-input"
                           placeholder="300 123 4567" value="{{ old('telefono') }}" required autocomplete="tel">
                </div>
            </div>

            <div class="txp-auth-field">
                <label class="txp-auth-label" for="ctrl-password">Contraseña <span class="req">*</span></label>
                <div class="txp-auth-input-wrap">
                    <i class="fa-solid fa-lock txp-auth-input-icon"></i>
                    <input id="ctrl-password" name="password" type="password" class="txp-auth-input"
                           placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
                    <button type="button" class="txp-auth-eye txp-toggle-pass" tabindex="-1" aria-label="Ver contraseña">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="txp-auth-field">
                <label class="txp-auth-label" for="ctrl-name">Nombre <span class="req">*</span></label>
                <div class="txp-auth-input-wrap">
                    <i class="fa-solid fa-user txp-auth-input-icon"></i>
                    <input id="ctrl-name" name="name" type="text" class="txp-auth-input"
                           placeholder="Tu nombre completo" value="{{ old('name') }}" required
                           data-url="componentsdata/users_name_value_exist/"
                           data-loading-msg="Comprobando..." data-available-msg="Disponible" data-unavailable-msg="No disponible">
                </div>
                <div class="check-status small mt-1"></div>
            </div>

            <div class="txp-auth-field">
                <label class="txp-auth-label" for="ctrl-email">Email <span class="req">*</span></label>
                <div class="txp-auth-input-wrap">
                    <i class="fa-solid fa-envelope txp-auth-input-icon"></i>
                    <input id="ctrl-email" name="email" type="email" class="txp-auth-input"
                           placeholder="correo@ejemplo.com" value="{{ old('email') }}" required autocomplete="email"
                           data-url="componentsdata/users_email_value_exist/"
                           data-loading-msg="Comprobando..." data-available-msg="Disponible" data-unavailable-msg="No disponible">
                </div>
                <div class="check-status small mt-1"></div>
            </div>

            <div class="txp-auth-field">
                <label class="txp-auth-label">Foto de perfil <span style="font-weight:400;opacity:.7">(opcional)</span></label>
                <div id="ctrl-fotoperfil-holder">
                    <div class="dropzone dz-light txp-auth-dropzone" input="#ctrl-fotoperfil" fieldname="fotoperfil"
                         uploadurl="{{ url('fileuploader/upload/fotoperfil') }}"
                         data-multiple="false" dropmsg="Toca para subir tu foto"
                         btntext="Elegir foto" extensions=".jpg,.png,.gif,.jpeg"
                         filesize="50" maximum="1">
                        <input name="fotoperfil" id="ctrl-fotoperfil" class="dropzone-input form-control" value="{{ old('fotoperfil') }}" type="text">
                        <div class="dz-file-limit text-danger small mt-2"></div>
                    </div>
                </div>
            </div>

            <div class="txp-auth-actions">
                <button class="txp-auth-btn txp-auth-btn--primary" type="submit">
                    <i class="fa-solid fa-user-plus"></i> Crear cuenta
                </button>
            </div>

            <p class="txp-auth-foot">
                ¿Ya tienes cuenta?
                <a href="{{ route('pasajero.login') }}" class="txp-auth-link">Inicia sesión</a>
            </p>
        </form>

        @if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
        <div class="txp-firebase-auth mt-2">
            <div class="txp-auth-divider">Registro rápido</div>
            <div class="d-grid gap-2">
                <button type="button" class="txp-auth-btn txp-auth-btn--ghost" id="txp-register-firebase-email">
                    <i class="fa-solid fa-envelope"></i> Crear con correo
                </button>
                <button type="button" class="txp-auth-btn txp-auth-btn--google" id="txp-register-firebase-google">
                    <i class="fa-brands fa-google"></i> Crear con Google
                </button>
            </div>
            <div id="txp-register-fb-error" class="txp-auth-alert txp-auth-alert--error mt-2" style="display:none;"></div>
        </div>
        @include('components.firebase-auth')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          if (!window.TaxpiyaFirebase) return;
          window.TaxpiyaFirebase.init();
          const err = document.getElementById('txp-register-fb-error');
          const showErr = (m) => { if (err) { err.textContent = m; err.style.display = 'block'; } };
          const profile = () => ({
            app: 'pasajero',
            name: document.getElementById('ctrl-name')?.value?.trim(),
            telefono: document.getElementById('ctrl-telefono')?.value?.trim(),
          });
          document.getElementById('txp-register-firebase-email')?.addEventListener('click', async () => {
            const email = document.getElementById('ctrl-email')?.value?.trim();
            const pass  = document.getElementById('ctrl-password')?.value || '';
            if (!email || !pass) { showErr('Completa correo y contraseña'); return; }
            try {
              const data = await window.TaxpiyaFirebase.registerEmail(email, pass, profile());
              window.location.href = data?.redirect || '/home';
            } catch (e) { showErr(e.message); }
          });
          document.getElementById('txp-register-firebase-google')?.addEventListener('click', async () => {
            try {
              const data = await window.TaxpiyaFirebase.loginGoogle(profile());
              window.location.href = data?.redirect || '/home';
            } catch (e) { showErr(e.message); }
          });
        });
        </script>
        @endif
    </div>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ asset('css/taxpiya-auth.css') }}">
@endsection

@section('pagejs')
<script>
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.txp-toggle-pass');
  if (!btn) return;
  const input = document.getElementById('ctrl-password');
  const icon = btn.querySelector('i');
  if (!input || !icon) return;
  const show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  icon.classList.toggle('fa-eye', !show);
  icon.classList.toggle('fa-eye-slash', show);
});
</script>
@endsection
