@if(config('taxpiya.firebase.use_firebase_auth') && config('firebase.web.api_key'))
<script>
(function () {
  const app = @json($firebaseApp ?? (auth()->check() && auth()->user()->hasRole('Conductor') ? 'conductor' : 'pasajero'));
  const laravelAuthed = @json(auth()->check());

  function whenReady(cb, n) {
    n = n || 0;
    if (window.TaxpiyaFirebase) return cb();
    if (n > 100) return;
    setTimeout(() => whenReady(cb, n + 1), 50);
  }

  whenReady(async () => {
    await window.TaxpiyaFirebase.init();

    if (!laravelAuthed && window.TaxpiyaFirebase.resyncSession) {
      try {
        const data = await window.TaxpiyaFirebase.resyncSession({ app });
        if (data?.ok && data.redirect) {
          window.location.replace(data.redirect);
        }
      } catch (_) {}
    }

    window.TaxpiyaFirebase.onAuthChange(async (user) => {
      if (laravelAuthed || !user || !window.TaxpiyaFirebase.resyncSession) return;
      try {
        const data = await window.TaxpiyaFirebase.resyncSession({ app });
        if (data?.ok && data.redirect) {
          window.location.replace(data.redirect);
        }
      } catch (_) {}
    });
  });
})();
</script>
@endif
