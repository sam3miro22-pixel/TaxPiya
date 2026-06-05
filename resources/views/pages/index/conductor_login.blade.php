<?php $pageTitle = "Iniciar sesión - Conductor"; ?>
@extends($layout)

@section('title', $pageTitle)

@section('content')
<div class="auth-wrap d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="card glass-card p-4 p-md-5">
                    @include('pages.index.loginview', ['app' => 'conductor'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagecss')
<style>
.navbar-brand,.topbar,.navbar,.footer{display:none}
    .auth-wrap{
        min-height: calc(100vh - 56px);
        background: linear-gradient(135deg, #0b132b 0%, #1c2541 40%, #3a506b 100%);
        padding: 2rem 0;
    }
    .glass-card{
        border: 0;
        border-radius: 1.25rem;
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        color: #fff;
    }
	:root{
        --txp-brand: #FFB703; /* amarillo */
        --txp-brand-2: #FB8500; /* naranja */
    }

    .txp-text-70{ color: rgba(255,255,255,0.7); }
    .txp-text-80{ color: rgba(255,255,255,0.85); }

    .txp-logo-glow{ filter: drop-shadow(0 8px 18px rgba(251,133,0,.35)); }

    .txp-ipt{
        background: rgba(255,255,255,0.95);
        border: 0;
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        padding: 12px 14px;
    }
    .txp-ipt:focus{ box-shadow: 0 0 0 .25rem rgba(251,133,0,.25); }
    .txp-ipt-pre{
        background: rgba(0,0,0,.35);
        color: #fff;
        border: 0;
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        width: 54px;
        justify-content: center;
    }

    /* <<< FIX: botón brand forzado para que no lo pisen estilos de Bootstrap >>> */
    .btn-brand,
    .btn-brand:focus{
        background: linear-gradient(135deg, var(--txp-brand), var(--txp-brand-2)) !important;
        color: #1a1a1a !important;
        border: 0 !important;
        border-radius: 14px !important;
        font-weight: 700 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        box-shadow: 0 10px 24px rgba(251,133,0,.25);
    }
    .btn-brand:hover{
        filter: brightness(.95);
        color: #111 !important;
    }

    .txp-btn-create{
        border-radius: 14px;
        border: 2px solid rgba(255,255,255,.35) !important;
        color: #fff !important;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }
    .txp-btn-create:hover{
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.55) !important;
    }
</style>
@endsection
