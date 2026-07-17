@inject('comp_model', 'App\Models\ComponentsData')
@php $pageTitle = "Conductor"; @endphp

@extends($layout)
@section('title', $pageTitle)

@section('content')
<div id="txd-map-root">
  <div id="map" class="map-canvas"></div>

  <div id="txp-ui-layer" class="txp-ui-layer">
  @php
  $drvDisponible = \DB::table('conductores')
      ->where('user_id', auth()->id())
      ->value('disponible') ?? 0;
  @endphp

  <div class="txp-conductor-head" aria-hidden="true">
    <span class="txp-conductor-head__title">TAXPIYA</span>
  </div>

  <div class="txp-conductor-top-right">
    <div class="txp-brand-badge" aria-hidden="true">
      <img src="{{ asset('images/logo.png') }}" alt="" class="txp-brand-badge__img" width="40" height="40">
    </div>
    <button id="driver-online-toggle"
            class="btn btn-brand"
            data-online="{{ $drvDisponible ? '1' : '0' }}">
      <i class="fa-solid fa-power-off me-1"></i> DISPONIBLE
    </button>
  </div>

  <div id="geo-accuracy" class="geo-badge" style="display:none;"></div>

  <button id="txd-recenter" class="txd-recenter-fab" type="button" aria-label="Centrar en mi ubicación">
    <i class="fa-solid fa-crosshairs"></i>
  </button>


  <div id="txd-banner" class="txd-banner" aria-hidden="true">
    <i id="txd-banner-ico" class="fa-solid fa-circle-info me-2"></i>
    <span id="txd-banner-txt">Estado</span>
  </div>

  
  <div class="quick-menu" id="quickMenu">
    <button class="qm-toggle" id="qmToggle" aria-expanded="false" aria-label="Abrir menú">
      <i class="fa-solid fa-bars"></i>
    </button>

    <nav class="qm-items">
      <button id="qmGoOnline" class="qm-item" style="--i:1" aria-label="Disponibilidad">
        <i class="fa-solid fa-power-off"></i>
        <span class="tip">Conectarme</span>
      </button>
      <button id="qmTrips" class="qm-item" style="--i:2" aria-label="Viajes"
              onclick="window.location.href='{{ route('conductor.viajes') }}'">
        <i class="fa-solid fa-route"></i>
        <span class="tip">Viajes</span>
      </button>
      <button id="qmWallet" class="qm-item" style="--i:3" aria-label="Wallet"
              onclick="window.location.href='{{ route('conductor.wallet') }}'">
        <i class="fa-solid fa-wallet"></i>
        <span class="tip">Wallet</span>
      </button>
      <a href="{{ route('conductor.cuenta') }}" id="qmCuenta" class="qm-item" style="--i:4" aria-label="Cuenta">
        <i class="fa-solid fa-user"></i>
        <span class="tip">Cuenta</span>
      </a>
      <a href="{{ route('logout') }}" id="qmLogout" class="qm-item" style="--i:5" aria-label="Salir">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="tip">Salir</span>
      </a>
    </nav>
  </div>

  </div>{{-- /txp-ui-layer --}}


  <div id="drv-sheet-solicitud" class="txp-sheet" aria-hidden="true">
    <div class="txp-sheet-backdrop" data-close></div>
    <div class="txp-sheet-dialog" role="dialog" aria-modal="true" aria-labelledby="drvSolicitudTitulo">
      <div class="txp-sheet-handle"></div>
      <h6 id="drvSolicitudTitulo" class="m-0 mb-2 fw-bold">Nueva solicitud</h6>

      <div class="txp-resumen">
        <div class="txp-par">
          <span class="txp-dot origen"></span>
          <div class="txp-col">
            <div class="txp-lab">Recoger en</div>
            <div id="drv-o-txt" class="txp-val">—</div>
          </div>
        </div>
        <div class="txp-par">
          <span class="txp-dot destino"></span>
          <div class="txp-col">
            <div class="txp-lab">Destino</div>
            <div id="drv-d-txt" class="txp-val">—</div>
          </div>
        </div>
        <div class="txp-meta">
          <span>Tarifa: <b id="drv-monto">—</b></span>
          <span id="drv-countdown" class="ms-2" style="opacity:.8;">15s</span>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button id="drv-btn-aceptar" class="btn btn-warning">
          <i class="fa-solid fa-check me-1"></i> Aceptar
        </button>
        <button id="drv-btn-rechazar" class="btn btn-light">
          <i class="fa-solid fa-xmark me-1"></i> Rechazar
        </button>
      </div>
    </div>
  </div>

 
  <div id="txd-trip-cta" class="bottom-cta" style="display:none;">

    <div id="txd-nav-row" class="d-flex gap-2 mb-2" style="display:none;">
      <button id="txd-open-google" class="btn btn-sm btn-light flex-fill">
        <i class="fa-brands fa-google me-1"></i> Google Maps
      </button>
      <button id="txd-open-waze" class="btn btn-sm btn-light flex-fill">
        <i class="fa-brands fa-waze me-1"></i> Waze
      </button>
    </div>

    
    <div id="txd-comm-row" class="d-flex gap-2 mb-2" style="display:none;">
      <a id="txd-call" class="btn btn-sm btn-outline-light flex-fill" href="#">
        <i class="fa-solid fa-phone me-1"></i> Llamar
      </a>
      <button id="txd-chat" class="btn btn-sm btn-outline-light flex-fill">
        <i class="fa-solid fa-comments me-1"></i> Chat
      </button>
    </div>

    <div class="w-100" style="max-width:740px;">
      <div id="txd-trip-info" class="mb-2 text-center text-white fw-bold"></div>
    </div>
  </div>


  <div id="txd-chat-sheet" class="txp-sheet" aria-hidden="true">
    <div class="txp-sheet-backdrop" data-close></div>
    <div class="txp-sheet-dialog" role="dialog" aria-modal="true" aria-labelledby="txdChatTitle">
      <div class="txp-sheet-handle"></div>
      <h6 id="txdChatTitle" class="m-0 mb-2 fw-bold">Chat con el pasajero</h6>

      <div id="txd-chat-list" style="height:260px; overflow:auto; padding:6px 4px; background:#0f172a; border-radius:10px;"></div>

      <div class="d-flex mt-2">
        <input id="txd-chat-text" type="text" class="form-control me-2" placeholder="Escribe un mensaje…">
        <button id="txd-chat-send" class="btn btn-warning">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </div>
<audio id="snd-offer" src="{{ asset('sounds/solicitudtaxpiya.mp3') }}?v=2" preload="auto"></audio>

<div id="bg-consent" class="txp-modal" aria-hidden="true" role="dialog" aria-labelledby="bgc-title" aria-describedby="bgc-desc">
  <div class="txp-modal-backdrop"></div>
  <div class="txp-modal-dialog">
    <div class="txp-modal-header">
      <div class="txp-badge">Importante</div>
      <h5 id="bgc-title" class="m-0">Uso de ubicación en segundo plano</h5>
    </div>

    <div class="txp-modal-body">
      <p id="bgc-desc" class="mb-2">
        Taxpiya Conductor necesita acceder a tu ubicación en segundo plano para poder:
      </p>
      <ul class="txp-list">
        <li>Asignarte viajes cercanos incluso con la app en segundo plano o pantalla apagada.</li>
        <li>Mostrar tu posición actualizada al pasajero mientras estés “Disponible”.</li>
      </ul>
      <p class="txp-note">
        La ubicación se utiliza <b>solo cuando estás en modo “Disponible”</b> y puedes cambiar este permiso en
        <i>Ajustes del sistema &gt; Permisos</i> en cualquier momento.
      </p>
    </div>

    <div class="txp-modal-actions">
      <button id="bgc-accept" class="btn btn-warning w-100">
        Acepto
      </button>
    </div>
  </div>
</div>

  <button type="button" id="txp-sos-btn" class="txp-sos-fab" title="Emergencia SOS">SOS</button>
</div>

@endsection

@section('pagecss')
<style>

.navbar, .topbar, .footer, #sidebar { display: none !important; }
body#main { padding-top: 0 !important; overflow: hidden; }
body#main #page-wrapper,
body#main #main-content,
body#main #page-content {
  padding: 0 !important; margin: 0 !important;
  width: 100% !important; position: static !important; min-height: 100dvh;
}

:root{
  --txp-bg-1:#0b132b;
  --txp-brand:#ffd166;
  --txp-brand-2:#ff9f1c;
  --qm-gap:64px;
  --txp-ui-z: 800;
  --txp-sheet-z: 3000;
}


#txd-map-root{ position:fixed !important; inset:0 !important; width:100vw !important; height:100dvh !important; background:#000; overflow:hidden; z-index:0; }
#txd-map-root #map, #txd-map-root .map-canvas, #txd-map-root .leaflet-container {
  position:absolute !important; inset:0 !important; width:100% !important; height:100% !important; z-index:1 !important;
}
.txp-ui-layer { position:absolute; inset:0; z-index:var(--txp-ui-z); pointer-events:none; }
.txp-ui-layer > * { pointer-events:auto; }

.txp-conductor-head{
  position: fixed;
  top: calc(env(safe-area-inset-top, 0px) + 16px);
  left: 0;
  right: 0;
  z-index: 5;
  text-align: center;
  pointer-events: none;
}
.txp-conductor-head__title{
  display: inline-block;
  font-size: 15px;
  font-weight: 800;
  letter-spacing: 0.18em;
  color: #fff;
  text-shadow: 0 2px 10px rgba(0,0,0,.65);
}

.txp-conductor-top-right{
  position: fixed;
  top: calc(env(safe-area-inset-top, 0px) + 10px);
  right: calc(env(safe-area-inset-right, 0px) + 12px);
  z-index: 6;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0;
  pointer-events: none;
}
.txp-conductor-top-right > * { pointer-events: auto; }

.txp-brand-badge{
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #fff;
  display: grid;
  place-items: center;
  box-shadow: 0 4px 16px rgba(0,0,0,.28);
  margin-bottom: 12px;
  flex-shrink: 0;
}
.txp-brand-badge__img{
  width: 38px;
  height: 38px;
  object-fit: contain;
}

.brand-logo{
  width:64px; height:64px; object-fit:contain;
  filter: drop-shadow(0 0 10px rgba(255,209,102,.5));
}


.bottom-cta{
  position:absolute; left:0; right:0; bottom:72px; z-index:var(--txp-ui-z);
  display:flex; justify-content:center; padding:0 16px;
}
.btn-brand{
  background: linear-gradient(180deg, var(--txp-brand), var(--txp-brand-2));
  color:#1b1b1b; font-weight:800; border:none; border-radius:16px;
  box-shadow: 0 20px 45px rgba(255,159,28,.35), inset 0 2px 0 rgba(255,255,255,.35);
  text-transform: uppercase; letter-spacing:.3px;
}
.btn-xxl{ padding:16px 26px; font-size:18px; }
#driver-online-toggle.btn-brand{
  padding: 9px 14px;
  font-size: 13px;
  font-weight: 800;
  border-radius: 14px;
  line-height: 1.1;
  white-space: nowrap;
  box-shadow: 0 8px 22px rgba(255,159,28,.30), inset 0 2px 0 rgba(255,255,255,.35);
}


.geo-badge{
  position: absolute;
  left: calc(env(safe-area-inset-left, 0px) + 16px);
  top: calc(env(safe-area-inset-top, 0px) + 68px);
  bottom: auto;
  z-index: var(--txp-ui-z);
  background: rgba(0,0,0,.55); color:#fff; backdrop-filter: blur(6px);
  border:1px solid rgba(255,255,255,.15);
  border-radius:10px; padding:6px 10px; font-size:12px;
}


.txd-banner{
  position: fixed; left: 50%; transform: translateX(-50%);
  bottom: 120px; background: rgba(17,24,39,.92); color:#fff;
  padding: 10px 14px; border-radius: 12px; font-size:14px;
  display: flex; align-items: center; gap: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,.25);
  transition: opacity .25s, transform .25s;
  opacity:0; pointer-events:none; z-index: 1000;
}
.txd-banner.show{ opacity:1; pointer-events:auto; transform: translate(-50%,0); }


.quick-menu{
  position:absolute;
  left: calc(env(safe-area-inset-left, 0px) + 16px);
  top:  calc(env(safe-area-inset-top, 0px) + 14px);
  z-index: calc(var(--txp-ui-z) + 1);
}
.qm-toggle{
  width:54px;height:54px;border:0;border-radius:50%;
  display:grid;place-items:center;cursor:pointer;
  background:linear-gradient(180deg,var(--txp-brand),var(--txp-brand-2));
  color:#1a1a1a;font-size:18px;
  box-shadow:0 12px 30px rgba(255,159,28,.35), inset 0 2px 0 rgba(255,255,255,.35);
  transition:transform .15s ease, filter .15s ease;
}
.qm-toggle:hover{ filter:brightness(1.03); transform:translateY(-1px); }

.qm-items{ position:relative; width:0; height:0; pointer-events:none; }

.qm-item{
  position:absolute; left:0; top:0;              
  width:50px; height:50px; border-radius:50%;
  display:grid; place-items:center; text-decoration:none; cursor:pointer;
  background:linear-gradient(180deg,var(--txp-brand),var(--txp-brand-2)); color:#000;
  box-shadow:0 10px 24px rgba(255,159,28,.28), inset 0 2px 0 rgba(255,255,255,.28);
  transform:translateY(0) scale(.92);
  opacity:0; pointer-events:none;
  transition:transform .28s cubic-bezier(.2,.7,.2,1), opacity .28s ease;
  transition-delay: calc(var(--i,1) * 20ms);
}


.quick-menu.open .qm-items{ pointer-events:auto; }
.quick-menu.open .qm-item{
  opacity:1; pointer-events:auto;
  transform:translateY(calc(var(--i,1) * var(--qm-gap))) scale(1); 
}


.qm-item .tip{
  position:absolute;
  left: calc(100% + 10px);   
  right: auto;
  top:50%; transform:translateY(-50%) scale(.96);
  background:rgba(0,0,0,.9); color:#fff; padding:6px 8px; font-size:12px; border-radius:8px; white-space:nowrap;
  opacity:0; pointer-events:none; transition:opacity .18s, transform .18s; box-shadow:0 8px 18px rgba(0,0,0,.35);
}
.qm-item .tip::after{
  content:"";
  position:absolute;
  right:100%; left:auto; top:50%; transform:translateY(-50%);
  border:6px solid transparent; border-right-color:rgba(0,0,0,.9); /* flecha apuntando desde el tip hacia el botón */
}

@media (max-width:480px){
  .quick-menu{
    left: calc(env(safe-area-inset-left, 0px) + 12px);
    top:  calc(env(safe-area-inset-top, 0px) + 12px);
  }
  :root{ --qm-gap:58px; }
  .qm-item .tip{ font-size:11px; }
}

.txp-sos-fab {
  position: fixed;
  left: calc(env(safe-area-inset-left, 0px) + 16px);
  bottom: calc(110px + env(safe-area-inset-bottom));
  z-index: 10005;
  width: 52px; height: 52px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #ef4444, #b91c1c);
  color: #fff;
  font-weight: 800;
  font-size: 0.72rem;
  letter-spacing: 0.04em;
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.45);
  cursor: pointer;
  pointer-events: auto;
}
.txp-sos-fab:active { transform: scale(0.96); }
.txp-sos-fab--confirm { background: linear-gradient(135deg, #f97316, #c2410c); }

.txp-drv-code-input {
  font-size: 1.45rem !important;
  font-weight: 800;
  letter-spacing: .18em;
  padding: 14px 12px !important;
  border-radius: 14px !important;
  border: 2px solid rgba(255, 209, 102, .45) !important;
}

.txp-sheet{position:fixed; inset:0; display:none; z-index:var(--txp-sheet-z);}
.txp-sheet[aria-hidden="false"]{display:block;}
.txp-sheet-backdrop{position:absolute; inset:0; background:rgba(0,0,0,.35); backdrop-filter:blur(2px);}
.txp-sheet-dialog{
  position:absolute; left:0; right:0; bottom:0;
  background:#fff; color:#0f172a;
  border-radius:18px 18px 0 0;
  box-shadow:0 -12px 40px rgba(0,0,0,.25);
  padding:14px 14px calc(52px + env(safe-area-inset-bottom, 16px));
  max-height:86vh; overflow:auto;
}
.txp-sheet-handle{
  width:46px; height:5px; border-radius:99px; background:#e5e7eb;
  margin:6px auto 12px;
}


.txp-resumen{ background:#f8fafc; border-radius:14px; padding:10px 12px; margin-bottom:10px; }
.txp-par{ display:flex; gap:10px; align-items:center; margin-bottom:6px; }
.txp-dot{ width:10px; height:10px; border-radius:50%; }
.txp-dot.origen{ background:#10b981; }
.txp-dot.destino{ background:#ff9f1c; }
.txp-col .txp-lab{ font-size:.78rem; color:#64748b; }
.txp-col .txp-val{ font-weight:600; color:#0f172a; }
.txp-meta{ margin-top:4px; color:#334155; font-size:.86rem; font-weight:600; }


@media (prefers-color-scheme: dark){
  .txp-sheet-dialog{ background:#0b0f19; color:#e5e7eb; }
  .txp-sheet-handle{ background:#1f2937; }
  .txp-resumen{ background:#0f172a; }
  .txp-col .txp-val{ color:#e5e7eb; }
  .txp-meta{ color:#cbd5e1; }
}


#txd-map-root::after{
  content:""; position: fixed; left: 0; right: 0; bottom: 0;
  height: calc(90px + env(safe-area-inset-bottom, 0px)); pointer-events: none;
  background: linear-gradient(0deg, rgba(0,0,0,.65), rgba(0,0,0,0));
}


#driver-online-toggle{
  position: static;
  margin-top: 0;
}
#driver-online-toggle i{ margin-right: .35rem; }
@media (max-width: 480px){
  .txp-brand-badge{ width: 44px; height: 44px; margin-bottom: 10px; }
  .txp-brand-badge__img{ width: 34px; height: 34px; }
  #driver-online-toggle.btn-brand{ padding: 8px 12px; font-size: 12px; }
}

#txd-trip-cta{ display:none; }


#txd-trip-cta{
  display: none;              
  flex-direction: column;
  align-items: center;
  gap: 8px;
}


#txd-trip-cta > *{
  width: 100%;
  max-width: 740px;
}


#txd-nav-row .btn,
#txd-comm-row .btn{
  flex: 1;
}


#txd-trip-cta.bottom-cta{
  bottom: calc(130px + env(safe-area-inset-bottom, 0px));
}


#txd-trip-cta{
  gap: 14px;                    
}
#txd-comm-row{ 
  margin-bottom: 8px;           
}


#txd-nav-row .btn,
#txd-comm-row .btn{
  height: 38px;
  border-radius: 18px;
  padding: 6px 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,.15);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}


#txd-comm-row .btn{
  background: #fff !important;
  color: #111 !important;
  border: 0 !important;
}


#txd-nav-row .btn i,
#txd-comm-row .btn i{
  margin-right: 6px;
  opacity: .9;
}


#txd-trip-cta > #txd-nav-row,
#txd-trip-cta > #txd-comm-row{
  width: 100%;
  max-width: 740px;  
  margin-left: auto;
  margin-right: auto;
  gap: 10px;          
}


@media (max-width: 480px){
  #txd-trip-cta.bottom-cta{
    bottom: calc(140px + env(safe-area-inset-bottom, 0px));
  }
  #txd-nav-row .btn,
  #txd-comm-row .btn{
    height: 36px;
    border-radius: 16px;
  }
}
body.txd-chat-open #drv-actions { display: none !important; }
body.txd-chat-open #txd-trip-cta { display: none !important; }

.txd-recenter-fab{
  position: fixed;
  right: calc(env(safe-area-inset-right, 0px) + 16px);
  bottom: calc(110px + env(safe-area-inset-bottom));
  z-index: 860;
  width: 52px; height: 52px; border: 0; border-radius: 50%;
  display: grid; place-items: center; cursor: pointer;
  background: rgba(255,255,255,.95); color: #111;
  box-shadow: 0 10px 30px rgba(0,0,0,.35);
}
.txd-recenter-fab:hover{ filter: brightness(1.03); }
.txd-recenter-fab:active{ transform: scale(0.96); }

#txd-chat[data-badge="1"]::after{
  content:"";
  position:absolute; top:6px; right:6px;
  width:9px; height:9px; border-radius:50%;
  background:#ef4444; box-shadow:0 0 0 2px #fff;
}


.txp-modal{ position:fixed; inset:0; z-index:2000; display:none; }
.txp-modal[aria-hidden="false"]{ display:block; }
.txp-modal-backdrop{
  position:absolute; inset:0;
  background: rgba(0,0,0,.55);
  backdrop-filter: blur(2px);
}
.txp-modal-dialog{
  position:absolute; left:50%; top:50%; transform:translate(-50%,-50%);
  width: min(560px, calc(100% - 32px));
  background:#0b0f19; color:#e5e7eb;
  border-radius:16px; border:1px solid rgba(255,255,255,.12);
  box-shadow: 0 24px 60px rgba(0,0,0,.45);
  padding:16px;
}
.txp-modal-header{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.txp-badge{
  background: linear-gradient(180deg, var(--txp-brand), var(--txp-brand-2));
  color:#111; font-weight:800; font-size:12px;
  padding:4px 8px; border-radius:999px;
  box-shadow: 0 8px 20px rgba(255,159,28,.25);
}
.txp-modal-body{ font-size:14px; line-height:1.45; }
.txp-list{ margin:0 0 10px 18px; }
.txp-list li{ margin:6px 0; }
.txp-note{ font-size:12.5px; opacity:.9; }
.txp-modal-actions{ margin-top:8px; }


.top-controls{ align-items: flex-start !important; }


.brand-wrap{
  position: fixed !important;
  top:  calc(env(safe-area-inset-top, 0px) + 12px);
  /* 12px (margen) + 54px (ancho del botón) + 12px (separación) */
  right: calc(env(safe-area-inset-right, 0px) + 12px + 54px + 12px);
  z-index: 5; /* el botón tiene z-index:6, así quedan en orden */
}


.brand-logo{
  width: 56px; height: 56px; object-fit: contain;
  filter: drop-shadow(0 0 10px rgba(255,209,102,.5));
}


@media (max-width: 400px){
  .brand-wrap{
    right: calc(env(safe-area-inset-right, 0px) + 10px + 50px + 10px);
  }
  .brand-logo{ width: 50px; height: 50px; }
}

/* Ocultar chatbot cuando el conductor tiene una oferta o viaje activo */
#drv-sheet-solicitud[aria-hidden="false"] ~ * #txp-assistant-root,
body:has(#drv-sheet-solicitud[aria-hidden="false"]) #txp-assistant-root,
body:has(#txd-trip-cta:not([style*="display:none"]):not([style*="display: none"])) #txp-assistant-root {
  display: none !important;
}
</style>
@endsection

@section('pagejs')
<script src="{{ asset('js/taxpiya-geolocation.js') }}?v=1"></script>
<script src="{{ asset('js/taxpiya-background.js') }}?v=3"></script>
<script>

function getCsrf(){
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}
function txpGetCurrentPosition(opts){
  if (window.TxpGeo?.getCurrentPosition) return window.TxpGeo.getCurrentPosition(opts);
  return new Promise((res, rej) => {
    if (!navigator.geolocation) { rej(new Error('no geolocation')); return; }
    navigator.geolocation.getCurrentPosition(res, rej, opts);
  });
}
</script>

<script>

window.__txpActiveTrip = @json($activeTripBootstrap ?? null);

const CONDUCTOR_DISPONIBLE_URL = "{{ route('conductor.disponible') }}";
const CONDUCTOR_POSICION_URL   = "{{ route('conductor.posicion') }}";
const CONDUCTOR_SOLICITUD_URL  = "{{ route('conductor.solicitud') }}";
const VIAJE_ACEPTAR_URL        = "{{ route('viaje.aceptar') }}";
const VIAJE_RECHAZAR_URL       = "{{ route('viaje.rechazar') }}";
const VIAJE_ESTADO_BASE        = "{{ url('/viaje/estado') }}";   
const VIAJE_LLEGO_URL          = "{{ route('viaje.llego') }}";   
const VIAJE_TERMINAR_URL       = "{{ route('viaje.terminar') }}";
const VIAJE_VERIFICAR_URL      = "{{ route('viaje.verificar.codigo') }}";


window.currentViajeId = window.__txpActiveTrip?.id ?? window.currentViajeId ?? null;
let isOnline = (document.getElementById('driver-online-toggle')?.dataset.online === '1');
window.isOnline = isOnline;
setOnlineBtn(isOnline);


const txpMapStyle = [
  { elementType: "geometry", stylers: [{color:"#0b132b"}] },
  { elementType: "labels.text.stroke", stylers: [{color:"#0b132b"}] },
  { elementType: "labels.text.fill", stylers: [{color:"#e0e7ff"}] },
  { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{color:"#ffd166"}] },
  { featureType: "poi", elementType: "labels.text.fill", stylers: [{color:"#cbd5e1"}] },
  { featureType: "poi.park", elementType: "geometry", stylers: [{color:"#1c2541"}] },
  { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{color:"#a7f3d0"}] },
  { featureType: "road", elementType: "geometry", stylers: [{color:"#3a506b"}] },
  { featureType: "road", elementType: "geometry.stroke", stylers: [{color:"#263b5e"}] },
  { featureType: "road", elementType: "labels.text.fill", stylers: [{color:"#e5e7eb"}] },
  { featureType: "road.highway", elementType: "geometry", stylers: [{color:"#4f5d75"}] },
  { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{color:"#1f2937"}] },
  { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{color:"#ffd166"}] },
  { featureType: "transit", elementType: "geometry", stylers: [{color:"#2b3a55"}] },
  { featureType: "water", elementType: "geometry", stylers: [{color:"#0b1220"}] },
  { featureType: "water", elementType: "labels.text.fill", stylers: [{color:"#9ca3af"}] }
];


const CAR_ICON_URL = "{{ asset('images/carrotaxpiya.png') }}";


let map, geocoder, infoWindow;
let driverCar = null, driverHalo = null;
let watchId = null;
let lastSendTs = 0;


function showBanner(text, icon='fa-circle-info'){
  const b = document.getElementById('txd-banner');
  const i = document.getElementById('txd-banner-ico');
  const t = document.getElementById('txd-banner-txt');
  if (!b || !i || !t) return;
  i.className = `fa-solid ${icon} me-2`;
  t.textContent = text;
  b.classList.add('show');
  setTimeout(()=> b.classList.remove('show'), 2800);
}
function setOnlineBtn(on){
  const btn = document.getElementById('driver-online-toggle');
  if (!btn) return;
  btn.innerHTML = on
    ? '<i class="fa-solid fa-power-off me-2"></i> NO DISPONIBLE'
    : '<i class="fa-solid fa-power-off me-2"></i> DISPONIBLE';
}
function showAccuracy(m){
  const el = document.getElementById('geo-accuracy');
  if (!el) return;
  el.textContent = `Precisión: ~${Math.round(m)} m`;
  el.style.display = 'block';
}

function centerMapOnce(lat, lng, zoom = 16){
  if (!map) return;
  if (!map.__centeredOnce){
    map.panTo({ lat, lng });
    map.setZoom(zoom);
    map.__centeredOnce = true;
  }
}

function centerMapNow(){
  if (!map) return;
  const go = (p) => {
    map.__centeredOnce = true;
    map.panTo(p);
    map.setZoom(Math.max(15, map.getZoom() || 15));
    try { map._map?.invalidateSize?.(true); } catch (_) {}
  };
  if (window.__lastDriverPos) { go(window.__lastDriverPos); return; }
  txpGetCurrentPosition({ enableHighAccuracy: true, timeout: 12000, maximumAge: 0 })
    .then((pos) => {
      const p = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      window.__lastDriverPos = p;
      putDriverMarker(p.lat, p.lng);
      go(p);
    })
    .catch(() => showBanner?.('No se pudo obtener ubicación', 'fa-triangle-exclamation'));
}
document.getElementById('txd-recenter')?.addEventListener('click', centerMapNow);


const CAR_RATIO = 184 / 424;
function carSizeByZoom(zoom = 15){
  const clamp = (v,a,b)=>Math.max(a, Math.min(b, v));
  const baseH = 86;
  const h = clamp(Math.round(baseH * Math.pow(1.12, (zoom - 15))), 72, 148);
  const w = Math.round(h * CAR_RATIO);
  return { w, h };
}
function carAnchorForSize({ w, h }){ return new google.maps.Point(Math.round(w/2), Math.round(h*0.85)); }
function carIconForZoom(zoom){ const { w, h } = carSizeByZoom(zoom); return { url: CAR_ICON_URL, scaledSize: new google.maps.Size(w, h), anchor: carAnchorForSize({ w, h }) }; }
function haloIconForZoom(zoom){
  const { w, h } = carSizeByZoom(zoom);
  const d = Math.round(Math.max(w, h) * 0.9);
  const r = Math.round(d / 2);
  const svg =
    `<svg xmlns="http://www.w3.org/2000/svg" width="${d}" height="${d}" viewBox="0 0 ${d} ${d}">
      <defs>
        <radialGradient id="g" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#ffffff" stop-opacity="0.50"/>
          <stop offset="60%" stop-color="#ffffff" stop-opacity="0.20"/>
          <stop offset="100%" stop-color="#000000" stop-opacity="0"/>
        </radialGradient>
        <filter id="f" x="-50%" y="-50%" width="200%" height="200%">
          <feGaussianBlur stdDeviation="${Math.max(2, Math.round(d*0.05))}" />
        </filter>
      </defs>
      <circle cx="${r}" cy="${r}" r="${Math.round(r*0.85)}" fill="url(#g)" filter="url(#f)"/>
    </svg>`;
  const url = "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
  return { url, scaledSize: new google.maps.Size(d, d), anchor: new google.maps.Point(Math.round(d/2), Math.round(d*0.6)) };
}
function refreshDriverIconsByZoom(){
  if (!map) return;
  const z = map.getZoom() || 15;
  if (driverCar)  driverCar.setIcon(carIconForZoom(z));
  if (driverHalo) driverHalo.setIcon(haloIconForZoom(z));
}
function putDriverMarker(lat, lng){
  const pos = new google.maps.LatLng(lat, lng);
  const z = map?.getZoom?.() || 15;
  if (!driverHalo){
    driverHalo = new google.maps.Marker({ position: pos, map, clickable: false, zIndex: 450, icon: haloIconForZoom(z) });
  } else driverHalo.setPosition(pos);
  if (!driverCar){
    driverCar = new google.maps.Marker({ position: pos, map, clickable: false, zIndex: 500, icon: carIconForZoom(z), optimized: true });
  } else driverCar.setPosition(pos);
}

function startWatch(){
  stopWatch();
  const onPos = (pos) => handlePosition(pos);
  const onErr = (err) => console.warn('Geoloc error:', err);
  const opts = { enableHighAccuracy:true, maximumAge:0, timeout:20000 };
  if (window.TxpGeo?.watchPosition) {
    window.TxpGeo.watchPosition(onPos, onErr, opts).then((id) => { watchId = id; }).catch(onErr);
  } else if (navigator.geolocation) {
    watchId = navigator.geolocation.watchPosition(onPos, onErr, opts);
  }
}
function stopWatch(){
  if (!watchId) return;
  if (window.TxpGeo?.clearWatch) window.TxpGeo.clearWatch(watchId);
  else if (navigator.geolocation) navigator.geolocation.clearWatch(watchId);
  watchId = null;
}

async function handlePosition(pos){
  const { latitude, longitude, accuracy, heading, speed } = pos.coords;
  const lat = latitude, lng = longitude;
  showAccuracy(accuracy);
  putDriverMarker(lat, lng);

centerMapOnce(lat, lng);
window.__lastDriverPos = { lat, lng };

  const now = Date.now();
  if (isOnline && (now - lastSendTs) > 3000) {
    lastSendTs = now;
    try{
      await fetch(CONDUCTOR_POSICION_URL, {
        method:'POST',
        headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify({
          lat, lng,
          heading: (heading != null && !isNaN(heading)) ? heading : null,
          velocidad_kmh: (speed != null && !isNaN(speed)) ? Math.max(0, Math.round(speed * 3.6)) : null
        })
      });
    }catch(e){ }
  }
}


async function setOnline(on){
  const btn = document.getElementById('driver-online-toggle');
  if (btn) btn.disabled = true;
  try{
    const r = await fetch(CONDUCTOR_DISPONIBLE_URL, {
      method:'POST',
      headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ disponible: !!on })
    });
    let j = {};
    try { j = await r.json(); } catch (_) {
      throw new Error(r.status >= 500 ? 'Error del servidor al conectar. Intenta de nuevo.' : 'No se pudo cambiar estado');
    }
    if (!r.ok || !j.ok) throw new Error(j?.message || 'No se pudo cambiar estado');

    isOnline = !!j.disponible;
    window.isOnline = isOnline;
    setOnlineBtn(isOnline);
    showBanner(isOnline ? 'Estás en línea' : 'Estás fuera de línea', isOnline ? 'fa-signal' : 'fa-power-off');
if (isOnline && window.__lastDriverPos){
  const {lat,lng} = window.__lastDriverPos;
  centerMapOnce(lat, lng);
}
if (isOnline) {
  if (window.Capacitor?.isNativePlatform?.()) { startBgWatcher(); } else { startWatch(); }
  if (navigator.geolocation) {
    txpGetCurrentPosition({ enableHighAccuracy: true, timeout: 12000, maximumAge: 0 })
      .then((pos) => handlePosition(pos)).catch(() => {});
  }
  startOfferPoll();
  window.TxpBackground?.syncConductor?.();
} else {
  if (window.Capacitor?.isNativePlatform?.()) { stopBgWatcher(); } else { stopWatch(); }
  stopOfferPoll();
  window.TxpBackground?.syncConductor?.();
}
  }catch(e){
    showBanner(e.message || 'No se pudo cambiar tu estado', 'fa-triangle-exclamation');
  }finally{ if (btn) btn.disabled = false; }
}


window.initMap = function(){
  const mapEl = document.getElementById('map');
  map = new google.maps.Map(mapEl, {
    center: { lat: 6.2476, lng: -75.5658 },
    zoom: 14,
    disableDefaultUI: true,
    styles: txpMapStyle,
    gestureHandling: 'greedy'
  });
  geocoder = new google.maps.Geocoder();
  infoWindow = new google.maps.InfoWindow();

  map.addListener('zoom_changed', refreshDriverIconsByZoom);

  const toggle = document.getElementById('driver-online-toggle');
  toggle?.addEventListener('click', ()=> setOnline(!isOnline));
};
</script>

<script>

(function(){
  const qm = document.getElementById('quickMenu');
  const btn = document.getElementById('qmToggle');

  function closeMenu(){ qm?.classList.remove('open'); btn?.setAttribute('aria-expanded','false'); }

  btn?.addEventListener('click', (e)=>{
    e.stopPropagation();
    const open = !qm.classList.contains('open');
    qm.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.addEventListener('click', (e)=>{ if(qm && !qm.contains(e.target)) closeMenu(); });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeMenu(); });

  const goBtn = document.getElementById('qmGoOnline');
  function refreshGoBtn(){
    const ico = goBtn?.querySelector('i');
    const tip = goBtn?.querySelector('.tip');
    if (!goBtn || !ico || !tip) return;
    if (window.isOnline){ ico.className = 'fa-solid fa-signal'; tip.textContent = 'Desconectarme'; }
    else { ico.className = 'fa-solid fa-power-off'; tip.textContent = 'Conectarme'; }
  }
  if (goBtn){
    refreshGoBtn();
    goBtn.addEventListener('click', async (e)=>{
      e.preventDefault();
      await setOnline(!window.isOnline);
      refreshGoBtn();
      closeMenu();
    });
  }

  document.getElementById('qmCuenta')?.addEventListener('click', ()=>{ closeMenu(); });
})();

(function(){
  const SOS_URL = @json(route('sos.reportar'));
  const btn = document.getElementById('txp-sos-btn');
  let confirm = false;
  btn?.addEventListener('click', async ()=>{
    if (!confirm) {
      confirm = true;
      btn.classList.add('txp-sos-fab--confirm');
      btn.textContent = 'OK?';
      setTimeout(()=>{ confirm = false; btn.classList.remove('txp-sos-fab--confirm'); btn.textContent = 'SOS'; }, 4000);
      return;
    }
    btn.disabled = true;
    btn.textContent = '...';
    try {
      const pos = await txpGetCurrentPosition({ timeout: 8000, enableHighAccuracy: true });
      const body = new URLSearchParams({
        _token: document.querySelector('meta[name="csrf-token"]')?.content || '',
        lat: pos.coords.latitude,
        lng: pos.coords.longitude,
        viaje_id: window.currentViajeId || '',
        descripcion: 'Alerta SOS conductor',
      });
      const r = await fetch(SOS_URL, { method:'POST', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body, credentials:'same-origin' });
      const j = await r.json();
      showBanner?.(j.message || 'SOS enviado', 'fa-triangle-exclamation');
    } catch(e) {
      showBanner?.('SOS enviado (sin GPS)', 'fa-triangle-exclamation');
      const body = new URLSearchParams({ _token: document.querySelector('meta[name="csrf-token"]')?.content || '', descripcion: 'Alerta SOS conductor' });
      fetch(SOS_URL, { method:'POST', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body, credentials:'same-origin' });
    }
    btn.disabled = false; btn.textContent = 'SOS'; confirm = false;
    btn.classList.remove('txp-sos-fab--confirm');
  });
})();
</script>

<script>

const offer = {
  timer: null,
  pollHandle: null,
  baseMs: 3000,
  backoffMs: 3000,
  maxBackoffMs: 60000,
  ttlSec: 60,
  remaining: 0,
  data: null,   
};

function drvSheet(show){
  const el = document.getElementById('drv-sheet-solicitud');
  if (!el) return;
  el.setAttribute('aria-hidden', show ? 'false' : 'true');
  document.body.style.overflow = show ? 'hidden' : '';
}
function fmtCOP(v, mon='COP'){
  return v==null ? '—' : new Intl.NumberFormat('es-CO',{style:'currency',currency:mon,maximumFractionDigits:0}).format(v);
}

function scheduleOfferPoll(delay){ if (offer.pollHandle) clearTimeout(offer.pollHandle); offer.pollHandle = setTimeout(fetchOfferOnce, delay); }
function startOfferPoll(){ stopOfferPoll(); offer.backoffMs = offer.baseMs; scheduleOfferPoll(0); }
function stopOfferPoll(){ if (offer.pollHandle){ clearTimeout(offer.pollHandle); offer.pollHandle = null; } }

async function fetchOfferOnce(){
  if (!window.isOnline || window.currentViajeId || offer.data){ scheduleOfferPoll(offer.backoffMs); return; }
  try{
    const r = await fetch(CONDUCTOR_SOLICITUD_URL, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }, cache: 'no-store', credentials: 'same-origin' });

    if (r.redirected || r.status === 401 || r.status === 403){ stopOfferPoll(); showBanner?.('Tu sesión expiró. Inicia sesión.', 'fa-right-to-bracket'); return; }
    if (r.status === 404 || r.status === 204){ offer.backoffMs = offer.baseMs; scheduleOfferPoll(offer.backoffMs); return; }
    if (!r.ok){ offer.backoffMs = Math.min(offer.maxBackoffMs, Math.round(offer.backoffMs * 1.8)); scheduleOfferPoll(offer.backoffMs); return; }

    const ct = r.headers.get('content-type') || '';
    if (!ct.includes('application/json')){ offer.backoffMs = Math.min(offer.maxBackoffMs, Math.round(offer.backoffMs * 1.8)); scheduleOfferPoll(offer.backoffMs); return; }

    const j = await r.json();
    if (!j?.ok || !j.viaje){ offer.backoffMs = offer.baseMs; scheduleOfferPoll(offer.backoffMs); return; }

   
    offer.data = j.viaje;
    const elO = document.getElementById('drv-o-txt');
if (elO) elO.textContent = j.viaje.o?.txt || '—';

const elD = document.getElementById('drv-d-txt');
if (elD) elD.textContent = j.viaje.d?.txt || '—';

const elM = document.getElementById('drv-monto');
if (elM) elM.textContent = fmtCOP(j.viaje.monto ?? 0, j.viaje.mon || 'COP');

    if (typeof playOfferSound === 'function') playOfferSound();
    showBanner?.('Nueva solicitud de viaje', 'fa-taxi');
    drvSheet(true);
    startOfferCountdown();
  }catch(e){
    offer.backoffMs = Math.min(offer.maxBackoffMs, Math.round(offer.backoffMs * 1.8));
    scheduleOfferPoll(offer.backoffMs);
  }
}

function startOfferCountdown(){
  offer.remaining = offer.ttlSec;
  updateCountdown();
  if (offer.timer) clearInterval(offer.timer);
  offer.timer = setInterval(()=>{
    offer.remaining -= 1;
    updateCountdown();
    if (offer.remaining <= 0){ clearInterval(offer.timer); offer.timer = null; autoReject(); }
  }, 1000);
}
function updateCountdown(){ const el = document.getElementById('drv-countdown'); if (el) el.textContent = `${offer.remaining}s`; }

async function autoReject(){
  try{
    if (offer?.data?.id){
      await fetch(VIAJE_RECHAZAR_URL, {
        method: 'POST',
        headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify({ viaje_id: offer.data.id })
      });
    }
  }catch(e){}
  closeOffer();
}
function closeOffer(){
  drvSheet(false);
  offer.data = null;
  if (offer.timer){ clearInterval(offer.timer); offer.timer = null; }
  const cd = document.getElementById('drv-countdown'); if (cd) cd.textContent = `${offer.ttlSec}s`;
  offer.backoffMs = offer.baseMs;
  scheduleOfferPoll(offer.backoffMs);
}


document.getElementById('drv-btn-rechazar')?.addEventListener('click', async ()=>{
  if (!offer?.data?.id){ closeOffer(); return; }
  const btn = document.getElementById('drv-btn-rechazar');
  btn.disabled = true;
  try{
    await fetch(VIAJE_RECHAZAR_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: offer.data.id })
    });
  }catch(e){}
  btn.disabled = false;
  closeOffer();
});


document.getElementById('drv-btn-aceptar')?.addEventListener('click', async ()=>{
  if (!offer?.data?.id) return;
  const btn = document.getElementById('drv-btn-aceptar');
  btn.disabled = true;
  const old = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Aceptando…';
  try{
    const r = await fetch(VIAJE_ACEPTAR_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: offer.data.id })
    });
    const j = await r.json();
    if (!r.ok || !j?.ok) throw new Error(j?.message || 'No se pudo aceptar');

 
    window.currentViajeId = j.viaje.id;

    
    { const el = document.getElementById('txd-trip-cta'); if (el) el.style.setProperty('display','flex'); }
    if (typeof refreshNavAndCommRows === 'function') refreshNavAndCommRows();


    
    window.__viajeTargets = { o: j.viaje.o, d: j.viaje.d };

   
    const tel = j?.pasajero?.telefono || j?.viaje?.pasajero?.telefono || j?.telefono_pasajero || null;
    const callBtn = document.getElementById('txd-call');
    if (callBtn){
      if (tel){ callBtn.href = `tel:${tel}`; callBtn.style.removeProperty('display'); }
      else { callBtn.removeAttribute('href'); }
    }

    showBanner('¡Viaje aceptado! Dirígete al origen', 'fa-taxi');
    startRoutingToPickup();

    if (j.viaje.o?.lat && j.viaje.o?.lng && window.map){
      map.panTo({lat:j.viaje.o.lat, lng:j.viaje.o.lng});
      map.setZoom(15);
    }

    showDrvActions(true);
    startDrvStateLoop();
    closeOffer();
  }catch(e){
    showBanner(e.message || 'Error al aceptar', 'fa-triangle-exclamation');
  }finally{
    btn.disabled = false;
    btn.innerHTML = old;
  }
});
</script>

<script>

(function ensureDrvActions(){
  if (document.getElementById('drv-actions')) return;
  const wrap = document.createElement('div');
  wrap.id = 'drv-actions';
  wrap.style.cssText = `
    position:fixed; left:0; right:0; bottom:86px; z-index:4;
    display:none; padding:0 16px;
  `;
  wrap.innerHTML = `
    <div class="d-flex flex-column gap-2 justify-content-center" style="max-width:720px;margin:0 auto;">
      <div class="d-flex gap-2">
        <button id="drv-act-llego" class="btn btn-warning flex-fill">
          <i class="fa-solid fa-flag me-1"></i> Llegé al origen
        </button>
        <button id="drv-act-terminar" class="btn btn-danger flex-fill">
          <i class="fa-solid fa-flag-checkered me-1"></i> Terminar viaje
        </button>
      </div>
      <div id="drv-code-block" style="display:none;">
        <div style="background:rgba(15,23,42,.97); border:2px solid rgba(255,209,102,.4); border-radius:16px; padding:12px 14px;">
          <div style="font-size:0.75rem; color:#ffd166; font-weight:700; text-align:center; margin-bottom:8px;">
            <i class="fa-solid fa-shield-halved me-1"></i>INGRESA EL CÓDIGO DEL PASAJERO
          </div>
          <div class="d-flex gap-2">
            <input id="drv-code-input" type="number" inputmode="numeric" maxlength="4" placeholder="0000"
              class="form-control text-center fw-bold"
              style="font-size:1.6rem; letter-spacing:.2em; border-radius:12px; border:2px solid rgba(255,209,102,.5); background:#0b0f19; color:#fff; padding:10px 8px;"
            >
            <button id="drv-code-verify" class="btn btn-warning" style="white-space:nowrap; min-width:100px; border-radius:12px;">
              <i class="fa-solid fa-check me-1"></i>Verificar
            </button>
          </div>
          <div id="drv-code-error" style="font-size:0.75rem; color:#f87171; margin-top:6px; display:none;"></div>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(wrap);
})();

function showDrvActions(show){
  const el = document.getElementById('drv-actions');
  if (!el) return;
  el.style.display = show ? 'block' : 'none';
}
function setDrvActionsState(estado){
  const bLlegue = document.getElementById('drv-act-llego');
  const bTerm   = document.getElementById('drv-act-terminar');
  const codeBlock = document.getElementById('drv-code-block');
  if (!bLlegue || !bTerm) return;
  const llegaOk = (estado === 'asignado' || estado === 'en_camino');
  const termOk  = (estado === 'iniciado');   // solo activado DESPUÉS del código
  const codeOk  = (estado === 'llego');       // mostrar input de código cuando llego
  bLlegue.disabled = !llegaOk;
  bTerm.disabled   = !termOk;
  if (codeBlock) codeBlock.style.display = codeOk ? 'block' : 'none';
}


document.getElementById('drv-act-llego')?.addEventListener('click', async ()=>{
  if (!window.currentViajeId || !VIAJE_LLEGO_URL) return;
  const btn = document.getElementById('drv-act-llego');
  btn.disabled = true;
  const old = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Enviando…';
  try{
    let lat = null, lng = null;
    try {
      const pos = await txpGetCurrentPosition({ timeout: 8000, enableHighAccuracy: true });
      lat = pos.coords.latitude;
      lng = pos.coords.longitude;
    } catch (_) {}

    const r = await fetch(VIAJE_LLEGO_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: window.currentViajeId, lat, lng })
    });
    const j = await r.json();
    if (!r.ok || !j?.ok) throw new Error(j?.message || 'Error');
    showBanner('Marcado como “Llegué”', 'fa-flag');
    if (typeof checkDrvStateOnce === 'function') checkDrvStateOnce();
  }catch(e){
    showBanner(e.message || 'No se pudo marcar llegada', 'fa-triangle-exclamation');
  }finally{ btn.disabled = false; btn.innerHTML = old; }
});


document.getElementById('drv-act-terminar')?.addEventListener('click', async ()=>{
  if (!window.currentViajeId || !VIAJE_TERMINAR_URL) return;
  const btn = document.getElementById('drv-act-terminar');
  btn.disabled = true;
  const old = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Cerrando…';
  try{
    const r = await fetch(VIAJE_TERMINAR_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: window.currentViajeId })
    });
    const j = await r.json();
    if (!r.ok || !j?.ok) throw 0;

    showBanner('Viaje terminado', 'fa-flag-checkered');
    resetAfterTrip();
    if (window.isOnline && typeof startOfferPoll === 'function') startOfferPoll();
  }catch(e){
    showBanner('No se pudo terminar el viaje', 'fa-triangle-exclamation');
  }finally{ btn.innerHTML = old; }
});
</script>

<script>
// ── Verificación del código de recogida ────────────────────────────────────
document.addEventListener('click', async (e) => {
  if (!e.target.closest('#drv-code-verify')) return;
  const btn     = document.getElementById('drv-code-verify');
  const input   = document.getElementById('drv-code-input');
  const errEl   = document.getElementById('drv-code-error');
  if (!btn || !input || !window.currentViajeId) return;

  const codigo = (input.value || '').trim();
  if (codigo.length < 4) {
    if (errEl) { errEl.textContent = 'El código debe tener 4 dígitos.'; errEl.style.display = 'block'; }
    return;
  }

  const old = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  if (errEl) errEl.style.display = 'none';

  try {
    const r = await fetch(VIAJE_VERIFICAR_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: window.currentViajeId, codigo })
    });
    const j = await r.json();
    if (!r.ok || !j?.ok) {
      const msg = j?.message || 'Código incorrecto.';
      if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; }
      btn.innerHTML = old;
      btn.disabled = false;
      return;
    }
    // Código correcto: ocultar bloque y habilitar "Terminar viaje"
    showBanner('¡Código verificado! Viaje iniciado.', 'fa-check-circle');
    const codeBlock = document.getElementById('drv-code-block');
    if (codeBlock) codeBlock.style.display = 'none';
    const bTerm = document.getElementById('drv-act-terminar');
    if (bTerm) bTerm.disabled = false;
    if (typeof checkDrvStateOnce === 'function') checkDrvStateOnce();
  } catch(err) {
    if (errEl) { errEl.textContent = 'Error de red. Reintenta.'; errEl.style.display = 'block'; }
    btn.innerHTML = old;
    btn.disabled = false;
  }
});

// Limitar input a 4 dígitos
document.addEventListener('input', (e) => {
  if (!e.target.matches('#drv-code-input')) return;
  const v = e.target.value.replace(/\D/g, '').slice(0, 4);
  e.target.value = v;
});
</script>

<script>

let drvEstadoTimer = null;

function startDrvStateLoop(){
  stopDrvStateLoop();
  checkDrvStateOnce();
  drvEstadoTimer = setInterval(checkDrvStateOnce, 3000);
}
function stopDrvStateLoop(){ if (drvEstadoTimer){ clearInterval(drvEstadoTimer); drvEstadoTimer = null; } }

async function checkDrvStateOnce(){
  const id = window.currentViajeId;
  if (!id || !VIAJE_ESTADO_BASE) { showDrvActions(false); return; }

  try{
    const r = await fetch(`${VIAJE_ESTADO_BASE}/${id}`, { cache:'no-store' });
    const j = await r.json();
    if (!j?.ok) return;

    showDrvActions(true);
    setDrvActionsState(j.estado);

    switch (j.estado){
      case 'asignado':
      case 'en_camino':
        startRoutingToPickup();
        break;
      case 'llego':
        break;
      case 'iniciado':
        startRoutingToDropoff();
        break;
case 'terminado':
  showBanner('Viaje finalizado', 'fa-flag-checkered');
  resetAfterTrip();
  if (window.isOnline && typeof startOfferPoll === 'function') startOfferPoll();
  break;

case 'cancelado_pasajero':
case 'cancelado_conductor':
case 'cancelado_sistema':
case 'no_show':
case 'fallo_localizacion':
default:
  showBanner('El viaje fue cancelado', 'fa-circle-exclamation');
  resetAfterTrip();
  if (window.isOnline && typeof startOfferPoll === 'function') startOfferPoll();
  break;
    }

    
    const phone = j?.contact?.phone || j?.pasajero?.telefono || j?.telefono_pasajero || j?.phone || null;
    const callBtn = document.getElementById('txd-call');
    if (callBtn){
      if (phone){ callBtn.href = `tel:${phone}`; callBtn.style.removeProperty('display'); }
      else { callBtn.removeAttribute('href'); }
    }
    refreshNavAndCommRows();

  }catch(e){  }
}
</script>

<script>

document.addEventListener('DOMContentLoaded', ()=>{
  if (window.isOnline) {
    if (window.Capacitor?.isNativePlatform?.()) { startBgWatcher(); } else { startWatch(); }
    txpGetCurrentPosition({ enableHighAccuracy: true, timeout: 12000, maximumAge: 0 })
      .then((pos) => handlePosition(pos)).catch(() => {});
    startOfferPoll();
  }

  if (window.currentViajeId) {
    { const el = document.getElementById('txd-trip-cta'); if (el) el.style.setProperty('display','flex'); }
    refreshNavAndCommRows();
    showDrvActions(true);
    startDrvStateLoop();
  }
});
</script>

<script>

let dirSvc, dirRenderer, routeTimer = null, routeMode = null; 


(function patchHandlePositionForRouting(){
  if (typeof window.handlePosition === 'function'){
    const _orig = window.handlePosition;
    window.handlePosition = async function(pos){
      await _orig(pos);
      window.__lastDriverPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
    };
  }
})();

function ensureDirections(){
  if (!map) return;
  dirSvc = dirSvc || new google.maps.DirectionsService();
  if (!dirRenderer){
    dirRenderer = new google.maps.DirectionsRenderer({
      map,
      suppressMarkers: true,
      polylineOptions: { strokeColor: '#ffd166', strokeOpacity: 0.95, strokeWeight: 5 }
    });
  } else { dirRenderer.setMap(map); }
}

function clearRoute(){
 
  if (dirRenderer) {
    dirRenderer.set('directions', null);
    dirRenderer.setMap(null);   
    dirRenderer = null;        
  }
  const info = document.getElementById('txd-trip-info');
  if (info) info.textContent = '';
}

function resetAfterTrip(){

  stopDrvStateLoop();
  stopRouteLoop();
  clearRoute();

 
  window.currentViajeId = null;
  window.__viajeTargets = {};   
  routeMode = null;

  
  const cta  = document.getElementById('txd-trip-cta');
  const nav  = document.getElementById('txd-nav-row');
  const comm = document.getElementById('txd-comm-row');
  if (cta)  cta.style.setProperty('display','none');
  if (nav)  nav.style.display = 'none';
  if (comm) comm.style.display = 'none';
  showDrvActions(false);

  
  if (map) {
    map.__centeredOnce = false; 
    centerMapNow();             
  }
}

async function computeAndDrawRoute(from, to){
  ensureDirections();
  try{
    const res = await dirSvc.route({ origin: from, destination: to, travelMode: google.maps.TravelMode.DRIVING });
    dirRenderer.setDirections(res);
    const leg = res?.routes?.[0]?.legs?.[0];
    if (leg){
      const km  = (leg.distance.value / 1000).toFixed(1);
      const min = Math.round(leg.duration.value / 60);
      const lbl = routeMode === 'pickup' ? 'al origen' : 'al destino';
      const el  = document.getElementById('txd-trip-info');
      if (el) el.textContent = `Tiempo ${lbl}: ${min} min — ${km} km`;
    }
  }catch(e){ clearRoute(); }
}

function startRouteLoop(target){
  stopRouteLoop();
  computeRouteTick(target);
  routeTimer = setInterval(()=> computeRouteTick(target), 12000);
}
function stopRouteLoop(){ if (routeTimer){ clearInterval(routeTimer); routeTimer = null; } }

function computeRouteTick(target){
  const p = window.__lastDriverPos;
  if (!p || !target) return;
  const from = new google.maps.LatLng(p.lat, p.lng);
  const to   = new google.maps.LatLng(target.lat, target.lng);
  computeAndDrawRoute(from, to);
}

function startRoutingToPickup(){ if (!window.__viajeTargets?.o) return; routeMode = 'pickup'; startRouteLoop(window.__viajeTargets.o); }
function startRoutingToDropoff(){ if (!window.__viajeTargets?.d) return; routeMode = 'dropoff'; startRouteLoop(window.__viajeTargets.d); }
</script>

<script>


function currentTargetForNav(){
  const tg = (window.routeMode === 'dropoff') ? window.__viajeTargets?.d : window.__viajeTargets?.o;
  return tg && tg.lat && tg.lng ? { lat: +tg.lat, lng: +tg.lng } : null;
}

function googleNavUrl(from, to){
  const q = new URLSearchParams({ api: '1', travelmode: 'driving', destination: `${to.lat},${to.lng}` });
  if (from) q.set('origin', `${from.lat},${from.lng}`);
  return `https://www.google.com/maps/dir/?${q.toString()}`;
}

function wazeNavUrl(to){
  const ll = `${to.lat},${to.lng}`;
  const ua = navigator.userAgent.toLowerCase();
  const isIOS = /iphone|ipad|ipod|ios/.test(ua);
  return isIOS ? `waze://?ll=${ll}&navigate=yes` : `https://waze.com/ul?ll=${ll}&navigate=yes`;
}

function refreshNavAndCommRows(){
  const hasTrip = !!window.currentViajeId;
  const navRow  = document.getElementById('txd-nav-row');
  const commRow = document.getElementById('txd-comm-row');
  if (navRow)  navRow.style.display  = hasTrip ? 'flex' : 'none';
  if (commRow) commRow.style.display = hasTrip ? 'flex' : 'none';
}

{ const g = document.getElementById('txd-open-google'); if (g) g.addEventListener('click', ()=>{
  const to = currentTargetForNav();
  if (!to){ showBanner?.('No hay destino disponible', 'fa-triangle-exclamation'); return; }
  const from = window.__lastDriverPos || null;
  window.open(googleNavUrl(from, to), '_blank');
});}

{ const w = document.getElementById('txd-open-waze');   if (w) w.addEventListener('click', ()=>{
  const to = currentTargetForNav();
  if (!to){ showBanner?.('No hay destino disponible', 'fa-triangle-exclamation'); return; }
  window.location.href = wazeNavUrl(to);
});}


document.addEventListener('DOMContentLoaded', refreshNavAndCommRows);


(function patchCheckDrvStateOnceForComm(){
  if (typeof window.checkDrvStateOnce !== 'function') return;
  const _orig = window.checkDrvStateOnce;
  window.checkDrvStateOnce = async function(){
    await _orig();
    const id = window.currentViajeId;
    if (!id || !VIAJE_ESTADO_BASE) return;
    try{
      const r = await fetch(`${VIAJE_ESTADO_BASE}/${id}`, { cache:'no-store' });
      const j = await r.json();
      const phone = j?.contact?.phone || j?.pasajero?.telefono || j?.telefono_pasajero || j?.phone || null;
      const callBtn = document.getElementById('txd-call');
      if (callBtn){
        if (phone){ callBtn.href = `tel:${phone}`; callBtn.style.removeProperty('display'); }
        else { callBtn.removeAttribute('href'); }
      }
      refreshNavAndCommRows();
    }catch(e){}
  };
})();


const VIAJE_CHAT_LIST_BASE = "{{ url('/viaje') }}";          
const VIAJE_CHAT_SEND_URL  = "{{ route('viaje.chat.send.driver') }}";
const VIAJE_CHAT_READ_URL  = "{{ route('viaje.chat.read.driver') }}";

const chat = { poller:null, everyMs:3000, lastId:0, open:false, seen:new Set() };

function openChatSheet(){
  const s = document.getElementById('txd-chat-sheet');
  if (!s) return;
  s.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden';
   document.body.classList.add('txd-chat-open');
  chat.open = true;
  startChatPoll();
}
function closeChatSheet(){
  const s = document.getElementById('txd-chat-sheet');
  if (!s) return;
  s.setAttribute('aria-hidden','true');
  document.body.style.overflow = '';
  document.body.classList.remove('txd-chat-open');
  chat.open = false;
  stopChatPoll();
}


if (!window.__txdChatBound){
  document.querySelector('#txd-chat-sheet .txp-sheet-backdrop')?.addEventListener('click', closeChatSheet);
  document.getElementById('txd-chat')?.addEventListener('click', openChatSheet);
  document.getElementById('txd-chat-send')?.addEventListener('click', sendChat);
  document.getElementById('txd-chat-text')?.addEventListener('keydown', (e)=>{
    if (e.key === 'Enter'){ e.preventDefault(); sendChat(); }
  });
  window.__txdChatBound = true;
}


function renderMsgs(list){
  const box = document.getElementById('txd-chat-list');
  if (!box) return;

  const arr = Array.isArray(list) ? list : [];
  let maxSeen = chat.lastId;

  for (const m of arr){
    const mid = Number.isFinite(+m.id) ? +m.id : null;

   
    if (mid !== null) {
      if (chat.seen.has(mid)) continue;
      
      if (mid <= chat.lastId) { chat.seen.add(mid); continue; }
    }

    const role = (m.remitente_rol || m.from || m.role || m.sender || '').toString().toLowerCase();
    const isSystem = (m.tipo === 'system' || role === 'system');
    const mine = !isSystem && role.includes('conductor'); 
    const msg  = m.mensaje ?? m.text ?? m.body ?? '';

    const wrap = document.createElement('div');
    wrap.style.display = 'flex';
    wrap.style.margin = '6px 0';
    wrap.style.justifyContent = isSystem ? 'center' : (mine ? 'flex-end' : 'flex-start');

    const bub = document.createElement('div');
    bub.style.maxWidth = isSystem ? '92%' : '78%';
    bub.style.padding = '8px 10px';
    bub.style.borderRadius = '12px';
    bub.style.whiteSpace = 'pre-wrap';
    bub.style.wordBreak = 'break-word';
    if (isSystem) {
      bub.style.background = 'rgba(255,209,102,.12)';
      bub.style.color = '#fde68a';
      bub.style.textAlign = 'center';
      bub.style.fontSize = '13px';
      bub.style.border = '1px dashed rgba(255,209,102,.35)';
    } else {
      bub.style.background = mine ? '#ffd166' : '#1e293b';
      bub.style.color = mine ? '#1a1a1a' : '#e5e7eb';
    }
    bub.textContent = msg;

    wrap.appendChild(bub);
    box.appendChild(wrap);

    if (mid !== null){
      chat.seen.add(mid);
      if (mid > maxSeen) maxSeen = mid;
    }
  }

  
  if (maxSeen > chat.lastId) chat.lastId = maxSeen;

 
  box.scrollTop = box.scrollHeight;
}



async function fetchChatOnce(){
  const id = window.currentViajeId;
  if (!id) return;

  const url = `${VIAJE_CHAT_LIST_BASE}/${id}/chat-driver` + (chat.lastId ? `?since_id=${chat.lastId}` : '');
  try{
    const r = await fetch(url, { cache:'no-store', headers:{ 'Accept':'application/json' }});
    if (!r.ok){
      if (r.status === 403) showBanner?.('Sin permisos para leer el chat', 'fa-lock');
      return;
    }
    const j = await r.json();
    const arr = j?.items ?? j?.messages ?? j?.data ?? [];
    if (arr.length) renderMsgs(arr);

   
    if (chat.lastId){
      fetch(VIAJE_CHAT_READ_URL, {
        method:'POST',
        headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify({ viaje_id: id, max_id: chat.lastId })
      }).catch(()=>{});
    }
  }catch(e){  }
}
function startChatPoll(){ stopChatPoll(); fetchChatOnce(); chat.poller = setInterval(fetchChatOnce, chat.everyMs); }
function stopChatPoll(){ if (chat.poller){ clearInterval(chat.poller); chat.poller = null; } }


let sendingChat = false;
async function sendChat(){
  if (sendingChat) return;
  const id  = window.currentViajeId;
  const inp = document.getElementById('txd-chat-text');
  if (!id || !inp) return;
  const txt = (inp.value || '').trim();
  if (!txt) return;

  sendingChat = true;
  const btn = document.getElementById('txd-chat-send');
  btn && (btn.disabled = true);

  try{
    const r = await fetch(VIAJE_CHAT_SEND_URL, {
      method: 'POST',
      headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({ viaje_id: id, mensaje: txt, tipo:'text' })
    });
    if (!r.ok){
      showBanner?.('No se pudo enviar el mensaje', 'fa-triangle-exclamation');
    }else{
      inp.value = '';
     
      fetchChatOnce();
    }
  }catch(e){
    showBanner?.('No se pudo enviar el mensaje', 'fa-triangle-exclamation');
  }finally{
    sendingChat = false;
    btn && (btn.disabled = false);
    inp.focus();
  }
}


(function watchCurrentViajeIdForComm(){
  let _val = window.currentViajeId || null;
  Object.defineProperty(window, 'currentViajeId', {
    configurable: true,
    get(){ return _val; },
    set(v){
      _val = v;
      refreshNavAndCommRows?.();
      if (!v) closeChatSheet();
    }
  });
})();
</script>

<script>
(function () {
  const C = window.Capacitor;
  const isNative = !!(C && C.isNativePlatform && C.isNativePlatform());
  console.log('[Capacitor] isNative?', isNative, 'platform:', C?.getPlatform?.());
  if (isNative) {
    
    try { navigator.vibrate?.(20); } catch(e){}
  }
})();
</script>

<script type="module">
  
  import { Device } from "https://cdn.skypack.dev/@capacitor/device";
  import { PushNotifications } from "https://cdn.skypack.dev/@capacitor/push-notifications";
  import { FirebaseMessaging } from "https://cdn.skypack.dev/@capacitor-firebase/messaging";

  const isNative = !!(window.Capacitor?.isNativePlatform?.() === true);
 
  const PUSH_REGISTER_URL = "{{ route('push.register') }}";

 
const SCOPE = "{{ config('services.fcm.scope', env('FCM_SCOPE', 'dev')) }}";

  async function registrarTokenEnBackend({ token }) {
    if (!token) return;
    try {
     
      const { identifier: device_uuid } = await Device.getId();
      const info = await Device.getInfo();
      const plataforma = (info.platform === 'android' ? 'android'
                        : info.platform === 'ios' ? 'ios' : 'web');
      const is_emulator = !!info.isVirtual;

      await fetch(PUSH_REGISTER_URL, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || ""
        },
        body: JSON.stringify({
         
          provider: "fcm",
          token,
          device_uuid,
          plataforma,
          scope: SCOPE,
          is_emulator
        })
      });
      console.log("[FCM] Token enviado al backend");
    } catch (e) {
      console.warn("[FCM] Fallo registrando token en backend", e);
    }
  }

  async function initPush() {
    try {
      
      const perm = await PushNotifications.requestPermissions();
      if (perm.receive !== "granted") return;

     
      await PushNotifications.register();

     
      const { token } = await FirebaseMessaging.getToken();
      console.log("[FCM] token:", token);
      await registrarTokenEnBackend({ token });

      
      FirebaseMessaging.addListener("tokenChanged", async ({ token }) => {
        console.log("[FCM] tokenChanged:", token);
        await registrarTokenEnBackend({ token });
      });

      
      PushNotifications.addListener("pushNotificationReceived", (n) => {
  console.log("[Push] foreground:", n);
  const t = n?.data?.t || n?.data?.type || n?.data?.evento;
  if (t === 'offer') {
    playOfferSound();
  }
});
      PushNotifications.addListener("pushNotificationActionPerformed", (a) => {
        console.log("[Push] tapped:", a);
      });
    } catch (e) {
      console.warn("[Push] init error", e);
    }
  }

  if (isNative) initPush();
  else {
    console.log('[Push] omitido en web');
  }

  function handlePushData(data = {}) {
    const t   = (data.t || data.type || '').toString();
    const vId = data.viaje_id ? +data.viaje_id : null;

    try { navigator.vibrate?.(20); } catch(e){}

    if (t === 'offer') {
      if (typeof playOfferSound === 'function') playOfferSound();
      if (typeof drvSheet === 'function') drvSheet(true);
      if (data.o_lat && data.o_lng) {
        window.__viajeTargets = window.__viajeTargets || {};
        window.__viajeTargets.o = { lat: +data.o_lat, lng: +data.o_lng, txt: 'Solicitud cerca' };
      }
      if (typeof showBanner === 'function') showBanner('Nueva solicitud cerca', 'fa-taxi');
      return;
    }

    if (t === 'chat') {
      if (vId) window.currentViajeId = window.currentViajeId || vId;
      if (typeof showBanner === 'function') showBanner('Nuevo mensaje del pasajero', 'fa-comments');
      if (typeof openChatSheet === 'function') openChatSheet?.();
      const chatBtn = document.getElementById('txd-chat');
      if (chatBtn) chatBtn.dataset.badge = '1';
      return;
    }
  }

  if (isNative) {
    PushNotifications.addListener("pushNotificationReceived", (n) => {
      handlePushData(n?.data || {});
    });

    PushNotifications.addListener("pushNotificationActionPerformed", (a) => {
      const data = a?.notification?.data || a?.notification?.extra || {};
      handlePushData(data || {});
    });
  }

document.getElementById('txd-chat')?.addEventListener('click', ()=>{
  const chatBtn = document.getElementById('txd-chat');
  if (chatBtn) delete chatBtn.dataset.badge;
});

import { LocalNotifications } from "https://cdn.skypack.dev/@capacitor/local-notifications";

async function ensureAndroidChannel(){
  if (!(window.Capacitor?.getPlatform?.() === 'android')) return;
  try{
    await LocalNotifications.createChannel({
      id: 'taxpiya-high',
      name: 'High Priority',
      description: 'Alertas de ofertas y chat',
      importance: 5,    
      sound: 'default',
      visibility: 1,    
      lights: true,
      vibration: true
    });
  }catch(e){ console.warn('[NotifChannel]', e); }
}
ensureAndroidChannel();

</script>

<script>

let bgWatcherId = null;

async function startBgWatcher(){
  const isNative = !!(window.Capacitor?.isNativePlatform?.());
  const BG = window.Capacitor?.Plugins?.BackgroundGeolocation;
  if (!isNative || !BG || bgWatcherId) return;

  try {
 
    const perm = await BG.requestPermissions();
    
    if (perm !== 'granted' && perm?.location !== 'granted') {
      try { await BG.openSettings(); } catch(e){}
    }

    bgWatcherId = await BG.addWatcher({
      backgroundMessage: "Estamos ubicándote para asignarte viajes",
      backgroundTitle: "Taxpiya activo",
      requestPermissions: true,
      stale: false,
      distanceFilter: 20,       
      stationaryRadius: 25,    
      stopOnTerminate: false,
      startOnBoot: true
    }, async (location, error) => {
      if (error) {
        
        if (error.code === 'NOT_AUTHORIZED') {
          try { await BG.openSettings(); } catch(e){}
        }
        return;
      }
      if (!location) return;

      const lat = location.latitude;
      const lng = location.longitude;
      const accuracy = location.accuracy ?? null;
      const heading = location.bearing ?? null;
      const speedMs = location.speed ?? null;

      if (accuracy != null) showAccuracy(accuracy);
      putDriverMarker(lat, lng);
	  centerMapOnce(lat, lng);                
window.__lastDriverPos = { lat, lng };   

    
      const now = Date.now();
      if (isOnline && (now - lastSendTs) > 3000) {
        lastSendTs = now;
        try{
          await fetch(CONDUCTOR_POSICION_URL, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({
              lat, lng,
              heading: (heading != null && !isNaN(heading)) ? heading : null,
              velocidad_kmh: (speedMs != null && !isNaN(speedMs)) ? Math.max(0, Math.round(speedMs * 3.6)) : null
            })
          });
        }catch(e){}
      }
    });
    window.__txpConductorBgActive = true;
    window.TxpBackground?.syncConductor?.();
  } catch(e) {
    console.warn('[BG] start error', e);
  }
}

async function stopBgWatcher(){
  const BG = window.Capacitor?.Plugins?.BackgroundGeolocation;
  if (bgWatcherId && BG?.removeWatcher) {
    try { await BG.removeWatcher({ id: bgWatcherId }); } catch(e){}
  }
  bgWatcherId = null;
  window.__txpConductorBgActive = false;
}
</script>

<script>

const isNative = !!(window.Capacitor?.isNativePlatform?.() === true);

/* No marcar offline al minimizar (pagehide en Android). Solo al cerrar pestaña en web. */
if (!isNative) {
  function markDriverOfflineOnLeave(){
    try {
      if (!window.isOnline || !CONDUCTOR_DISPONIBLE_URL) return;
      fetch(CONDUCTOR_DISPONIBLE_URL, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify({ disponible: false }),
        keepalive: true
      }).catch(()=>{});
      window.isOnline = false;
    } catch(e){}
  }
  window.addEventListener('beforeunload', markDriverOfflineOnLeave);
}

if (isNative && window.TxpBackground) {
  document.addEventListener('DOMContentLoaded', () => TxpBackground.init('conductor'), { once: true });
}
</script>

<script>
function playOfferSound(){
  // Reproducir mp3 si está disponible
  const el = document.getElementById('snd-offer');
  if (el) { try { el.currentTime = 0; el.play(); } catch (e) {} }

  // Tono de alerta digital limpio (bip doble) con Web Audio API
  try {
    const Ctx = window.AudioContext || window.webkitAudioContext;
    if (!Ctx) return;
    const ctx = new Ctx();
    function bip(freq, t0, dur) {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain); gain.connect(ctx.destination);
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, t0);
      gain.gain.setValueAtTime(0.22, t0);
      gain.gain.exponentialRampToValueAtTime(0.001, t0 + dur);
      osc.start(t0); osc.stop(t0 + dur);
    }
    const t = ctx.currentTime;
    bip(987.77, t,       0.13); // Si5
    bip(1318.51, t+0.16, 0.13); // Mi6
  } catch (err) {}
}
</script>

<script>

const BG_CONSENT_KEY = 'txd.bgLocationConsent.v1';

function showBgConsentIfNeeded(){

  const isNative = !!(window.Capacitor?.isNativePlatform?.() === true);
  if (!isNative) return;
  if (localStorage.getItem(BG_CONSENT_KEY) === '1') return;
  document.getElementById('bg-consent')?.setAttribute('aria-hidden', 'false');
}

function acceptBgConsent(){
  localStorage.setItem(BG_CONSENT_KEY, '1');
  document.getElementById('bg-consent')?.setAttribute('aria-hidden', 'true');
}

document.getElementById('bgc-accept')?.addEventListener('click', acceptBgConsent);
document.addEventListener('DOMContentLoaded', showBgConsentIfNeeded);


window.requireBgConsent = (fn) => {
  if (localStorage.getItem(BG_CONSENT_KEY) === '1') { fn?.(); return; }
  const el = document.getElementById('bg-consent');
  if (el) el.setAttribute('aria-hidden','false');
  const once = () => { document.getElementById('bgc-accept')?.removeEventListener('click', once); fn?.(); };
  document.getElementById('bgc-accept')?.addEventListener('click', once);
};
</script>

@include('components.osm-maps-script', ['callback' => 'initMap'])
@include('components.firebase-auth')
@include('components.firebase-session-guard', ['firebaseApp' => 'conductor'])
@endsection
