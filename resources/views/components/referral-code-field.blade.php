@php
    $referralValue = old('codigo_referido', request('ref', ''));
@endphp
<div class="txp-auth-field txp-referral-field">
    <label class="txp-auth-label" for="txp-referral-code">
        Código de referido <span style="font-weight:400;opacity:.7">(opcional)</span>
    </label>
    <div class="txp-auth-input-wrap">
        <i class="fa-solid fa-gift txp-auth-input-icon"></i>
        <input type="text"
               name="codigo_referido"
               id="txp-referral-code"
               class="txp-auth-input txp-referral-input"
               placeholder="Ej: TXP-P000042 o TXP-E000003"
               value="{{ $referralValue }}"
               autocomplete="off"
               maxlength="20">
    </div>
    <div class="form-text small mt-1" style="opacity:.75">Si alguien te invitó, ingresa su código aquí.</div>
</div>
<script>
(function () {
  const el = document.getElementById('txp-referral-code');
  if (!el || el.value) return;
  try {
    const ref = new URLSearchParams(window.location.search).get('ref');
    if (ref) el.value = ref.trim().toUpperCase();
  } catch (_) {}
})();
</script>
