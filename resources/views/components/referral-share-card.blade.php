@php
    $code = $referral['codigo'] ?? '—';
    $shareUrl = $referralShareUrl ?? url('/pasajero/registro?ref=' . urlencode($code));
@endphp
<div class="txp-mobile-card" style="border:1px solid rgba(56,189,248,.35);">
    <h3 class="h6 mb-2"><i class="fa-solid fa-gift me-2"></i>Invita y gana</h3>
    <p class="small text-muted mb-2">Gana <strong>${{ number_format((float)($referral['bono_por_referido'] ?? 5000), 0, ',', '.') }}</strong> por cada referido cuando confirme su cuenta. El bono se acredita a tu billetera.</p>
    <div class="txp-info-row"><span>Tu código</span><strong><code id="txp-my-referral-code">{{ $code }}</code></strong></div>
    <div class="txp-info-row"><span>Referidos</span><strong>{{ $referral['total'] ?? 0 }} ({{ $referral['activos'] ?? 0 }} activos)</strong></div>
    @if(($referral['bonos_pagados'] ?? 0) > 0)
    <div class="txp-info-row"><span>Bonos pagados</span><strong>{{ $referral['bonos_pagados'] }} · ${{ number_format((float)($referral['ganancia_total'] ?? 0), 0, ',', '.') }}</strong></div>
    @elseif(($referral['activos'] ?? 0) > 0)
    <div class="txp-info-row"><span>Bono pendiente</span><strong class="text-warning">Abre Mi billetera para acreditar</strong></div>
    @endif
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
