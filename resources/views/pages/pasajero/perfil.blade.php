@php $pageTitle = 'Mi perfil'; @endphp
@extends($layout)
@section('title', $pageTitle)

@section('content')
<div class="txp-mobile-page">
    <header class="txp-mobile-head">
        <a href="{{ route('home') }}" class="txp-mobile-back"><i class="fa-solid fa-arrow-left"></i> Volver al mapa</a>
        <h1>Mi perfil</h1>
    </header>

    @if(!empty($saved))
        <div class="txp-mobile-card txp-alert-success">
            <i class="fa-solid fa-check-circle me-2"></i> Perfil actualizado correctamente.
        </div>
    @endif

    <form method="POST" action="{{ route('pasajero.perfil.update') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        <div class="txp-mobile-card txp-profile-hero">
            @php $photoUrl = app(\App\Services\ProfilePhotoService::class)->publicUrl($user->fotoperfil ?? null); @endphp
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="Foto" class="txp-profile-photo" id="txp-profile-preview">
            @else
                <div class="txp-profile-photo txp-profile-photo--placeholder" id="txp-profile-preview-wrap"><i class="fa-solid fa-user"></i></div>
            @endif
            <div class="txp-profile-edit-field mt-3">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" class="form-control txp-input-dark" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="txp-profile-edit-field mt-2">
                <label for="fotoperfil-file">Cambiar foto</label>
                <input type="file" id="fotoperfil-file" name="fotoperfil_file" class="form-control txp-input-dark" accept="image/jpeg,image/png,image/gif,image/webp">
                <input type="hidden" name="fotoperfil" value="{{ old('fotoperfil', $user->fotoperfil) }}">
            </div>
            <p class="text-muted small mt-2 mb-0">Pasajero TaxPiya</p>
        </div>

        @include('components.referral-share-card', ['referral' => $referral ?? [], 'referralShareUrl' => $referralShareUrl ?? null])

        <div class="txp-mobile-card">
            <div class="txp-info-row"><span>Celular</span><strong>{{ $user->telefono ?? '—' }}</strong></div>
            <div class="txp-info-row"><span>Email</span><strong>{{ $user->email ?? '—' }}</strong></div>
            <div class="txp-info-row"><span>Estado</span><strong>{{ (int)($user->estado ?? 1) === 1 ? 'Activo' : 'Inactivo' }}</strong></div>
        </div>

        <div class="txp-mobile-actions">
            <button type="submit" class="txp-mobile-btn"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
            <a href="{{ route('pasajero.wallet') }}" class="txp-mobile-btn txp-mobile-btn--ghost"><i class="fa-solid fa-wallet"></i> Mi billetera</a>
            <a href="{{ route('pasajero.viajes') }}" class="txp-mobile-btn txp-mobile-btn--ghost"><i class="fa-solid fa-route"></i> Mis viajes</a>
            <a href="{{ route('logout') }}" class="txp-mobile-btn txp-mobile-btn--ghost"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
        </div>
    </form>
</div>
@endsection

@section('pagecss')
<link rel="stylesheet" href="{{ secure_asset('css/taxpiya-mobile-pages.css') }}?v=2">
@endsection

@section('pagejs')
<script>
document.getElementById('fotoperfil-file')?.addEventListener('change', function (e) {
  const file = e.target.files?.[0];
  const img = document.getElementById('txp-profile-preview');
  const wrap = document.getElementById('txp-profile-preview-wrap');
  if (!file) return;
  const url = URL.createObjectURL(file);
  if (img) {
    img.src = url;
    img.style.display = 'block';
  } else if (wrap) {
    wrap.outerHTML = '<img src="' + url + '" alt="Foto" class="txp-profile-photo" id="txp-profile-preview">';
  }
});
</script>
@endsection
