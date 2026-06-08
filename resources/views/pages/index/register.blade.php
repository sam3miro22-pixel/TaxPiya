@inject('comp_model', 'App\Models\ComponentsData')
@php
    $pageTitle = 'Crear cuenta (Pasajero)';
@endphp

@extends('layouts.auth')
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-scene txp-auth-scene--scroll">
    <div class="txp-auth-orb txp-auth-orb--1"></div>
    <div class="txp-auth-orb txp-auth-orb--2"></div>
    <div class="txp-auth-orb txp-auth-orb--3"></div>
    <div class="txp-auth-card">
        <div class="txp-auth-header">
            <div class="txp-auth-logo-wrap">
                <x-taxpiya-logo />
            </div>
            <h1 class="txp-auth-title">Únete como <span>Pasajero</span></h1>
            <p class="txp-auth-subtitle">Crea tu cuenta y pide taxi en segundos.</p>
        </div>

        @if($errors->any())
            <div class="txp-auth-alert txp-auth-alert--error">{{ $errors->first() }}</div>
        @endif

        <form id="users-userregister-form" role="form" novalidate enctype="multipart/form-data"
              class="form page-form" action="{{ route('pasajero.register_store') }}" method="post">
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

            @include('components.referral-code-field')

            <div class="txp-auth-field">
                <label class="txp-auth-label" for="fotoperfil-file">Foto de perfil <span style="font-weight:400;opacity:.7">(opcional)</span></label>
                <div class="txp-auth-photo-pick">
                    <img id="txp-register-photo-preview" src="" alt="" class="txp-auth-photo-preview" style="display:none;">
                    <label class="txp-auth-photo-btn" for="fotoperfil-file">
                        <i class="fa-solid fa-camera"></i> Elegir foto
                    </label>
                    <input type="file" name="fotoperfil_file" id="fotoperfil-file" accept="image/jpeg,image/png,image/gif,image/webp" class="visually-hidden">
                </div>
                <p class="small text-muted mt-2 mb-0">JPG, PNG o WEBP · máx. 5 MB. Se guarda en el servidor (sin Firebase Storage).</p>
                <input type="hidden" name="fotoperfil" id="ctrl-fotoperfil" value="{{ old('fotoperfil') }}">
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
            <div class="txp-auth-divider">o regístrate con</div>
            <div class="d-grid gap-2">
                <button type="button" class="txp-auth-btn txp-auth-btn--google" id="txp-register-firebase-google">
                    <i class="fa-brands fa-google"></i> Crear con Google
                </button>
            </div>
            <p class="small text-muted mt-2 mb-0">Correo y Google se registran en <strong>Firebase Auth</strong>. La foto de perfil puedes subirla después en Mi perfil.</p>
            <div id="txp-register-fb-error" class="txp-auth-alert txp-auth-alert--error mt-2" style="display:none;"></div>
        </div>
        <script>
        (function () {
          function whenFirebaseReady(cb, tries) {
            tries = tries || 0;
            if (window.TaxpiyaFirebase) return cb();
            if (tries > 80) return;
            setTimeout(() => whenFirebaseReady(cb, tries + 1), 50);
          }

          function bootRegisterFb() {
            if (!window.TaxpiyaFirebase) return;
            const err = document.getElementById('txp-register-fb-error');
            const form = document.getElementById('users-userregister-form');
            const showErr = (m) => {
              if (!err) return;
              const text = window.TaxpiyaFirebase?.formatFirebaseError
                ? window.TaxpiyaFirebase.formatFirebaseError({ message: m })
                : (m || 'Error');
              err.textContent = text;
              err.style.display = 'block';
            };
            const hideErr = () => { if (err) err.style.display = 'none'; };
            const profile = () => {
              const urlRef = new URLSearchParams(window.location.search).get('ref');
              const inputRef = document.getElementById('txp-referral-code')?.value?.trim();
              const ref = (inputRef || urlRef || '').trim().toUpperCase() || null;
              return {
                app: 'pasajero',
                name: document.getElementById('ctrl-name')?.value?.trim(),
                telefono: document.getElementById('ctrl-telefono')?.value?.trim(),
                referral_code: ref,
                is_register: true,
              };
            };

            if (window.__txpFbRedirectError) showErr(window.__txpFbRedirectError);
            window.addEventListener('txp-firebase-auth-error', (e) => showErr(e?.detail));
            window.addEventListener('txp-firebase-auth-done', (e) => {
              if (e?.detail?.ok) window.location.href = e.detail.redirect || '/home';
            });

            form?.addEventListener('submit', async (e) => {
              const email = document.getElementById('ctrl-email')?.value?.trim();
              const pass  = document.getElementById('ctrl-password')?.value || '';
              const telRaw = document.getElementById('ctrl-telefono')?.value?.trim() || '';
              if (!email || !pass) return;
              if (form.dataset.txpFbSubmitting === '1') return;

              e.preventDefault();
              hideErr();

              if (telRaw && (telRaw.includes('@') || !/^\d[\d\s\-()]{6,}$/.test(telRaw))) {
                showErr('En Móvil escribe tu número de celular (solo dígitos), no el correo.');
                return;
              }

              if (!window.TaxpiyaFirebase) {
                form.dataset.txpFbSubmitting = '1';
                form.submit();
                return;
              }

              try {
                await window.TaxpiyaFirebase.init();
                const data = await window.TaxpiyaFirebase.registerEmail(email, pass, profile());
                window.location.href = data?.redirect || '/home';
              } catch (ex) {
                let msg = ex.message || 'No se pudo crear la cuenta. Verifica el correo o intenta con Google.';
                if (/ya tiene cuenta|email-already-in-use|EMAIL_EXISTS/i.test(msg)) {
                  msg += ' Si ya te registraste antes, usa Iniciar sesión con el mismo correo y contraseña.';
                }
                showErr(msg);
              }
            });

            document.getElementById('txp-register-firebase-google')?.addEventListener('click', async () => {
              hideErr();
              try {
                await window.TaxpiyaFirebase.init();
                const data = await window.TaxpiyaFirebase.loginGoogle(profile());
                if (data?.redirect) return;
                window.location.href = data?.redirect || '/home';
              } catch (ex) { showErr(ex.message); }
            });
          }

          whenFirebaseReady(bootRegisterFb);
        })();
        </script>
        @endif
    </div>
</div>
@endsection

@section('pagejs')
<script>
document.getElementById('fotoperfil-file')?.addEventListener('change', function (e) {
  const file = e.target.files?.[0];
  const preview = document.getElementById('txp-register-photo-preview');
  if (!file || !preview) return;
  preview.src = URL.createObjectURL(file);
  preview.style.display = 'block';
});
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
