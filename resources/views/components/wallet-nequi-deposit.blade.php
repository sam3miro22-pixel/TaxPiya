@php
    $nequi = config('taxpiya.wallet.nequi');
    $btnClass = $btnClass ?? 'txp-mobile-btn w-100 border-0';
    $btnStyle = $btnStyle ?? '';
@endphp
<div class="txp-mobile-card txp-nequi-card">
    <h2 class="txp-section-title mt-0"><i class="fa-solid fa-mobile-screen me-1"></i> Recargar con NEQUI</h2>

    <div class="txp-nequi-info mb-3">
        <div class="txp-info-row"><span>Número NEQUI</span><strong>{{ $nequi['numero'] ?? '3124959199' }}</strong></div>
        <div class="txp-info-row"><span>Titular</span><strong>{{ $nequi['titular'] ?? 'Medardo Torres' }}</strong></div>
        <div class="txp-info-row"><span>Cédula</span><strong>{{ $nequi['cedula'] ?? '1083875427' }}</strong></div>
    </div>

    <p class="small text-muted mb-3">
        1. Envía el monto desde tu app NEQUI.<br>
        2. Toma captura del comprobante.<br>
        3. Sube la imagen aquí. Un administrador revisará y acreditará tu saldo.
    </p>

    <form method="post" action="{{ $action }}" enctype="multipart/form-data" id="txp-nequi-deposit-form">
        @csrf
        <input type="hidden" name="metodo_pago" value="nequi">

        <div class="txp-profile-edit-field mb-3">
            <label>Monto enviado (COP)</label>
            <input type="number" name="monto" class="form-control txp-input-dark @error('monto') is-invalid @enderror" min="1000" max="50000000" step="1000" required placeholder="Ej: 50000" value="{{ old('monto') }}">
            @error('monto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted">Mínimo $1.000 · máximo $50.000.000</small>
        </div>

        <div class="txp-profile-edit-field mb-3">
            <label>Referencia / número de transacción NEQUI</label>
            <input type="text" name="referencia_pago" class="form-control txp-input-dark @error('referencia_pago') is-invalid @enderror" maxlength="64" required placeholder="Ej: M12345678" value="{{ old('referencia_pago') }}">
            @error('referencia_pago')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="txp-profile-edit-field mb-3">
            <label>Comprobante de pago (foto) <span class="text-danger">*</span></label>
            <input type="file" name="comprobante" class="form-control txp-input-dark @error('comprobante') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp" required>
            @error('comprobante')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted">JPG, PNG o WEBP · máx. 5 MB · obligatorio</small>
        </div>

        <button type="submit" class="{{ $btnClass }}" @if($btnStyle) style="{{ $btnStyle }}" @endif>
            <i class="fa-solid fa-paper-plane"></i> Enviar solicitud de recarga
        </button>
    </form>
</div>
