@php
    $fbApp = $app ?? null;
@endphp
@if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
<div class="txp-firebase-auth" data-app="{{ $fbApp }}">
    <div class="txp-auth-divider">o continúa con</div>
    <div class="d-grid gap-2">
        <button type="button" class="txp-auth-btn txp-auth-btn--google" id="txp-firebase-google">
            <i class="fa-brands fa-google"></i> Continuar con Google
        </button>
        @if($fbApp === 'pasajero')
        <button type="button" class="txp-auth-btn txp-auth-btn--ghost" id="txp-firebase-email-toggle">
            <i class="fa-solid fa-envelope"></i> Correo electrónico
        </button>
        @endif
    </div>
    <div id="txp-firebase-email-panel" class="mt-3" style="display:none;">
        <div class="txp-auth-field mb-2">
            <input type="email" id="txp-fb-email" class="txp-auth-input" style="padding-left:16px" placeholder="Correo electrónico">
        </div>
        <div class="txp-auth-field mb-2">
            <input type="password" id="txp-fb-password" class="txp-auth-input" style="padding-left:16px" placeholder="Contraseña">
        </div>
        <button type="button" class="txp-auth-btn txp-auth-btn--primary w-100" id="txp-firebase-email-login">
            Iniciar con Firebase
        </button>
    </div>
    <div id="txp-firebase-error" class="txp-auth-alert txp-auth-alert--error mt-2" style="display:none;"></div>
</div>
@include('components.firebase-auth')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const wrap = document.querySelector('.txp-firebase-auth');
  if (!wrap || !window.TaxpiyaFirebase) return;

  const app = wrap.dataset.app || null;
  const meta = app ? { app } : {};
  const errEl = document.getElementById('txp-firebase-error');

  function showErr(msg) {
    if (!errEl) return;
    errEl.textContent = msg || 'Error de autenticación';
    errEl.style.display = 'block';
  }

  function goHome(data) {
    window.location.href = data?.redirect || '/home';
  }

  window.TaxpiyaFirebase.init();

  window.TaxpiyaFirebase.completeGoogleRedirect(meta).then((data) => {
    if (data?.ok) goHome(data);
  }).catch((e) => { if (e?.message) showErr(e.message); });

  document.getElementById('txp-firebase-google')?.addEventListener('click', async () => {
    try {
      const data = await window.TaxpiyaFirebase.loginGoogle(meta);
      if (data?.redirect) return;
      goHome(data);
    } catch (e) { showErr(e.message); }
  });

  document.getElementById('txp-firebase-email-toggle')?.addEventListener('click', () => {
    const p = document.getElementById('txp-firebase-email-panel');
    if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none';
  });

  document.getElementById('txp-firebase-email-login')?.addEventListener('click', async () => {
    const email = document.getElementById('txp-fb-email')?.value?.trim();
    const pass  = document.getElementById('txp-fb-password')?.value || '';
    if (!email || !pass) { showErr('Ingresa correo y contraseña'); return; }
    try {
      const data = await window.TaxpiyaFirebase.loginEmail(email, pass, meta);
      goHome(data);
    } catch (e) { showErr(e.message); }
  });
});
</script>
@endif
