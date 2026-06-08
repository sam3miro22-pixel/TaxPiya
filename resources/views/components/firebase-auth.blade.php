@if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
<script>
  window.TAXPIYA_FIREBASE_CONFIG = {
    apiKey: @json(config('firebase.web.api_key')),
    authDomain: @json(config('firebase.web.auth_domain')),
    projectId: @json(config('firebase.project_id')),
    storageBucket: @json(config('firebase.web.storage_bucket')),
    messagingSenderId: @json(config('firebase.web.messaging_sender_id')),
    appId: @json(config('firebase.web.app_id')),
    measurementId: @json(config('firebase.web.measurement_id')),
  };
  window.TAXPIYA_FIREBASE_SYNC_URL = @json(route('auth.firebase.sync'));
</script>
<div id="txp-fb-redirect-busy" class="txp-fb-redirect-busy" style="display:none;" aria-live="polite">
  <div class="txp-fb-redirect-busy__card">
    <i class="fa-solid fa-spinner fa-spin"></i>
    <span>Completando inicio con Google…</span>
  </div>
</div>
<style>
  .txp-fb-redirect-busy{
    position:fixed; inset:0; z-index:99999;
    display:flex; align-items:center; justify-content:center;
    background:rgba(7,11,24,.72);
  }
  .txp-fb-redirect-busy__card{
    display:flex; flex-direction:column; align-items:center; gap:12px;
    padding:20px 24px; border-radius:14px;
    background:#0f172a; color:#fff; font-weight:600;
    box-shadow:0 12px 40px rgba(0,0,0,.35);
  }
  .txp-fb-redirect-busy__card i{ font-size:1.5rem; color:#ffd166; }
</style>
<script src="{{ secure_asset('js/firebase-auth.bundle.js') }}?v=8" defer></script>
@endif
