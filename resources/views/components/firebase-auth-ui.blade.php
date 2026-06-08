@php
    $fbApp = $app ?? null;
    $fbEnabled = config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key');
    $fbSupported = in_array($fbApp, ['pasajero', 'conductor'], true);
@endphp
@if($fbEnabled && $fbSupported)
<div class="txp-firebase-auth" data-app="{{ $fbApp }}">
    <div class="txp-auth-divider">o continúa con</div>
    <div class="d-grid gap-2">
        <button type="button" class="txp-auth-btn txp-auth-btn--google" id="txp-firebase-google">
            <i class="fa-brands fa-google"></i> Continuar con Google
        </button>
        <button type="button" class="txp-auth-btn txp-auth-btn--ghost" id="txp-firebase-email-toggle">
            <i class="fa-solid fa-envelope"></i> Correo electrónico (Firebase)
        </button>
    </div>
    <div id="txp-firebase-email-panel" class="mt-3" style="display:none;">
        <div class="txp-auth-field mb-2">
            <input type="email" id="txp-fb-email" class="txp-auth-input" style="padding-left:16px" placeholder="Correo electrónico">
        </div>
        <div class="txp-auth-field mb-2">
            <input type="password" id="txp-fb-password" class="txp-auth-input" style="padding-left:16px" placeholder="Contraseña">
        </div>
        <button type="button" class="txp-auth-btn txp-auth-btn--primary w-100" id="txp-firebase-email-login">
            Iniciar sesión
        </button>
    </div>
    <div id="txp-firebase-error" class="txp-auth-alert txp-auth-alert--error mt-2" style="display:none;"></div>
</div>
<script>
(function () {
  function whenFirebaseReady(cb, tries) {
    tries = tries || 0;
    if (window.TaxpiyaFirebase) return cb();
    if (tries > 80) return;
    setTimeout(() => whenFirebaseReady(cb, tries + 1), 50);
  }

  function bootUi() {
    const wrap = document.querySelector('.txp-firebase-auth');
    if (!wrap) return;

    const app = wrap.dataset.app || 'pasajero';
    const meta = { app };
    const refCode = document.getElementById('txp-referral-code')?.value?.trim();
    if (refCode) meta.referral_code = refCode;
    const errEl = document.getElementById('txp-firebase-error');
    const loginForm = document.querySelector('form[name="loginForm"]');

    function showErr(msg) {
      if (!errEl) return;
      const text = window.TaxpiyaFirebase?.formatFirebaseError
        ? window.TaxpiyaFirebase.formatFirebaseError({ message: msg })
        : (msg || 'Error de autenticación');
      errEl.textContent = text;
      errEl.style.display = 'block';
    }

    function hideErr() {
      if (errEl) errEl.style.display = 'none';
    }

    function goHome(data) {
      window.location.href = data?.redirect || '/home';
    }

    if (window.__txpFbRedirectError) {
      showErr(window.__txpFbRedirectError);
    }

    window.addEventListener('txp-firebase-auth-error', (e) => {
      showErr(e?.detail || 'Error al volver de Google');
    });

    window.addEventListener('txp-firebase-auth-done', (e) => {
      if (e?.detail?.ok) goHome(e.detail);
    });

    document.getElementById('txp-firebase-google')?.addEventListener('click', async () => {
      hideErr();
      try {
        await window.TaxpiyaFirebase.init();
        const data = await window.TaxpiyaFirebase.loginGoogle(meta);
        if (data?.redirect) return;
        goHome(data);
      } catch (e) { showErr(e.message); }
    });

    document.getElementById('txp-firebase-email-toggle')?.addEventListener('click', () => {
      const p = document.getElementById('txp-firebase-email-panel');
      if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none';
    });

    function submitLaravelLogin(form) {
      if (!form) return;
      form.removeAttribute('data-txp-fb-submitting');
      if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
      } else {
        form.submit();
      }
    }

    async function firebaseEmailLogin(email, pass, form) {
      hideErr();
      if (!email || !pass) { showErr('Ingresa correo y contraseña'); return false; }
      if (!window.TaxpiyaFirebase) return false;
      try {
        await window.TaxpiyaFirebase.init();
        const data = await window.TaxpiyaFirebase.loginEmail(email, pass, meta);
        goHome(data);
        return true;
      } catch (e) {
        return false;
      }
    }

    document.getElementById('txp-firebase-email-login')?.addEventListener('click', async () => {
      const email = document.getElementById('txp-fb-email')?.value?.trim();
      const pass  = document.getElementById('txp-fb-password')?.value || '';
      const ok = await firebaseEmailLogin(email, pass, loginForm);
      if (!ok) showErr('No se pudo con Firebase. Usa celular o contacta soporte.');
    });

    loginForm?.addEventListener('submit', async (e) => {
      const username = document.getElementById('txp-username')?.value?.trim() || '';
      const password = document.getElementById('txp-password')?.value || '';
      if (!username.includes('@')) return;
      if (loginForm.dataset.txpFbSubmitting === '1') return;

      e.preventDefault();
      hideErr();

      if (!window.TaxpiyaFirebase) {
        submitLaravelLogin(loginForm);
        return;
      }

      const ok = await firebaseEmailLogin(username, password, loginForm);
      if (!ok) {
        loginForm.dataset.txpFbSubmitting = '1';
        submitLaravelLogin(loginForm);
      }
    });
  }

  whenFirebaseReady(bootUi);
})();
</script>
@endif
