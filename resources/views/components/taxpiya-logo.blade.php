@props(['size' => 200, 'conductor' => false])
<div class="txp-auth-logo-wrap" aria-hidden="true">
    <img src="{{ asset('images/logo.png') }}?v=taxpiya2026"
         alt="TaxPiya"
         class="txp-auth-logo-img"
         width="{{ $size }}"
         height="{{ $size }}"
         style="max-width:{{ $size }}px">
</div>
