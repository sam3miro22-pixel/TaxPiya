@php
    $code = $referral['codigo'] ?? '—';
    $shareUrl = $referralShareUrl ?? url('/pasajero/registro?ref=' . urlencode($code));
@endphp
<div class="txp-mobile-card" style="border:1px solid rgba(56,189,248,.35);">
    <h3 class="h6 mb-2"><i class="fa-solid fa-gift me-2"></i>Invita y gana</h3>
    <p class="small text-muted mb-2">Comparte tu código con pasajeros, conductores o empresas.</p>
    <div class="txp-info-row"><span>Tu código</span><strong><code id="txp-my-referral-code">{{ $code }}</code></strong></div>
    <div class="txp-info-row"><span>Referidos</span><strong>{{ $referral['total'] ?? 0 }} ({{ $referral['activos'] ?? 0 }} activos)</strong></div>
    <button type="button" class="txp-mobile-btn mt-2" id="txp-copy-referral" data-code="{{ $code }}" data-url="{{ $shareUrl }}">
        <i class="fa-solid fa-copy"></i> Copiar enlace de invitación
    </button>
</div>
<script>
document.getElementById('txp-copy-referral')?.addEventListener('click', function () {
  const text = this.dataset.url || this.dataset.code || '';
  if (navigator.clipboard?.writeText) {
    navigator.clipboard.writeText(text).then(() => alert('Enlace copiado'));
  } else {
    prompt('Copia este enlace:', text);
  }
});
</script>
