@php
    $fbApp = $app ?? null;
@endphp
@if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
<div class="txp-firebase-auth mt-3" data-app="{{ $fbApp }}">
    <div class="text-center small txp-text-70 mb-2">o continúa con</div>
    <div class="d-grid gap-2">
        <button type="button" class="btn btn-light btn-lg w-100 txp-btn-google" id="txp-firebase-google">
            <i class="fa-brands fa-google me-2"></i> Google
        </button>
        @if($fbApp === 'pasajero')
        <button type="button" class="btn btn-outline-light btn-lg w-100" id="txp-firebase-email-toggle">
            <i class="fa-solid fa-envelope me-2"></i> Correo (Firebase)
        </button>
        @endif
    </div>
    <div id="txp-firebase-email-panel" class="mt-3" style="display:none;">
        <div class="mb-2">
            <input type="email" id="txp-fb-email" class="form-control txp-ipt" placeholder="Correo electrónico">
        </div>
        <div class="mb-2">
            <input type="password" id="txp-fb-password" class="form-control txp-ipt" placeholder="Contraseña">
        </div>
        <button type="button" class="btn btn-brand w-100" id="txp-firebase-email-login">
            Iniciar con Firebase
        </button>
    </div>
    <div id="txp-firebase-error" class="alert alert-danger mt-2 py-2 small" style="display:none;"></div>
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
