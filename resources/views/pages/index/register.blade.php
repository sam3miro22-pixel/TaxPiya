@inject('comp_model', 'App\Models\ComponentsData')
@php
    $pageTitle = "Crear cuenta (Pasajero)";
@endphp

@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="auth-bg min-vh-100 d-flex align-items-center justify-content-center px-3">
    <div class="glass-card shadow-xl p-4 p-md-5" style="max-width: 520px; width:100%;">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" width="86" height="86"
                 class="img-fluid rounded-3 logo-glow" alt="Taxpiya" />
            <div class="h4 fw-bold mt-3">
                Bienvenido <span class="text-brand">Pasajero</span>
            </div>
            <div class="small text-light-60">
                Crea tu cuenta para pedir taxi fácil y seguro.
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
        @endif

        <form id="users-userregister-form" role="form" novalidate
              enctype="multipart/form-data"
              class="form page-form needs-validation"
              action="{{ route('auth.register_store') }}" method="post">
            @csrf

            
            <div class="mb-3">
                <label class="form-label">Móvil <span class="text-danger">*</span></label>
                <div class="icon-inside">
                    <i class="fa-solid fa-phone"></i>
                    <input id="ctrl-telefono" name="telefono" type="text"
                           class="form-control input-elevated"
                           placeholder="Escribe tu número de celular"
                           value="{{ old('telefono') }}" required>
                </div>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <div class="icon-inside">
                    <i class="fa-solid fa-lock"></i>
                    <input id="ctrl-password" name="password" type="password"
                           class="form-control input-elevated pe-5"
                           placeholder="Mín. 6 caracteres" required>
                    <button type="button" class="btn btn-sm btn-outline-dark-soft toggle-pass" tabindex="-1" aria-label="Ver contraseña">
                        <i class="fa-solid fa-eye muted"></i>
                    </button>
                </div>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <div class="icon-inside">
                    <i class="fa-solid fa-user"></i>
                    <input id="ctrl-name" name="name" type="text"
                           class="form-control input-elevated"
                           placeholder="Tu nombre y apellido"
                           value="{{ old('name') }}" required
                           data-url="componentsdata/users_name_value_exist/"
                           data-loading-msg="Comprobando disponibilidad ..."
                           data-available-msg="Disponible"
                           data-unavailable-msg="No disponible">
                </div>
                <div class="check-status small mt-1"></div>
            </div>

           
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <div class="icon-inside">
                    <i class="fa-solid fa-envelope"></i>
                    <input id="ctrl-email" name="email" type="email"
                           class="form-control input-elevated"
                           placeholder="tucorreo@dominio.com"
                           value="{{ old('email') }}" required
                           data-url="componentsdata/users_email_value_exist/"
                           data-loading-msg="Comprobando disponibilidad ..."
                           data-available-msg="Disponible"
                           data-unavailable-msg="No disponible">
                </div>
                <div class="check-status small mt-1"></div>
            </div>

          
            <div class="mb-4">
                <label class="form-label">Foto de perfil (opcional)</label>
                <div id="ctrl-fotoperfil-holder">
                    <div class="dropzone dz-light" input="#ctrl-fotoperfil" fieldname="fotoperfil"
                         uploadurl="{{ url('fileuploader/upload/fotoperfil') }}"
                         data-multiple="false" dropmsg="Elige o arrastra tu foto aquí"
                         btntext="Explorar" extensions=".jpg,.png,.gif,.jpeg"
                         filesize="50" maximum="1">
                        <input name="fotoperfil" id="ctrl-fotoperfil"
                               class="dropzone-input form-control" value="{{ old('fotoperfil') }}" type="text"/>
                        <div class="dz-file-limit text-danger small mt-2"></div>
                    </div>
                </div>
            </div>

           
            <div class="d-grid mb-3">
                <button class="btn btn-brand btn-lg" type="submit">
                    <i class="fa-solid fa-user-plus me-2"></i> Crear cuenta
                </button>
            </div>

            <div class="text-center small text-light-60">
                ¿Ya tienes cuenta?
                <a href="{{ route('pasajero.login') }}" class="link-light text-decoration-underline">Inicia sesión</a>
            </div>
        </form>

        @if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
        <div class="mt-4 pt-3 border-top border-light border-opacity-10">
            <div class="text-center small text-light-60 mb-2">Registro rápido con Firebase</div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-light" id="txp-register-firebase-email">
                    <i class="fa-solid fa-envelope me-2"></i> Crear con correo (Firebase)
                </button>
                <button type="button" class="btn btn-outline-light" id="txp-register-firebase-google">
                    <i class="fa-brands fa-google me-2"></i> Crear con Google
                </button>
            </div>
            <div id="txp-register-fb-error" class="alert alert-danger mt-2 py-2 small" style="display:none;"></div>
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
<style>

.navbar-brand,.topbar,.navbar,.footer{display:none}


.auth-bg{
    background:
      radial-gradient(60% 80% at 80% 0%, rgba(255,162,0,.15), transparent 60%),
      radial-gradient(55% 70% at 0% 100%, rgba(255,209,102,.12), transparent 60%),
      linear-gradient(180deg, #0b132b 0%, #1c2541 55%, #0b132b 100%);
}


:root{
    --txp-brand:#ffd166; --txp-white:#fff;
}

.text-brand{ color: var(--txp-brand); }
.text-light-60{ color: rgba(255,255,255,.72); }

.glass-card{
    background: linear-gradient(165deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 22px;
}
.logo-glow{ filter: drop-shadow(0 0 12px rgba(255, 209, 102, .45)); }


.input-elevated{
    background: rgba(255,255,255,.96);
    border: 0;
    border-radius: 14px;
    padding: 12px 14px 12px 50px; 
    color:#0b132b;
    box-shadow: inset 0 2px 8px rgba(0,0,0,.06);
}


.icon-inside{ position: relative; }
.icon-inside > i{
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(180deg, rgba(255,209,102,.35), rgba(255,159,28,.18));
    color: #1a1a1a;               
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    z-index: 3;
}


.toggle-pass{
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    border-radius: 10px; padding: 6px 9px;
}
.btn-outline-light-soft{
    border: 1px solid rgba(255,255,255,.35);
    color: rgba(255,255,255,.9);
    background: rgba(255,255,255,.06);
}


.btn-brand{
    background: linear-gradient(180deg, #ffd166, #ff9f1c);
    color:#1a1a1a; font-weight: 700; border: none; border-radius: 999px;
    box-shadow: 0 8px 24px rgba(255,159,28,.35);
}
.btn-brand:hover{ filter: brightness(1.03); }


.dz-light{
    background: rgba(255,255,255,.06);
    border: 1px dashed rgba(255,255,255,.25) !important;
    border-radius: 14px; padding: 16px;
    color: rgba(255,255,255,.75);
}
</style>
@endsection

@section('pagejs')
<script>
   
    document.addEventListener('click', function(e){
        if(e.target.closest('.toggle-pass')){
            const btn = e.target.closest('.toggle-pass');
            const input = document.getElementById('ctrl-password');
            const icon = btn.querySelector('i');
            if(input.type === 'password'){
                input.type = 'text';
                icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash');
            }else{
                input.type = 'password';
                icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye');
            }
        }
    });
</script>
@endsection
