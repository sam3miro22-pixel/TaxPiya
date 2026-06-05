@inject('comp_model', 'App\Models\ComponentsData')
<?php $pageTitle = "Taxpiya"; ?>
@extends($layout)

@section('title', $pageTitle)

@section('content')
<div class="txp-hero">
    <div class="txp-hero__overlay"></div>

    <div class="txp-center">
        <div class="card txp-card glass shadow-xl p-4 p-md-5 text-center">
            <img src="{{ asset('images/logo.png') }}" class="txp-logo mb-3" alt="Taxpiya">

            <h1 class="txp-title fw-800 mb-2">
                ¡Bienvenido a <span class="text-brand">Taxpiya</span>!
            </h1>
            <p class="txp-subtitle mb-4">
                Tu viaje, más fácil y seguro. Elige cómo quieres entrar.
            </p>

            <div class="d-grid gap-3">
                <a href="{{ url('/pasajero/login') }}" class="btn btn-brand btn-lg btn-pill">
                    <i class="fa fa-user me-2"></i> Entrar como Pasajero
                </a>
                <a href="{{ url('/conductor/login') }}" class="btn btn-glass btn-lg btn-pill">
                    <i class="fa fa-taxi me-2"></i> Entrar como Conductor
                </a>
            </div>

            <div class="small text-white-50 mt-4">
                © {{ date('Y') }} Taxpiya. Todos los derechos reservados.
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagecss')

<style>
   
	.navbar-brand,.topbar,.navbar,.footer{display:none}
    .txp-hero{
        min-height: 100vh;
        width: 100%;
        position: relative;
        overflow: hidden;
        /* Halos cálidos + base navy */
        background:
            radial-gradient(900px 600px at 15% -10%, rgba(255,209,102,.42) 0%, rgba(255,209,102,.20) 40%, transparent 70%),
            radial-gradient(900px 650px at 110% 10%, rgba(244,140,6,.38) 0%, rgba(244,140,6,.22) 45%, transparent 72%),
            radial-gradient(1200px 800px at 85% 115%, rgba(255,168,0,.28) 0%, rgba(255,168,0,.14) 40%, transparent 70%),
            linear-gradient(135deg, #0b132b 0%, #161e33 55%, #0f1627 100%);
        animation: huefloat 14s ease-in-out infinite alternate;
    }
    @keyframes huefloat{
        0%   { filter: hue-rotate(0deg) brightness(1); }
        100% { filter: hue-rotate(-6deg) brightness(1.06); }
    }

   
    .txp-center{
        position: relative;
        display: grid;
        place-items: center;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    
    .txp-card.glass{
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 18px;
        background: rgba(20, 26, 44, 0.55);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: #fff;
        max-width: 540px;
        width: 100%;
    }


    .txp-logo{
        width: 110px;
        height: 110px;
        object-fit: contain;
        display: block;            /* centra el inline-element */
        margin: 0 auto 0.5rem;     /* centrado horizontal */
        border-radius: 50%;
        border: 2px solid rgba(255,209,102,.65);
        box-shadow:
            0 0 0 6px rgba(255,209,102,.12),
            0 0 45px rgba(255,183,3,.55),
            0 12px 24px rgba(0,0,0,.35);
    }

   
    .txp-title{
        font-size: clamp(1.6rem, 2.8vw, 2.2rem);
        line-height: 1.2;
        color: #fff;
        margin: 0;
    }
    .text-brand{ color: #FFD166; }
    .fw-800{ font-weight: 800; }

    
    .txp-subtitle{
        font-family: inherit;          
        font-weight: 500;
        letter-spacing: .1px;
        color: rgba(255,255,255,.85);  
        font-size: clamp(0.95rem, 1.2vw, 1.05rem);
        margin: 0;
    }

   
    .btn-pill{ border-radius: 999px; }

    .btn-brand{
        background-image: linear-gradient(90deg, #FFD166 0%, #FFB703 55%, #F48C06 100%);
        border: 0;
        color: #1b1b1b;
        font-weight: 700;
        letter-spacing: .2px;
        box-shadow: 0 10px 20px rgba(244,140,6,.28);
    }
    .btn-brand:hover{
        filter: brightness(1.06);
        box-shadow: 0 14px 28px rgba(244,140,6,.36);
        color: #111;
    }

    .btn-glass{
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.45);
        color: #fff;
        font-weight: 600;
    }
    .btn-glass:hover{
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.7);
        color: #fff;
    }

    .shadow-xl{ box-shadow: 0 28px 70px rgba(0,0,0,.5)!important; }
</style>
@endsection


@section('pagejs')
<script>
    $(function(){
        
    });
</script>
@endsection
