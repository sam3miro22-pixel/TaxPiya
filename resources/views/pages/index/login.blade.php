
@extends($layout)

@section('title', 'Iniciar sesión')

@section('content')
<div class="auth-bg min-vh-100 d-flex align-items-center justify-content-center px-3">
    <div class="glass-card shadow-xl p-4 p-md-5" style="max-width: 520px; width:100%;">
        @include('pages.index.loginview') {{-- usa el mismo parcial de login --}}
    </div>
</div>
@endsection

@section('pagecss')
<style>

.navbar, .topbar, .footer { display: none; }
body { padding-top: 0 !important; }


.auth-bg{
    background:
      radial-gradient(60% 80% at 80% 0%, rgba(255,162,0,.18), transparent 60%),
      radial-gradient(55% 70% at 0% 100%, rgba(255,209,102,.14), transparent 60%),
      linear-gradient(180deg, #0b132b 0%, #1c2541 55%, #0b132b 100%);
}


.glass-card{
    background: linear-gradient(165deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 22px;
}
</style>
@endsection
