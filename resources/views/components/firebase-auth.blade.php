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
<script src="{{ secure_asset('js/firebase-auth.bundle.js') }}" defer></script>
@endif
