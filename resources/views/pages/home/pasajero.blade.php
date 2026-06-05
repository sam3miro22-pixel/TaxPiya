@inject('comp_model', 'App\Models\ComponentsData')
@php
    $pageTitle = "Pasajero";
@endphp

@extends($layout)
@section('title', $pageTitle)

@section('content')
<div id="txp-map-root">
   
    <div id="map" class="map-canvas"></div>

   
    <div class="top-controls">
        
        <div class="d-flex align-items-center justify-content-center position-relative">
            <button type="button"
                    class="navbar-toggler dropdown-toggle position-absolute start-0 top-50 translate-middle-y ms-2"
                    data-bs-toggle="collapse" data-bs-target=".navbar-responsive-collapse" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="brand-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Taxpiya"
                     class="brand-logo" width="82" height="82" style="width:82px;height:82px;">
            </div>
        </div>

        
        <div class="search-wrap stack shadow-xl">
         
            <div class="search-row">
                <i class="fa-solid fa-circle-dot search-icon origin"></i>
                <input id="origin-input" type="text" class="form-control search-input"
                       placeholder="¿Dónde estás? (toca para editar)">
                <div class="btns-inline">
                    <button id="pin-origin" class="btn mic-btn pin-btn" type="button" title="Elegir origen en el mapa">
                        <i class="fa-solid fa-location-pin"></i>
                    </button>
                    <button id="voice-origin" class="btn mic-btn" type="button" aria-label="Voz origen" title="Voz origen">
                        <i class="fa-solid fa-microphone"></i>
                    </button>
                </div>
            </div>
            <div class="divider"></div>
          
            <div class="search-row">
                <i class="fa-solid fa-location-dot search-icon dest"></i>
                <input id="dest-input" type="text" class="form-control search-input"
                       placeholder="¿A dónde vamos? Busca una dirección">
                <div class="btns-inline">
                    <button id="pin-dest" class="btn mic-btn pin-btn" type="button" title="Elegir destino en el mapa">
                        <i class="fa-solid fa-location-pin"></i>
                    </button>
                    <button id="voice-dest" class="btn mic-btn" type="button" aria-label="Voz destino" title="Voz destino">
                        <i class="fa-solid fa-microphone"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <button id="recenter-btn" class="fab" type="button" title="Mi ubicación">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    
  <div class="bottom-cta">
  <button id="solicitar-btn" class="btn btn-brand btn-xxl" style="display:none;">
    <i class="fa-solid fa-taxi me-2"></i> ¡Solicitar Viaje!
  </button>
</div>



<div id="txp-sheet-viaje" class="txp-sheet" aria-hidden="true">
  <div class="txp-sheet-backdrop"></div>
  <div class="txp-sheet-dialog" role="dialog" aria-modal="true" aria-labelledby="txpSheetTitulo">
    <div class="txp-sheet-handle"></div>

    <h6 id="txpSheetTitulo" class="m-0 mb-2 fw-bold">Confirmar solicitud</h6>

    <div class="txp-resumen">
      <div class="txp-par">
        <span class="txp-dot origen"></span>
        <div class="txp-col">
          <div class="txp-lab">Origen</div>
          <div id="txp-origen-txt" class="txp-val">—</div>
        </div>
      </div>
      <div class="txp-par">
        <span class="txp-dot destino"></span>
        <div class="txp-col">
          <div class="txp-lab">Destino</div>
          <div id="txp-destino-txt" class="txp-val">—</div>
        </div>
      </div>
      <div class="txp-meta">
        <span id="txp-dist">—</span> · <span id="txp-dura">—</span>
      </div>
    </div>

    <div class="txp-card txp-ride">
      <div class="txp-ride-left">
        <span class="txp-ride-icon"><i class="fa-solid fa-taxi"></i></span>
        <div>
          <div class="txp-ride-title">Taxi — Tarifa fija</div>
          <div class="txp-ride-sub">Pago en efectivo</div>
        </div>
      </div>
      <div class="txp-ride-right">
        <div id="txp-tarifa" class="txp-precio">—</div>
      </div>
    </div>

    

    <div class="txp-acciones">
      <button id="txp-btn-confirmar" class="btn btn-brand btn-xxl w-100">
        <i class="fa-solid fa-check me-2"></i> Confirmar solicitud
      </button>
      <button class="btn btn-light w-100 mt-2" type="button" data-close>Cerrar</button>
    </div>
  </div>
</div>


   
    <div id="geo-accuracy" class="geo-badge" style="display:none;"></div>

<div class="quick-menu" id="quickMenu">
  <button class="qm-toggle" id="qmToggle" aria-expanded="false" aria-label="Abrir menú">
    <i class="fa-solid fa-bars"></i>
  </button>

  <nav class="qm-items">
    <a href="#" class="qm-item" style="--i:1" aria-label="Tu Perfil">
      <i class="fa-solid fa-user"></i>
      <span class="tip">Tu Perfil</span>
    </a>

    <a href="#" class="qm-item" style="--i:2" aria-label="Tus Viajes">
      <i class="fa-solid fa-route"></i>
      <span class="tip">Tus Viajes</span>
    </a>

    <a href="#" class="qm-item" style="--i:3" aria-label="Tus Direcciones">
      <i class="fa-solid fa-location-dot"></i>
      <span class="tip">Tus Direcciones</span>
    </a>

    <a href="<?php print_link('auth/logout') ?>" class="qm-item" style="--i:4" aria-label="Cerrar sesión">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span class="tip">Cerrar sesión</span>
    </a>
  </nav>
</div>


<div id="txp-searching" class="txp-searching" aria-hidden="true">
  <div class="txp-searching-box">
    <div class="txp-radar">
      <span class="ring ring-1"></span>
      <span class="ring ring-2"></span>
      <span class="ring ring-3"></span>
      <span class="dot"></span>
    </div>

    <div class="txp-searching-label">
      <i class="fa-solid fa-location-dot me-2"></i>
      Buscando conductores disponibles...
    </div>

    <button id="txp-cancelar-busqueda" class="txp-btn-cancel" type="button">
      <i class="fa-solid fa-xmark"></i> Cancelar servicio
    </button>
  </div>
</div>


<div id="txp-sheet-asignado" class="txp-sheet" aria-hidden="true">
  <div class="txp-sheet-backdrop"></div>
  <div class="txp-sheet-dialog" role="dialog" aria-modal="true" aria-labelledby="txpAsignadoTitulo">
    <div class="txp-sheet-handle"></div>
    <h6 id="txpAsignadoTitulo" class="m-0 mb-2 fw-bold">Conductor asignado</h6>

    <div class="txp-card txp-ride" id="txp-asignado-card">
      <div class="txp-ride-left">
        <span class="txp-ride-icon"><i class="fa-solid fa-taxi"></i></span>
        <div>
          <div class="txp-ride-title" id="txp-asignado-nombre">—</div>
          <div class="txp-ride-sub" id="txp-asignado-vehiculo">—</div>
          <div class="txp-ride-sub" id="txp-asignado-placa">—</div>
        </div>
      </div>
      <div class="txp-ride-right">
        <div class="txp-precio" id="txp-asignado-eta">ETA —</div>
        <div class="txp-ride-sub" id="txp-asignado-dist">—</div>
      </div>
    </div>

    <div class="txp-acciones">
      <button id="txp-cancelar-asignado" class="btn btn-light w-100">
        <i class="fa-solid fa-xmark me-1"></i> Cancelar servicio
      </button>
    </div>
  </div>
</div>


<div id="txp-banner" class="txp-banner" aria-hidden="true">
  <i id="txp-banner-ico" class="fa-solid fa-circle-info me-2"></i>
  <span id="txp-banner-txt">Estado</span>
</div>


<div id="txp-sheet-final" class="txp-sheet" aria-hidden="true">
  <div class="txp-sheet__backdrop" data-close></div>
  <div class="txp-sheet__body">
    <div class="txp-sheet__grab"></div>
    <div class="txp-final__head">
      <i class="fa-solid fa-flag-checkered"></i> Viaje finalizado
    </div>
    <div class="txp-final__monto">
      <div class="txp-final__label">Total a pagar (efectivo)</div>
      <div class="txp-final__valor" id="txp-final-monto">—</div>
    </div>
    <button id="txp-final-ok" class="btn btn-brand w-100 mt-3">
      Entendido
    </button>
  </div>
</div>


</div>

@endsection

@section('pagecss')
<style>
/* Ocultar navegación para pantalla completa */
.navbar, .topbar, .footer, #sidebar { display: none !important; }

:root{
    --txp-bg-1:#0b132b; --txp-bg-2:#1c2541; --txp-bg-3:#3a506b;
    --txp-brand:#ffd166; --txp-brand-2:#ff9f1c; --txp-white:#fff;
}

#txp-map-root{ position:fixed; inset:0; background:#000; overflow:hidden; z-index:1; }
.map-canvas{ position:absolute; inset:0; }

.top-controls{
    position:absolute; left:0; right:0; top:0;
    display:flex; flex-direction:column; align-items:center; gap:10px;
    padding:16px 12px 8px; z-index:3;
    background: linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,0));
}
.brand-wrap{ display:flex; justify-content:center; }
.brand-logo{
    width:64px; height:64px; object-fit:contain;
    filter: drop-shadow(0 0 10px rgba(255,209,102,.5));
}


.search-wrap.stack{
    display:flex; flex-direction:column; gap:6px;
    width:min(920px, 92vw);
    background: rgba(255,255,255,.95);
    border-radius: 16px;
    padding: 8px 10px;
    border:1px solid rgba(0,0,0,.08);
}
.search-row{ display:flex; align-items:center; gap:8px; padding: 6px 6px; }
.btns-inline{ display:flex; gap:6px; }
.divider{ height:1px; background: rgba(0,0,0,.08); margin: 0 6px; }

.search-icon{ color:#111; opacity:.7; margin-left:6px; }
.search-icon.origin{ color:#22c55e; }
.search-icon.dest{ color:#ef4444; }

.search-input{
    border:none; background:transparent; outline:none; box-shadow:none;
    color:#111; font-size:16px; padding-left:2px; flex:1;
}
.search-input::placeholder{ color:#6b7280; }

.mic-btn{
    border:0; border-radius:999px; padding:10px 12px;
    background: linear-gradient(180deg, rgba(255,209,102,.32), rgba(255,159,28,.28));
    color:#111;
}
.mic-btn:hover{ filter:brightness(1.03); }
.pin-btn.active{ outline:2px solid #ffb100; }


.fab{
    position:absolute; right:16px; bottom:110px; z-index:3;
    width:52px; height:52px; border-radius:50%;
    border:0; background:rgba(255,255,255,.95); color:#111;
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 10px 30px rgba(0,0,0,.35);
}


.bottom-cta{
    position:absolute; left:0; right:0; bottom:0; z-index:3;
    display:flex; justify-content:center;
    padding: 18px 16px 28px;
    background: linear-gradient(0deg, rgba(0,0,0,.65), rgba(0,0,0,0));
}
.btn-brand{
    background: linear-gradient(180deg, var(--txp-brand), var(--txp-brand-2));
    color:#1b1b1b; font-weight:800; border:none; border-radius: 16px;
    box-shadow: 0 20px 45px rgba(255,159,28,.35), inset 0 2px 0 rgba(255,255,255,.35);
    text-transform: uppercase; letter-spacing:.3px;
}
.btn-brand:hover{ filter:brightness(1.03); transform: translateY(-1px); }
.btn-xxl{ padding:16px 26px; font-size:18px; }


.pulse-marker{
    position:absolute; transform:translate(-50%, -50%);
    width:16px; height:16px; border-radius:50%;
    background:#2dd4bf; border:2px solid #fff;
    box-shadow:0 0 0 rgba(45,212,191,0.7);
    animation:pulse 2s infinite;
}
@keyframes pulse{
    0%{ box-shadow:0 0 0 0 rgba(45,212,191,0.6); }
    70%{ box-shadow:0 0 0 20px rgba(45,212,191,0); }
    100%{ box-shadow:0 0 0 0 rgba(45,212,191,0); }
}


.geo-badge{
  position: absolute; left: 16px; bottom: 28px; z-index: 3;
  background: rgba(0,0,0,.55); color: #fff; backdrop-filter: blur(6px);
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 10px; padding: 6px 10px; font-size: 12px;
}

.shadow-xl{ box-shadow: 0 25px 70px rgba(0,0,0,.45) !important; }


.bottom-cta{
  bottom: 72px;               /* o calc(72px + env(safe-area-inset-bottom,0)) */
  padding-bottom: 0;
  background: none !important;
  z-index: 3;                 /* por encima del fade */
}


#txp-map-root::after{
  content: "";
  position: fixed;
  left: 0; right: 0; bottom: 0;
  height: 90px;               /* alto del degradado */
  pointer-events: none;
  background: linear-gradient(0deg, rgba(0,0,0,.65), rgba(0,0,0,0));
  z-index: 2;                 /* debajo del CTA, encima del mapa */
}


.geo-badge{ z-index: 3; }


:root{
  --txp-brand: #ffd166;
  --txp-brand-2: #ff9f1c;
  --qm-gap: 64px;           /* separación vertical entre botones */
}

.quick-menu{
  position: absolute;
  right: 16px;              /* margen con el borde */
  bottom: 22px;
  z-index: 4;
}


.qm-toggle{
  width: 54px; height: 54px; border: 0; border-radius: 50%;
  display: grid; place-items: center; cursor: pointer;
  background: linear-gradient(180deg, var(--txp-brand), var(--txp-brand-2));
  color: #1a1a1a; font-size: 18px;
  box-shadow: 0 12px 30px rgba(255,159,28,.35), inset 0 2px 0 rgba(255,255,255,.35);
  transition: transform .15s ease, filter .15s ease;
}
.qm-toggle:hover{ filter: brightness(1.03); transform: translateY(-1px); }


.qm-items{
  position: relative;
  width: 0; height: 0;
  pointer-events: none;
}


.qm-item{
  position: absolute; right: 0; bottom: 0;
  width: 50px; height: 50px; border-radius: 50%;
  display: grid; place-items: center;
  background: linear-gradient(180deg, var(--txp-brand), var(--txp-brand-2));
  color: #000; 
  text-decoration: none;
  box-shadow: 0 10px 24px rgba(255,159,28,.28), inset 0 2px 0 rgba(255,255,255,.28);
  transform: translateY(0) scale(.92);
  opacity: 0; pointer-events: none;
  transition: transform .28s cubic-bezier(.2,.7,.2,1), opacity .28s ease;
  transition-delay: calc(var(--i,1) * 20ms);
}


.quick-menu.open .qm-items{ pointer-events: auto; }
.quick-menu.open .qm-item{
  opacity: 1; pointer-events: auto;
  transform: translateY(calc(-1 * var(--i,1) * var(--qm-gap))) scale(1);
}


.qm-item .tip{
  position: absolute;
  right: calc(100% + 10px);
  top: 50%; transform: translateY(-50%) scale(.96);
  background: rgba(0,0,0,.9);
  color: #fff;
  padding: 6px 8px;
  font-size: 12px;          /* letra pequeña */
  line-height: 1;
  border-radius: 8px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity .18s ease, transform .18s ease;
  box-shadow: 0 8px 18px rgba(0,0,0,.35);
}
.qm-item .tip::after{
  content: ""; position: absolute; left: 100%; top: 50%;
  transform: translateY(-50%);
  border: 6px solid transparent;
  border-left-color: rgba(0,0,0,.9);
}


.quick-menu.open .qm-item:hover .tip,
.quick-menu.open .qm-item:focus .tip{
  opacity: 1; transform: translateY(-50%) scale(1);
}


@media (max-width: 480px){
  .quick-menu{ right: 12px; bottom: 18px; }
  :root{ --qm-gap: 58px; }
  .qm-item .tip{ font-size: 11px; }
}


.txp-sheet{position:fixed;inset:0;display:none;z-index:1000}
.txp-sheet[aria-hidden="false"]{display:block}
.txp-sheet-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(2px)}
.txp-sheet-dialog{position:absolute;left:0;right:0;bottom:0;background:#fff;border-radius:18px 18px 0 0;
  box-shadow:0 -12px 40px rgba(0,0,0,.25);padding:14px 14px 16px;max-height:86vh;overflow:auto}
.txp-sheet-handle{width:46px;height:5px;border-radius:99px;background:#e5e7eb;margin:6px auto 12px}
.txp-resumen{background:#f8fafc;border-radius:14px;padding:10px 12px;margin-bottom:10px}
.txp-par{display:flex;gap:10px;align-items:center;margin-bottom:6px}
.txp-dot{width:10px;height:10px;border-radius:50%}
.txp-dot.origen{background:#10b981}
.txp-dot.destino{background:#ff9f1c}
.txp-col .txp-lab{font-size:.78rem;color:#64748b}
.txp-col .txp-val{font-weight:600;color:#0f172a}
.txp-meta{margin-top:4px;color:#334155;font-size:.86rem;font-weight:600}
.txp-card{border:1px solid #e5e7eb;border-radius:14px;padding:12px 14px;background:#fff;margin-bottom:10px}
.txp-ride{display:flex;align-items:center;justify-content:space-between}
.txp-ride-left{display:flex;gap:10px;align-items:center}
.txp-ride-icon{width:38px;height:38px;border-radius:10px;background:#fff7ed;display:inline-grid;place-items:center;border:1px solid #fde68a}
.txp-ride-title{font-weight:700}
.txp-ride-sub{font-size:.86rem;color:#6b7280}
.txp-precio{font-weight:800;font-size:1.1rem}
@media (prefers-color-scheme: dark){
  .txp-sheet-dialog{background:#0b0f19;color:#e5e7eb}
  .txp-sheet-handle{background:#1f2937}
  .txp-resumen{background:#0f172a}
  .txp-col .txp-val{color:#e5e7eb}
  .txp-meta{color:#cbd5e1}
  .txp-card{background:#0b0f19;border-color:#1f2937}
  .txp-ride-icon{background:#1f2937;border-color:#374151}
  .txp-ride-sub{color:#94a3b8}
}


.txp-searching{
  position:fixed; inset:0; z-index:1100; display:none;
  background:rgba(8,12,22,.45); backdrop-filter: blur(2px);
  align-items:center; justify-content:center;
}
.txp-searching[aria-hidden="false"]{ display:flex; }

.txp-searching-box{
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  padding:18px 20px; background:rgba(15,23,42,.75); border:1px solid rgba(255,209,102,.25);
  border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.35);
}

.txp-searching-label{
  color:#fff; font-weight:700; letter-spacing:.2px; margin-top:10px;
  text-shadow:0 1px 2px rgba(0,0,0,.35);
}


.txp-radar{ position:relative; width:140px; height:140px; }
.txp-radar .dot{
  position:absolute; left:50%; top:50%; width:10px; height:10px; margin:-5px 0 0 -5px;
  background:#ffd166; border-radius:50%; box-shadow:0 0 12px rgba(255,209,102,.9), 0 0 24px rgba(255,209,102,.45);
}
.txp-radar .ring{
  position:absolute; left:50%; top:50%; border:2px solid rgba(255,209,102,.85);
  border-radius:50%; transform:translate(-50%,-50%) scale(.25); opacity:.9;
  box-shadow:0 0 22px rgba(255,209,102,.45), inset 0 0 18px rgba(255,209,102,.25);
  animation:txpPulse 2.4s cubic-bezier(.22,.61,.36,1) infinite;
}
.txp-radar .ring-1{ width:40px;  height:40px;  animation-delay:0s;    }
.txp-radar .ring-2{ width:80px;  height:80px;  animation-delay:.45s; }
.txp-radar .ring-3{ width:120px; height:120px; animation-delay:.9s;  }

@keyframes txpPulse{
  0%   { transform:translate(-50%,-50%) scale(.25); opacity:.95; border-color:rgba(255,209,102,.95); }
  70%  { opacity:.30; }
  100% { transform:translate(-50%,-50%) scale(1.05); opacity:0; border-color:rgba(255,209,102,0); }
}


@media (prefers-color-scheme: light){
  .txp-searching-box{ background:rgba(255,255,255,.88); border-color:rgba(255,209,102,.35); }
  .txp-searching-label{ color:#0f172a; text-shadow:none; }
}


.txp-btn-cancel{
  appearance:none; border:none; cursor:pointer;
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 16px; border-radius:9999px; font-weight:800; letter-spacing:.2px;
  color:#fff; background:rgba(255,255,255,.06);
  border:1.5px solid rgba(255,209,102,.65);
  box-shadow:
    inset 0 0 0 1px rgba(255,209,102,.25),
    0 8px 20px rgba(0,0,0,.35);
  backdrop-filter: blur(4px);
  transition: background .2s ease, box-shadow .2s ease, transform .08s ease;
}
.txp-btn-cancel:hover{
  background:rgba(255,209,102,.14);
  box-shadow:
    inset 0 0 0 1px rgba(255,209,102,.35),
    0 12px 28px rgba(0,0,0,.45);
}
.txp-btn-cancel:active{ transform:translateY(1px) scale(.98); }
.txp-btn-cancel:focus-visible{
  outline:none;
  box-shadow:
    0 0 0 3px rgba(255,209,102,.35),
    0 10px 24px rgba(0,0,0,.45);
}

@media (prefers-color-scheme: light){
  .txp-btn-cancel{
    color:#0f172a;
    background:rgba(255,255,255,.9);
    border-color:rgba(255,209,102,.8);
    box-shadow:0 6px 16px rgba(0,0,0,.12);
  }
  .txp-btn-cancel:hover{ background:rgba(255,209,102,.18); }
}


#txp-sheet-asignado .txp-ride-sub{ font-size:.86rem; color:#6b7280 }
@media (prefers-color-scheme: dark){
  #txp-sheet-asignado .txp-ride-sub{ color:#94a3b8 }
}

.txp-banner{
  position: fixed; left: 50%; transform: translateX(-50%);
  bottom: 88px; background: rgba(17,24,39,.92); color:#fff;
  padding: 10px 14px; border-radius: 12px; font-size:14px;
  display: flex; align-items: center; gap: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,.25);
  transition: opacity .25s, transform .25s;
  opacity:0; pointer-events:none; z-index: 1000;
}
.txp-banner.show{ opacity:1; pointer-events:auto; transform: translate(-50%,0); }

.txp-final__head{
  font-weight: 700; font-size: 18px; color: #fff; display:flex; gap:8px; align-items:center;
}
.txp-final__head i{ color:#10b981; }
.txp-final__monto{ margin-top: 10px; background:#0f172a; border-radius:14px; padding:12px 14px; }
.txp-final__label{ color:#cbd5e1; font-size:13px; }
.txp-final__valor{ font-size:22px; font-weight:800; color:#f8fafc; }

#txp-sheet-asignado .txp-sheet-backdrop{
  background: transparent !important;
  backdrop-filter: none !important;
  pointer-events: none !important; 
}


#txp-sheet-asignado{ 
  pointer-events: none;            
}
#txp-sheet-asignado .txp-sheet-dialog{
  pointer-events: auto;             
}


#txp-sheet-asignado .txp-sheet-backdrop{
  background: transparent !important;
  backdrop-filter: none !important;
  pointer-events: none !important;  
}


#txp-map-root, .map-canvas, #map{
  touch-action: pan-x pan-y;        
}


#txp-sheet-final .txp-sheet__backdrop{
  position:absolute; inset:0;
  background:rgba(0,0,0,.35);
  backdrop-filter:blur(2px);
}

#txp-sheet-final .txp-sheet__body{
  position:absolute; left:0; right:0; bottom:0;
  background:#0b0f19;          
  color:#e5e7eb;
  border-radius:18px 18px 0 0;
  box-shadow:0 -12px 40px rgba(0,0,0,.25);
  padding:14px 14px 16px;
  max-height:86vh; overflow:auto;
}


#txp-sheet-final .txp-sheet__grab{
  width:46px;height:5px;border-radius:99px;
  background:#1f2937;margin:6px auto 12px;
}


#txp-sheet-final .txp-final__monto{
  margin-top:10px;background:#0f172a;border-radius:14px;padding:12px 14px;
}
#txp-sheet-final .txp-final__label{ color:#cbd5e1;font-size:13px; }
#txp-sheet-final .txp-final__valor{ font-size:22px;font-weight:800;color:#f8fafc; }


html, body{ background:#0b132b !important; color-scheme: dark; }


.txp-sheet-dialog,
#txp-sheet-final .txp-sheet__body{
  background:#0b0f19 !important; color:#e5e7eb !important;
  border-radius:18px 18px 0 0 !important;
  border:1px solid rgba(255,255,255,.06) !important;
}
.txp-sheet-handle,
#txp-sheet-final .txp-sheet__grab{ background:#1f2937 !important; }

.txp-resumen,
#txp-sheet-final .txp-final__monto{
  background:#0f172a !important; border:1px solid #1f2937 !important;
}
.txp-col .txp-val{ color:#e5e7eb !important; }
.txp-meta, .txp-final__label{ color:#cbd5e1 !important; }
.txp-final__valor{ color:#f8fafc !important; }


.txp-card{
  background:#0b0f19 !important; border-color:#1f2937 !important;
}
.txp-ride-icon{
  background:#1f2937 !important; border-color:#374151 !important;
}
.txp-ride-sub{ color:#94a3b8 !important; }


.txp-searching{ background:rgba(8,12,22,.45) !important; }
.txp-searching-box{
  background:rgba(15,23,42,.85) !important;
  border-color:rgba(255,209,102,.25) !important;
  color:#e5e7eb !important;
}


.txp-banner{
  background: rgba(17,24,39,.92) !important; color:#fff !important;
}


.txp-btn-cancel{
  color:#fff !important; background:rgba(255,255,255,.06) !important;
  border-color:rgba(255,209,102,.65) !important;
  box-shadow: inset 0 0 0 1px rgba(255,209,102,.25), 0 8px 20px rgba(0,0,0,.35) !important;
}
.txp-btn-cancel:hover{
  background:rgba(255,209,102,.14) !important;
  box-shadow: inset 0 0 0 1px rgba(255,209,102,.35), 0 12px 28px rgba(0,0,0,.45) !important;
}

@media (prefers-color-scheme: light){
  .txp-sheet-dialog,
  #txp-sheet-final .txp-sheet__body{ background:#0b0f19 !important; color:#e5e7eb !important; }
  .txp-sheet-handle,
  #txp-sheet-final .txp-sheet__grab{ background:#1f2937 !important; }
  .txp-resumen,
  #txp-sheet-final .txp-final__monto{ background:#0f172a !important; border-color:#1f2937 !important; }
  .txp-card{ background:#0b0f19 !important; border-color:#1f2937 !important; }
  .txp-ride-icon{ background:#1f2937 !important; border-color:#374151 !important; }
  .txp-ride-sub{ color:#94a3b8 !important; }
  .txp-searching-box{ background:rgba(15,23,42,.85) !important; color:#e5e7eb !important; }
  .txp-banner{ background: rgba(17,24,39,.92) !important; color:#fff !important; }
  .txp-btn-cancel{ color:#fff !important; background:rgba(255,255,255,.06) !important; }
}

</style>
@endsection

@section('pagejs')
<script>
function getCsrf(){
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}
</script>
<script>

const API_TARIFA_URL = "{{ route('tarifa.fija') }}";


const $sheet      = document.getElementById('txp-sheet-viaje');
const $origenTxt  = document.getElementById('txp-origen-txt');
const $destinoTxt = document.getElementById('txp-destino-txt');
const $dist       = document.getElementById('txp-dist');
const $dura       = document.getElementById('txp-dura');
const $tarifa     = document.getElementById('txp-tarifa');
const $confirmar  = document.getElementById('txp-btn-confirmar');

function mostrarSheet(){ $sheet.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
function ocultarSheet(){ $sheet.setAttribute('aria-hidden','true');  document.body.style.overflow=''; }
$sheet.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', ocultarSheet));

const fmtCOP = v => new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(v||0);


document.getElementById('solicitar-btn').addEventListener('click', async () => {

  $origenTxt.textContent  = document.getElementById('origin-input')?.value || 'Mi ubicación';
  $destinoTxt.textContent = document.getElementById('dest-input')?.value || 'Destino';

  
  try {
    if (originLatLng && destLatLng && directionsService) {
      const req = { origin: originLatLng, destination: destLatLng, travelMode: google.maps.TravelMode.DRIVING };
      const res = await new Promise((ok,ko)=> directionsService.route(req,(r,s)=> s==='OK'?ok(r):ko(s)));
      const leg = res.routes?.[0]?.legs?.[0];
      $dist.textContent = leg?.distance?.text || '—';
      $dura.textContent = leg?.duration?.text || '—';
    }
  } catch { $dist.textContent='—'; $dura.textContent='—'; }


  const params = new URLSearchParams({ categoria: 'taxi', ciudad: 'Medellín' });
  try{
    const r = await fetch(`${API_TARIFA_URL}?${params.toString()}`, { cache: 'no-store' });
    const j = await r.json();
    if (!j.ok) throw new Error(j.message || 'Sin tarifa');
    $tarifa.textContent = fmtCOP(j.monto);
    $confirmar.dataset.tarifa = j.monto; 
  }catch(e){
    console.warn('Tarifa no disponible', e);
    $tarifa.textContent = '—';
  }

  mostrarSheet();
});

</script>
<script>

function txpShowSearching(){
  const el = document.getElementById('txp-searching');
  el.setAttribute('aria-hidden','false');
}
function txpHideSearching(){
  const el = document.getElementById('txp-searching');
  el.setAttribute('aria-hidden','true');
}
</script>


<script>
const txpSearch = {
  active: false, found: false,
  intervalId: null, timeoutId: null,
  radius: 8, maxRadius: 15,
  pollMs: 3000, timeoutMs: 25000,
  phase: 'idle' 
};

function setPhase(phase){
  txpSearch.phase = phase;
  const btn = document.getElementById('txp-cancelar-busqueda');
  if (!btn) return;

  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Cancelar servicio';
}

function setSearchingLabel(txt, icon = 'fa-location-dot'){
  const lbl = document.querySelector('#txp-searching .txp-searching-label');
  if (lbl) lbl.innerHTML = `<i class="fa-solid ${icon} me-2"></i>${txt}`;
}

function startDriverSearch(){
  txpSearch.active = true; txpSearch.found = false; txpSearch.radius = 8;
  setPhase('buscando');
  setSearchingLabel('Buscando conductores disponibles…');
  txpShowSearching();
  pollOnce(); 
  txpSearch.intervalId = setInterval(() => { if (txpSearch.active) pollOnce(); }, txpSearch.pollMs);
  txpSearch.timeoutId  = setTimeout(() => {
    if (!txpSearch.found) {
      stopDriverSearch();
     
    }
  }, txpSearch.timeoutMs);
}

function stopDriverSearch(){
  txpSearch.active = false;
  if (txpSearch.intervalId) { clearInterval(txpSearch.intervalId); txpSearch.intervalId = null; }
  if (txpSearch.timeoutId)  { clearTimeout(txpSearch.timeoutId);  txpSearch.timeoutId  = null; }
  txpHideSearching();
  setPhase('idle');
}

async function pollOnce(){
  let center;
  if (originLatLng && typeof originLatLng.lat === 'function') center = originLatLng;
  else center = map.getCenter();

  const lat = typeof center.lat === 'function' ? center.lat() : center.lat;
  const lng = typeof center.lng === 'function' ? center.lng() : center.lng;

  try{
    const r = await fetch(`${API_NEARBY_URL}?lat=${lat}&lng=${lng}&r=${txpSearch.radius}`, { cache:'no-store' });
    const list = await r.json();
    const count = Array.isArray(list) ? list.length : 0;

    if (count > 0){
      txpSearch.found = true;
      setPhase('esperando_aceptacion');
      setSearchingLabel('Conductor encontrado. Esperando aceptación…', 'fa-taxi');
      highlightNearest(list, lat, lng);

      if (txpSearch.intervalId){ clearInterval(txpSearch.intervalId); txpSearch.intervalId = null; }
      if (txpSearch.timeoutId){  clearTimeout(txpSearch.timeoutId);   txpSearch.timeoutId  = null; }
    } else {
      if (txpSearch.radius < txpSearch.maxRadius) txpSearch.radius += 2;
    }
  }catch(e){
    console.warn('Error buscando conductores', e);
  }
}

function highlightNearest(list, lat, lng){
  let best=null, bestD=Infinity;
  for (const d of list){
    const dist = haversine(lat, lng, parseFloat(d.lat), parseFloat(d.lng));
    if (dist < bestD){ bestD = dist; best = d; }
  }
  if (best && driverMarkers.has(best.conductor_id)){
    const obj = driverMarkers.get(best.conductor_id);
    if (obj?.halo) obj.halo.setOpacity(1);
    if (obj?.car)  obj.car.setZIndex(700);
    setTimeout(() => {
      if (obj?.halo) obj.halo.setOpacity(0.8);
      if (obj?.car)  obj.car.setZIndex(500);
    }, 1000);
  }
}


function haversine(lat1,lng1,lat2,lng2){
  const R=6371e3, toRad=a=>a*Math.PI/180;
  const φ1=toRad(lat1), φ2=toRad(lat2);
  const Δφ=toRad(lat2-lat1), Δλ=toRad(lng2-lng1);
  const a=Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
  return R*2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}


document.getElementById('txp-cancelar-busqueda').addEventListener('click', async () => {
  const btn = document.getElementById('txp-cancelar-busqueda');
  const csrf = (typeof getCsrf === 'function')
    ? getCsrf()
    : document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  setSearchingLabel('Cancelando búsqueda…', 'fa-circle-notch fa-spin');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Cancelando…';

  try {
    if (window.currentViajeId && typeof VIAJE_CANCELAR_URL !== 'undefined') {
      await fetch(VIAJE_CANCELAR_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
          viaje_id: window.currentViajeId,
          motivo: 'cancelado_pasajero'
        })
      }).catch(() => {});
    }
  } finally {
    stopDriverSearch();
    if (typeof showBanner === 'function') showBanner('Solicitud cancelada', 'fa-circle-xmark');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Cancelar servicio';
  
    stopTripStateLoop();
    window.currentViajeId = null;
    lastEstado = null;
  }
});
</script>

<script>

const CONDUCTOR_INFO_URL = "{{ url('/conductor') }}";

const $sheetAsignado = document.getElementById('txp-sheet-asignado');
const $asNombre  = document.getElementById('txp-asignado-nombre');
const $asVehic   = document.getElementById('txp-asignado-vehiculo');
const $asPlaca   = document.getElementById('txp-asignado-placa');
const $asETA     = document.getElementById('txp-asignado-eta');
const $asDist    = document.getElementById('txp-asignado-dist');

function showAsignado(){ $sheetAsignado.setAttribute('aria-hidden','false'); }
function hideAsignado(){ $sheetAsignado.setAttribute('aria-hidden','true'); }



const assigned = {
  active: false,
  conductorId: null,
  detail: null,     
  etaTimer: null,
  poly: null       
};


function clearDriverPolyline(){
  if (assigned.poly){ assigned.poly.setMap(null); assigned.poly = null; }
}
function renderDriverPolyline(path){
  clearDriverPolyline();
  assigned.poly = new google.maps.Polyline({
    map,
    path,
    strokeOpacity: 0,
    icons: [{
      icon: { path: 'M 0,-1 0,1', strokeOpacity: .9, strokeColor: '#22d3ee', scale: 2 },
      offset: '0',
      repeat: '18px'
    }],
    zIndex: 14
  });
}


function mToKmStr(m){ return m >= 995 ? `${Math.round(m/1000)} km` : `${(m/1000).toFixed(1)} km`; }
function minStr(min){ return `${Math.max(1, Math.round(min))} min`; }


async function fetchConductorInfo(conductorId){
  try{
    const r = await fetch(`${CONDUCTOR_INFO_URL}/${conductorId}`, { cache:'no-store' });
    const j = await r.json();
    return j?.ok ? j : null;
  }catch{ return null; }
}


async function updateETAOnce(){
  if (!assigned.active || !assigned.conductorId || !originLatLng) return;

  const mk = driverMarkers.get(assigned.conductorId); 
  if (!mk) return;

  const driverPos = mk.getPosition ? mk.getPosition() : null;
  if (!driverPos) return;

  try{
    const req = {
      origin: driverPos,
      destination: originLatLng,
      travelMode: google.maps.TravelMode.DRIVING
    };
    const res = await new Promise((ok,ko)=> directionsService.route(req,(r,s)=> s==='OK'?ok(r):ko(s)));
    const leg = res.routes?.[0]?.legs?.[0];
    if (!leg) return;

  
    const etaTxt  = leg.duration?.text || '—';
    const distTxt = leg.distance?.text || '—';
    $asETA.textContent  = `ETA ${etaTxt}`;
    $asDist.textContent = distTxt;

    
    const path = res.routes[0].overview_path;
    renderDriverPolyline(path);
  }catch(e){
 
  }
}

function startETALoop(){
  if (assigned.etaTimer) clearInterval(assigned.etaTimer);

  updateETAOnce();

  assigned.etaTimer = setInterval(updateETAOnce, 5000);
}
function stopETALoop(){
  if (assigned.etaTimer){ clearInterval(assigned.etaTimer); assigned.etaTimer = null; }
}


function focusAssignedDriver(){
  const mk = driverMarkers.get(assigned.conductorId);
  if (mk?.getPosition){
    map.panTo(mk.getPosition());
 
    try{ mk.setZIndex(700); setTimeout(()=> mk.setZIndex(500), 1000); }catch{}
  }
}


async function onDriverAccepted(detail){
  
  txpHideSearching();

  assigned.active = true;
  assigned.conductorId = detail.conductor_id;
  assigned.detail = detail;


  let info = await fetchConductorInfo(detail.conductor_id);
  const nombre = info?.nombre || detail.nombre || `Conductor #${detail.conductor_id}`;
  const vehTxt = `${info?.vehiculo?.marca ?? detail.marca ?? ''} ${info?.vehiculo?.linea ?? detail.linea ?? ''}`.trim();
  const placa = info?.vehiculo?.placa || detail.placa || '—';

  $asNombre.textContent = nombre;
  $asVehic.textContent  = vehTxt || 'Taxi';
  $asPlaca.textContent  = `Placa ${placa}`;

  
  showAsignado();
  focusAssignedDriver();
  startETALoop();
}



</script>

<script>

const PASAJERO_ID          = {{ auth()->id() ?? 'null' }};
const VIAJE_SOLICITAR_URL  = "{{ route('viaje.solicitar') }}";
const VIAJE_ESTADO_URL     = "{{ url('/viaje/estado') }}";              
const VIAJE_CANCELAR_URL   = "{{ route('viaje.cancelar') }}";  

function getCsrf(){
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

function collectTripPayload(){
  const oTxt = document.getElementById('origin-input')?.value || null;
  const dTxt = document.getElementById('dest-input')?.value || null;

  const oLat = originLatLng ? (typeof originLatLng.lat === 'function' ? originLatLng.lat() : originLatLng.lat) : null;
  const oLng = originLatLng ? (typeof originLatLng.lng === 'function' ? originLatLng.lng() : originLatLng.lng) : null;
  const dLat = destLatLng   ? (typeof destLatLng.lat   === 'function' ? destLatLng.lat()   : destLatLng.lat)   : null;
  const dLng = destLatLng   ? (typeof destLatLng.lng   === 'function' ? destLatLng.lng()   : destLatLng.lng)   : null;

  return {
    pasajero_id: PASAJERO_ID,
    categoria: 'taxi',
    ciudad: "{{ config('taxpiya.default_city', 'Medellín') }}",
    o_lat: oLat, o_lng: oLng, o_txt: oTxt,
    d_lat: dLat, d_lng: dLng, d_txt: dTxt
  };
}

async function solicitarViajeThenSearch(){
  try{
    const payload = collectTripPayload();
    if (!payload.pasajero_id){ alert('No hay pasajero identificado.'); return; }
    if (payload.o_lat == null || payload.o_lng == null){ alert('Falta origen válido.'); return; }

    const r = await fetch(VIAJE_SOLICITAR_URL, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify(payload)
    });

    const j = await r.json();
    if (!r.ok || !j.ok){ alert('No se pudo iniciar la solicitud.'); return; }

    window.currentViajeId = j.viaje_id; 
    ocultarSheet();
    startDriverSearch();     
    startTripStateLoop();    
  }catch(e){
    console.error(e);
    alert('Error de red al solicitar viaje.');
  }
}


(() => {
  const oldBtn = document.getElementById('txp-btn-confirmar');
  const clone = oldBtn.cloneNode(true);
  oldBtn.parentNode.replaceChild(clone, oldBtn);
  document.getElementById('txp-btn-confirmar').addEventListener('click', solicitarViajeThenSearch);
})();
</script>


<script>
function showBanner(text, icon='fa-circle-info'){
  const b = document.getElementById('txp-banner');
  const i = document.getElementById('txp-banner-ico');
  const t = document.getElementById('txp-banner-txt');
  if (!b || !i || !t) return;
  i.className = `fa-solid ${icon} me-2`;
  t.textContent = text;
  b.classList.add('show');

  if (!/en viaje/i.test(text)) {
    setTimeout(()=> b.classList.remove('show'), 3500);
  }
}
function hideBanner(){
  const b = document.getElementById('txp-banner');
  if (b) b.classList.remove('show');
}

function showFinalSheet(monto, moneda='COP'){
  const sh = document.getElementById('txp-sheet-final');
  const lbl = document.getElementById('txp-final-monto');
  const fmt = new Intl.NumberFormat('es-CO',{ style:'currency', currency: moneda || 'COP', maximumFractionDigits:0 });
  lbl.textContent = monto != null ? fmt.format(monto) : '—';
  sh.setAttribute('aria-hidden','false');
  document.body.style.overflow='hidden';
}
function hideFinalSheet(){
  const sh = document.getElementById('txp-sheet-final');
  sh.setAttribute('aria-hidden','true');
  document.body.style.overflow='';
}

document.querySelectorAll('#txp-sheet-final [data-close], #txp-final-ok').forEach(el=>{
  el.addEventListener('click', hideFinalSheet);
});
</script>

<script>

let estadoTimer = null;
let lastEstado  = null;

function startTripStateLoop(){
  stopTripStateLoop();

  checkTripStateOnce();
  estadoTimer = setInterval(checkTripStateOnce, 3000);
}
function stopTripStateLoop(){
  if (estadoTimer){ clearInterval(estadoTimer); estadoTimer = null; }
}

async function checkTripStateOnce(){
  if (!window.currentViajeId || !VIAJE_ESTADO_URL) return;

  try{
    const r = await fetch(`${VIAJE_ESTADO_URL}/${window.currentViajeId}`, { cache:'no-store' });
    const j = await r.json();
    if (!j?.ok) return;

    const est = j.estado;
    if (est === lastEstado) return;
    lastEstado = est;

    switch(est){
      case 'buscando':
        setSearchingLabel('Buscando conductores disponibles…', 'fa-location-dot');
        break;

      case 'asignado':
  onDriverAccepted({
    conductor_id: j.conductor_id,
    nombre: j.conductor?.nombre,
    telefono: j.conductor?.telefono,
    placa:  j.vehiculo?.placa,
    marca:  j.vehiculo?.marca,
    linea:  j.vehiculo?.linea
  });
  if ($sheetAsignado.getAttribute('aria-hidden') === 'true') showAsignado();
  showBanner('Conductor asignado', 'fa-taxi');
  toggleCTA && toggleCTA(false);
  break;

      case 'llego':
  showBanner('El conductor ha llegado', 'fa-flag-checkered');
  showAbordoAction(true);     
  break;

case 'en_camino':
  if ($sheetAsignado.getAttribute('aria-hidden') === 'true') showAsignado();
  showBanner('El conductor va en camino', 'fa-route');
  toggleCTA && toggleCTA(false);
  break;

      case 'iniciado':
       
        hideAsignado();
        stopETALoop();
        clearDriverPolyline();
        showBanner('En viaje', 'fa-location-arrow');
        break;

    case 'terminado': {

  hideAsignado?.();
  stopETALoop?.();
  clearDriverPolyline?.();
  stopTripStateLoop?.();       
  txpHideSearching?.();
  showBanner?.('Viaje finalizado', 'fa-flag-checkered');


  showFinalSheet(j.monto ?? null, j.moneda ?? 'COP');
toggleCTA && toggleCTA(true);
  
  break;
}

      case 'cancelado_pasajero':
      case 'cancelado_conductor':
      case 'no_show':
      case 'fallo_localizacion':
     
        hideAsignado();
        stopETALoop();
        clearDriverPolyline();
        stopTripStateLoop();
        txpHideSearching();
        showBanner('La solicitud fue cancelada', 'fa-circle-xmark');
        window.currentViajeId = null;
        lastEstado = null;
		toggleCTA && toggleCTA(true);
        break;

      default:
       
        break;
    }
  }catch(e){
   
  }
}
</script>


<script>
  (function(){
    const qm = document.getElementById('quickMenu');
    const btn = document.getElementById('qmToggle');

    function closeMenu(){
      qm.classList.remove('open');
      btn.setAttribute('aria-expanded','false');
    }
    btn.addEventListener('click', (e)=>{
      e.stopPropagation();
      const open = !qm.classList.contains('open');
      qm.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
   
    document.addEventListener('click', (e)=>{
      if(!qm.contains(e.target)) closeMenu();
    });
    document.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape') closeMenu();
    });
  })();
 
</script>
<script>

let assignmentTimer = null;
const assignmentPollMs = 2800;

function startAssignmentWatcher(){
  if (assignmentTimer || !window.currentViajeId) return;
  checkAssignmentOnce(); 
  assignmentTimer = setInterval(checkAssignmentOnce, assignmentPollMs);
}
function stopAssignmentWatcher(){
  if (assignmentTimer){ clearInterval(assignmentTimer); assignmentTimer = null; }
}

async function checkAssignmentOnce(){
  const id = window.currentViajeId;
  if (!id) return;

  try{
    const r = await fetch(`${VIAJE_ESTADO_URL}/${id}`, { cache:'no-store' });
    const j = await r.json();
    if (!j.ok) return;

    
    if (j.conductor_id && j.driver_pos && driverMarkers.has(j.conductor_id)) {
      const mk = driverMarkers.get(j.conductor_id);
      const latLng = new google.maps.LatLng(j.driver_pos.lat, j.driver_pos.lng);
      if (mk?.car)  mk.car.setPosition(latLng);
      if (mk?.halo) mk.halo.setPosition(latLng);
    }

    switch (j.estado) {
      case 'buscando':
      
        break;

      case 'asignado':
  onDriverAccepted({
    conductor_id: j.conductor_id,
    nombre: j.conductor?.nombre,
    telefono: j.conductor?.telefono,
    placa:  j.vehiculo?.placa,
    marca:  j.vehiculo?.marca,
    linea:  j.vehiculo?.linea
  });
  if ($sheetAsignado.getAttribute('aria-hidden') === 'true') showAsignado();
  showBanner('Conductor asignado', 'fa-taxi');
  toggleCTA && toggleCTA(false); 
  break;
      case 'en_camino': {
       
        if (!assigned.active || assigned.conductorId !== j.conductor_id) {
          await onDriverAccepted({
            conductor_id: j.conductor_id,
            nombre: j?.conductor?.nombre,
            marca:  j?.vehiculo?.marca,
            linea:  j?.vehiculo?.linea,
            placa:  j?.vehiculo?.placa
          });
        }
        if (j.estado === 'en_camino') {
          showBanner('Conductor en camino', 'fa-taxi');
        } else {
          showBanner('Conductor asignado', 'fa-user-check');
        }
        break;
      }

     case 'llego':
  showBanner('El conductor ha llegado', 'fa-flag-checkered');
  showAbordoAction(true);     
  break;
      case 'iniciado':
        showBanner('En viaje', 'fa-route');
        break;

case 'terminado': {
  
  hideAsignado?.();
  stopETALoop?.();
  clearDriverPolyline?.();
  stopTripStateLoop?.();      
  txpHideSearching?.();
  showBanner?.('Viaje finalizado', 'fa-flag-checkered');

 
  showFinalSheet(j.monto ?? null, j.moneda ?? 'COP');

  
  break;
}

      case 'cancelado_pasajero':
      case 'cancelado_conductor':
      case 'cancelado_sistema':
      case 'no_show':
      case 'fallo_localizacion':
      default: {
        stopAssignmentWatcher();
        stopETALoop();
        clearDriverPolyline();
        hideAsignado();
        txpHideSearching();
        showBanner('El viaje fue cancelado', 'fa-circle-exclamation');
        window.currentViajeId = null;
        break;
      }
    }
  }catch(e){
    console.warn('Estado viaje error', e);
  }
}


async function cancelarViajeDesdeUI(origen = 'radar'){
 
  if (origen === 'radar') { txpHideSearching(); }
  hideAsignado();
  stopETALoop();
  clearDriverPolyline();

  stopAssignmentWatcher();

  const id = window.currentViajeId;
  if (!id) { 
    showBanner('Solicitud cancelada', 'fa-ban');
    return;
  }

  try{
    const r = await fetch(VIAJE_CANCELAR_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify({ viaje_id: id, motivo: 'cancelado_pasajero' })
    });
   
    showBanner('Solicitud cancelada', 'fa-ban');
  }catch(e){
    console.warn('Error al cancelar viaje', e);
    showBanner('No se pudo cancelar. Reintenta', 'fa-triangle-exclamation');
  }finally{
    window.currentViajeId = null;
    stopDriverSearch(); 
  }
}


(() => {

  const old1 = document.getElementById('txp-cancelar-busqueda');
  if (old1) {
    const nn = old1.cloneNode(true);
    old1.parentNode.replaceChild(nn, old1);
    nn.addEventListener('click', ()=> cancelarViajeDesdeUI('radar'));
  }
 
  const old2 = document.getElementById('txp-cancelar-asignado');
  if (old2) {
    const nn2 = old2.cloneNode(true);
    old2.parentNode.replaceChild(nn2, old2);
    nn2.addEventListener('click', ()=> cancelarViajeDesdeUI('asignado'));
  }
})();
</script>


<script>
(() => {
  const btn = document.getElementById('txp-cancelar-asignado');
  if (!btn) return;

  btn.addEventListener('click', async ()=>{
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Cancelando…';

    try{
      const csrf = (typeof getCsrf === 'function')
        ? getCsrf()
        : document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      if (window.currentViajeId && typeof VIAJE_CANCELAR_URL !== 'undefined') {
        await fetch(VIAJE_CANCELAR_URL, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({
            viaje_id: window.currentViajeId,
            motivo: 'cancelado_pasajero'   
          })
        }).catch(()=>{});
      }
    } finally {
      hideAsignado();
      stopETALoop();
      clearDriverPolyline();
      stopTripStateLoop();
      txpHideSearching();
      if (typeof showBanner === 'function') showBanner('Solicitud cancelada', 'fa-circle-xmark');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-ban me-1"></i> Cancelar';
      window.currentViajeId = null;
      lastEstado = null;
    }
  });
})();
</script>
<script>
  const VIAJE_ABORDO_URL = "{{ route('viaje.pasajero.abordo') }}";
</script>
<script>
  const VIAJE_CALIFICAR_URL = "{{ route('viaje.calificar') }}";
</script>

<script>

(function ensureAbordoButton(){
  const sheet = document.getElementById('txp-sheet-asignado');
  if (!sheet) return;

  
const host = sheet.querySelector('.txp-acciones') 
             || sheet.querySelector('.txp-sheet-dialog') 
             || sheet;


let actions = sheet.querySelector('.txp-asignado-actions');
if (!actions) {
  actions = document.createElement('div');
  actions.className = 'txp-asignado-actions mt-2';
  host.appendChild(actions);
}

  
  let btn = document.getElementById('txp-btn-abordo');
  if (!btn) {
btn = document.createElement('button');
btn.type = 'button';   

    btn.id = 'txp-btn-abordo';
    btn.className = 'btn btn-warning w-100';
    btn.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Estoy en el vehículo';
    btn.style.display = 'none';
    actions.appendChild(btn);
  }
})();


function showAbordoAction(show){
  const b = document.getElementById('txp-btn-abordo');
  if (!b) return;
  b.style.display = show ? 'block' : 'none';
}


(() => {
  const b = document.getElementById('txp-btn-abordo');
  if (!b) return;
  b.addEventListener('click', async () => {
    if (!window.currentViajeId || typeof VIAJE_ABORDO_URL === 'undefined') return;
    b.disabled = true;
    b.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Enviando…';
    try{
     await fetch(VIAJE_ABORDO_URL, {
  method: 'POST',
  credentials: 'same-origin',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': getCsrf()
  },
  body: JSON.stringify({ viaje_id: window.currentViajeId })
});
      if (typeof showBanner === 'function') showBanner('¡Listo! Avisamos que ya abordaste.', 'fa-thumbs-up');
     
    } catch(e){
      if (typeof showBanner === 'function') showBanner('No se pudo enviar, reintenta.', 'fa-triangle-exclamation');
    } finally {
      b.disabled = false;
      b.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Estoy en el vehículo';
    }
  });
})();
</script>

<script>

(function injectRatingCSS(){
  if (document.getElementById('txp-rating-css')) return;
  const css = `
    .txp-rating-wrap{margin-top:10px;padding-top:10px;border-top:1px dashed rgba(255,255,255,0.15);}
    .txp-stars{display:flex;gap:6px;align-items:center;justify-content:center;margin:8px 0 4px;}
    .txp-stars .star{font-size:28px; line-height:1; background:transparent; border:none; cursor:pointer; opacity:.85;}
    .txp-stars .star.active{color:#ffb100; opacity:1;}
    #txp-rating-nota{width:100%;min-height:70px;resize:vertical;border-radius:8px;padding:8px;}
    .txp-rating-actions{display:flex;gap:8px;margin-top:10px;}
  `;
  const el = document.createElement('style');
  el.id = 'txp-rating-css';
  el.textContent = css;
  document.head.appendChild(el);
})();


const ratingState = { score: 0 };


function ensureRatingBlock(){
  const sheet = document.getElementById('txp-sheet-final');
  if (!sheet) return;


  const body = sheet.querySelector('.txp-sheet__body') || sheet;

  let box = sheet.querySelector('.txp-rating-wrap');
  if (box) return; 

  box = document.createElement('div');
  box.className = 'txp-rating-wrap';

  box.innerHTML = `
    <div class="text-center mb-1" style="font-weight:600;">Califica tu viaje</div>
    <div class="txp-stars" role="radiogroup" aria-label="Calificación">
      ${[1,2,3,4,5].map(n => `<button type="button" class="star" data-star="${n}" aria-label="${n} estrellas">★</button>`).join('')}
    </div>
    <div class="text-center" id="txp-stars-hint" style="font-size:12px;opacity:.85;">Toca una estrella</div>
    <textarea id="txp-rating-nota" class="form-control mt-2" placeholder="Comentario (opcional)"></textarea>
    <div class="txp-rating-actions">
      <button id="txp-rating-enviar" class="btn btn-warning flex-fill">
        <i class="fa-solid fa-paper-plane me-1"></i> Enviar
      </button>
      <button id="txp-rating-omitir" class="btn btn-secondary flex-fill">
        Omitir
      </button>
    </div>
  `;

  body.appendChild(box);

 
  box.querySelectorAll('.star').forEach(btn => {
    btn.addEventListener('click', () => {
      const val = parseInt(btn.getAttribute('data-star'), 10);
      ratingState.score = val;
      box.querySelectorAll('.star').forEach(s => {
        const n = parseInt(s.getAttribute('data-star'), 10);
        s.classList.toggle('active', n <= val);
      });
      const hint = document.getElementById('txp-stars-hint');
      if (hint) hint.textContent = `Tu calificación: ${val} ${val===1?'estrella':'estrellas'}`;
    });
  });
}


(function bindFinalSheetDelegation(){
  const sheet = document.getElementById('txp-sheet-final');
  if (!sheet) return;

  sheet.addEventListener('click', (e) => {
    const sendBtn   = e.target.closest('#txp-rating-enviar');
    const skipBtn   = e.target.closest('#txp-rating-omitir');
    const understood= e.target.closest('#txp-final-ok');

    if (sendBtn) {
      e.preventDefault();
      enviarCalificacion();
    } else if (skipBtn) {
      e.preventDefault();
      hideFinalSheet();
    } else if (understood) {
      e.preventDefault();
      hideFinalSheet();
    }
  });
})();


(function patchShowFinal(){
  const _show = window.showFinalSheet;
  window.showFinalSheet = function(monto, moneda){
    _show?.(monto, moneda);
    ratingState.score = 0;
    ensureRatingBlock();
    // reset visual de estrellas y textarea
    const wrap = document.querySelector('#txp-sheet-final .txp-rating-wrap');
    if (wrap){
      wrap.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
      const hint = document.getElementById('txp-stars-hint');
      if (hint) hint.textContent = 'Toca una estrella';
      const ta = document.getElementById('txp-rating-nota');
      if (ta) ta.value = '';
    }
  };
})();


(function patchHideFinal(){
  const _hide = window.hideFinalSheet;
  window.hideFinalSheet = function(){
    _hide?.(); 
 
    window.currentViajeId = null;
    if (typeof lastEstado !== 'undefined') lastEstado = null;

 
    if (typeof resetMapUI === 'function') {
      resetMapUI();
    } else {
      // fallback: limpia inputs y ruta
      try {
        document.getElementById('origin-input').value = '';
        document.getElementById('dest-input').value   = '';
      } catch(_) {}
      if (typeof clearNeonRoute === 'function') clearNeonRoute();
      if (typeof directionsRenderer !== 'undefined') directionsRenderer.set('directions', null);
    }
    
    try { toggleCTA && toggleCTA(false); } catch(_) {}
  };
})();


async function enviarCalificacion(){
  
  if (!window.currentViajeId || typeof VIAJE_CALIFICAR_URL === 'undefined') {
    if (typeof showBanner === 'function') showBanner('No hay viaje para calificar', 'fa-triangle-exclamation');
    return;
  }


  const score = (ratingState && typeof ratingState.score === 'number') ? ratingState.score : 0;
  if (!score) {
    if (typeof showBanner === 'function') showBanner('Selecciona una calificación', 'fa-star');
    return;
  }


  const texto = document.getElementById('txp-rating-nota')?.value?.trim() || '';


  const btn = document.getElementById('txp-rating-enviar');
  if (btn){
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Enviando…';
  }

  try{
    const r = await fetch(VIAJE_CALIFICAR_URL, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify({
        viaje_id: window.currentViajeId,
        puntuacion: score,
        comentario: texto || null
      })
    });

    let ok = r.ok;
    let j  = null;
    try { j = await r.json(); } catch(_){}

    if (!ok || (j && j.ok === false)) {
      const msg = (j && j.message) ? j.message : 'No se pudo calificar, reintenta.';
      if (typeof showBanner === 'function') showBanner(msg, 'fa-triangle-exclamation');
      return;
    }

    if (typeof showBanner === 'function') showBanner('¡Gracias por tu calificación!', 'fa-star');
    hideFinalSheet(); // cierra sheet y (en tu patch) limpia currentViajeId + resetea mapa
  }catch(e){
    if (typeof showBanner === 'function') showBanner('No se pudo calificar, reintenta.', 'fa-triangle-exclamation');
  }finally{
    if (btn){
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Enviar';
    }
  }
}
</script>
<script>

const VIAJE_CHAT_LIST_BASE = "{{ url('/viaje') }}";  
const VIAJE_CHAT_SEND_URL  = "{{ route('viaje.chat.send') }}";
const VIAJE_CHAT_READ_URL  = "{{ route('viaje.chat.read') }}";


(function ensureChatUI(){
  if (document.getElementById('txp-chat')) return;

  const wrap = document.createElement('div');
  wrap.id = 'txp-chat';
  wrap.className = 'txp-chat';
  wrap.setAttribute('aria-hidden','true');
  wrap.innerHTML = `
    <div class="txp-chat-box">
      <div class="txp-chat-header">
        <button id="txp-chat-close" class="btn btn-light btn-sm"><i class="fa-solid fa-arrow-left"></i></button>
        <div class="title"><i class="fa-solid fa-comments me-2"></i><span id="txp-chat-title">Chat</span></div>
        <div class="spacer"></div>
      </div>
      <div id="txp-chat-list" class="txp-chat-list"></div>
      <div class="txp-chat-input">
        <input id="txp-chat-text" type="text" class="form-control" placeholder="Escribe un mensaje..." />
        <button id="txp-chat-send" class="btn btn-warning"><i class="fa-solid fa-paper-plane"></i></button>
      </div>
    </div>
  `;
  document.body.appendChild(wrap);


  const css = `
    .txp-chat{position:fixed;inset:0;display:flex;align-items:flex-end;background:rgba(0,0,0,.35);z-index:9999}
    .txp-chat[aria-hidden="true"]{display:none}
    .txp-chat-box{width:100%;max-width:720px;margin:0 auto;background:#0f172a;color:#f8fafc;border-top-left-radius:16px;border-top-right-radius:16px;box-shadow:0 -6px 24px rgba(0,0,0,.3);display:flex;flex-direction:column;max-height:88vh}
    .txp-chat-header{display:flex;gap:8px;align-items:center;padding:10px 12px;border-bottom:1px solid rgba(255,255,255,.08)}
    .txp-chat-header .title{font-weight:700}
    .txp-chat-header .spacer{flex:1}
    .txp-chat-list{flex:1;overflow:auto;padding:12px;display:flex;flex-direction:column;gap:8px}
    .txp-bubble{max-width:78%;padding:8px 12px;border-radius:14px;font-size:14px;line-height:1.35;word-wrap:break-word;white-space:pre-wrap}
    .txp-me{align-self:flex-end;background:#ffd166;color:#111}
    .txp-other{align-self:flex-start;background:#1e293b;color:#e2e8f0;border:1px solid rgba(255,255,255,.06)}
    .txp-time{font-size:11px;opacity:.7;margin-top:2px}
    .txp-chat-input{display:flex;gap:8px;padding:10px;border-top:1px solid rgba(255,255,255,.08)}
  `;
  const st = document.createElement('style'); st.textContent = css; document.head.appendChild(st);


  document.getElementById('txp-chat-close').addEventListener('click', closeChat);
  document.getElementById('txp-chat-send').addEventListener('click', sendChatMessage);
  document.getElementById('txp-chat-text').addEventListener('keydown', (e)=>{ if (e.key==='Enter') sendChatMessage(); });
})();


const chatState = {
  open: false,
  timer: null,
  pollMs: 2000,
  lastId: 0,
  title: 'Chat',
};


const renderedIds = new Set();       
const pendingHashes = new Set();     


function msgHash(rol, mensaje){
  return `${rol}|${(mensaje || '').trim().replace(/\s+/g,' ')}`;
}


function openChat(){
  if (!window.currentViajeId) return;
  chatState.open = true;
  document.getElementById('txp-chat').setAttribute('aria-hidden','false');

  loadChat(true);

  if (chatState.timer) clearInterval(chatState.timer);
  chatState.timer = setInterval(()=> loadChat(false), chatState.pollMs);
}

function closeChat(){
  chatState.open = false;
  document.getElementById('txp-chat').setAttribute('aria-hidden','true');
  if (chatState.timer){ clearInterval(chatState.timer); chatState.timer = null; }
}

function resetMapUI(){
  try{ directionsRenderer?.set('directions', null); }catch{}
  try{ clearNeonRoute?.(); }catch{}

  try{
    if (originMarker){ originMarker.setMap(null); originMarker = null; }
    if (destinationMarker){ destinationMarker.setMap(null); destinationMarker = null; }
  }catch{}

  originLatLng = null;
  destLatLng   = null;

  const oi = document.getElementById('origin-input'); if (oi) oi.value = '';
  const di = document.getElementById('dest-input');   if (di) di.value = '';

  try{ infoWindow?.close?.(); }catch{}
  try{ toggleCTA && toggleCTA(false); }catch{}

 
  try{ hideAsignado?.(); }catch{}
  try{ stopETALoop?.(); }catch{}
  try{ clearDriverPolyline?.(); }catch{}
  try{ txpHideSearching?.(); }catch{}
}

async function loadChat(initial){
  if (!window.currentViajeId) return;
  try{
    const url = `${VIAJE_CHAT_LIST_BASE}/${window.currentViajeId}/chat` + (chatState.lastId ? `?since_id=${chatState.lastId}` : '');
    const r = await fetch(url, { cache:'no-store' });
    const j = await r.json();
    if (!j?.ok) return;

    const list = j.items || [];
    if (list.length){
      renderChat(list); 
      chatState.lastId = Math.max(chatState.lastId, ...list.map(x=>x.id || 0));

     
      const lastOther = [...list].reverse().find(m => m.rol === 'conductor');
      if (lastOther){
        fetch(VIAJE_CHAT_READ_URL, {
          method:'POST', headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN': getCsrf()
          },
          body: JSON.stringify({ viaje_id: window.currentViajeId, max_id: lastOther.id })
        }).catch(()=>{});
      }
    }
  
    if (initial || list.length){
      const box = document.getElementById('txp-chat-list');
      box.scrollTop = box.scrollHeight;
    }
  }catch(e){  }
}

function renderChat(items){
  const box = document.getElementById('txp-chat-list');

  for (const m of items){
    const id = m.id;
    if (id && renderedIds.has(id)) continue;  

    const mine = (m.rol === 'pasajero');
    const text = (m.mensaje ?? '');
    const hash = msgHash(m.rol, text);

    
    if (mine && pendingHashes.has(hash)){
      pendingHashes.delete(hash);   
      if (id) renderedIds.add(id);   
      continue;                     
    }

  
    const div = document.createElement('div');
    div.className = `txp-bubble ${mine ? 'txp-me' : 'txp-other'}`;


    let time;
    try{
      time = new Date((m.created_at || '').replace(' ', 'T'));
    }catch(_){ time = new Date(); }
    const hh = time.getHours().toString().padStart(2,'0');
    const mm = time.getMinutes().toString().padStart(2,'0');

 
    const safe = text.replace(/</g,'&lt;').replace(/>/g,'&gt;');
    div.innerHTML = `${safe}<div class="txp-time">${hh}:${mm}</div>`;
    box.appendChild(div);

    if (id) renderedIds.add(id);
  }
}

async function sendChatMessage(){
  if (!window.currentViajeId) return;
  const inp = document.getElementById('txp-chat-text');
  const msg = (inp.value || '').trim();
  if (!msg) return;

 
  const hash = msgHash('pasajero', msg);
  pendingHashes.add(hash);
  renderChat([{ rol:'pasajero', mensaje: msg, created_at: (new Date()).toISOString().slice(0,16).replace('T',' ') }]);


  const box = document.getElementById('txp-chat-list'); 
  box.scrollTop = box.scrollHeight;
  inp.value = '';

 
  try{
    await fetch(VIAJE_CHAT_SEND_URL, {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'Accept':'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify({ viaje_id: window.currentViajeId, mensaje: msg, tipo:'text' })
    });
   
  }catch(e){
  
    if (typeof showBanner === 'function') showBanner('No se pudo enviar el mensaje', 'fa-triangle-exclamation');
  }
}


(function patchOnDriverAccepted(){
  const _onDriverAccepted = window.onDriverAccepted;
  window.onDriverAccepted = async function(detail){
    await _onDriverAccepted?.(detail);

   
    const nombre = detail?.nombre || 'Conductor';
    const titleEl = document.getElementById('txp-chat-title');
    if (titleEl) titleEl.textContent = `Chat con ${nombre}`;

    
    let callBtn = document.getElementById('txp-call');
    if (!callBtn){
      const cont = document.querySelector('#txp-sheet-asignado .txp-sheet-dialog')|| document.getElementById('txp-sheet-asignado');
      const row = document.createElement('div');
      row.className = 'd-flex gap-2 mt-2';
      row.innerHTML = `
        <a id="txp-call" class="btn btn-outline-light flex-fill" href="#"><i class="fa-solid fa-phone"></i> Llamar</a>
        <button id="txp-chat-open" class="btn btn-warning flex-fill"><i class="fa-solid fa-comments"></i> Chat</button>
      `;
      cont && cont.appendChild(row);
      callBtn = row.querySelector('#txp-call');
      row.querySelector('#txp-chat-open').addEventListener('click', openChat);
    } else {
     
      const chatBtn = document.getElementById('txp-chat-open');
      if (chatBtn && !chatBtn._bound){
        chatBtn.addEventListener('click', openChat);
        chatBtn._bound = true;
      }
    }

   
    let telefono = detail?.telefono;
    if (!telefono && typeof fetchConductorInfo === 'function' && detail?.conductor_id){
      const info = await fetchConductorInfo(detail.conductor_id);
      telefono = info?.telefono || info?.phone || null;
    }
    if (callBtn){
      if (telefono){
        callBtn.href = `tel:${telefono}`;
        callBtn.classList.remove('disabled');
      } else {
        callBtn.href = '#';
        callBtn.classList.add('disabled');
      }
    }
  };
})();
</script>



<script>

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
const API_NEARBY_URL = "{{ url('/api/nearby-drivers') }}";


let driverMarkers = new Map(); 
let driversFetchInFlight = false;
let refreshIdleTimer = null;

let map, geocoder, infoWindow, directionsService, directionsRenderer;
let userMarker = null, originMarker = null, destinationMarker = null, accCircle = null;
let watchId = null;
let originLatLng = null, destLatLng = null;
let autocompleteOrigin = null, autocompleteDest = null;


let neonRoute = {
  base: null,   
  glow1: null,  
  glow2: null,  
  dash: null,  
  timer: null
};


let pinMode = null; 
let pinClickListener = null;


const CAR_RATIO = 184 / 424; 

function carSizeByZoom(zoom = 15) {
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  const baseH = 86; 
  const h = clamp(Math.round(baseH * Math.pow(1.12, (zoom - 15))), 72, 148);
  const w = Math.round(h * CAR_RATIO);
  return { w, h };
}

function carAnchorForSize({ w, h }) {
  
  return new google.maps.Point(Math.round(w / 2), Math.round(h * 0.85));
}

function carIconForZoom(zoom) {
  const { w, h } = carSizeByZoom(zoom);
  return {
    url: CAR_ICON_URL,
    scaledSize: new google.maps.Size(w, h),
    anchor: carAnchorForSize({ w, h })
  };
}


function haloIconForZoom(zoom) {
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
          <feGaussianBlur stdDeviation="${Math.max(2, Math.round(d * 0.05))}" />
        </filter>
      </defs>
      <circle cx="${r}" cy="${r}" r="${Math.round(r * 0.85)}" fill="url(#g)" filter="url(#f)"/>
    </svg>`;
  const url = "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
  return {
    url,
    scaledSize: new google.maps.Size(d, d),
    anchor: new google.maps.Point(Math.round(d / 2), Math.round(d * 0.6)) 
  };
}


function refreshDriverIconsByZoom() {
  if (!map) return;
  const z = map.getZoom() || 15;
  for (const obj of driverMarkers.values()) {
    if (obj?.car) obj.car.setIcon(carIconForZoom(z));
    if (obj?.halo) obj.halo.setIcon(haloIconForZoom(z));
  }
}


window.initMap = function(){
  const mapEl = document.getElementById('map');
  const initial = { lat: 4.7110, lng: -74.0721 };

  map = new google.maps.Map(mapEl, {
    center: initial,
    zoom: 15,
    disableDefaultUI: true,
    styles: txpMapStyle,
    gestureHandling: 'greedy'
  });

  geocoder = new google.maps.Geocoder();
  infoWindow = new google.maps.InfoWindow();
  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer({
    suppressMarkers: true,
    suppressPolylines: true, 
    preserveViewport: false
  });
  directionsRenderer.setMap(map);


  const originInput = document.getElementById('origin-input');
  autocompleteOrigin = new google.maps.places.Autocomplete(originInput, {
    fields: ["formatted_address", "geometry", "name"],
    componentRestrictions: { country: ["co"] }
  });
  autocompleteOrigin.addListener('place_changed', () => {
    const place = autocompleteOrigin.getPlace();
    if(!place.geometry || !place.geometry.location) return;
    originLatLng = place.geometry.location;
    centerTo(originLatLng);
    putOriginMarker(originLatLng);
    showLabel(originLatLng, place.formatted_address || place.name);
    tryRoute();
  });


  const destInput = document.getElementById('dest-input');
  autocompleteDest = new google.maps.places.Autocomplete(destInput, {
    fields: ["formatted_address", "geometry", "name"],
    componentRestrictions: { country: ["co"] }
  });
  autocompleteDest.addListener('place_changed', () => {
    const place = autocompleteDest.getPlace();
    if(!place.geometry || !place.geometry.location) return;
    destLatLng = place.geometry.location;
    centerTo(destLatLng);
    putDestinationMarker(destLatLng, place.formatted_address || place.name);
    tryRoute();
  });


  originInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      geocodeText(originInput.value, (loc, addr) => {
        originLatLng = loc;
        originInput.value = addr;
        centerTo(loc); putOriginMarker(loc); showLabel(loc, addr); tryRoute();
      });
    }
  });
  destInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      geocodeText(destInput.value, (loc, addr) => {
        destLatLng = loc;
        destInput.value = addr;
        centerTo(loc); putDestinationMarker(loc, addr); tryRoute();
      });
    }
  });


  document.getElementById('pin-origin').addEventListener('click', () => togglePinMode('origin'));
  document.getElementById('pin-dest').addEventListener('click', () => togglePinMode('dest'));


  getUserLocation();


  document.getElementById('recenter-btn').addEventListener('click', () => getUserLocation({ forceWatch:true }));


  setupVoice('voice-origin', 'origin-input', (txt) => {
    geocodeText(txt, (loc, addr) => {
      originLatLng = loc;
      document.getElementById('origin-input').value = addr;
      centerTo(loc); putOriginMarker(loc); showLabel(loc, addr); tryRoute();
    });
  });
  setupVoice('voice-dest', 'dest-input', (txt) => {
    geocodeText(txt, (loc, addr) => {
      destLatLng = loc;
      document.getElementById('dest-input').value = addr;
      centerTo(loc); putDestinationMarker(loc, addr); tryRoute();
    });
  });

  

 
  map.addListener('idle', scheduleIdleRefresh); 
  map.addListener('zoom_changed', refreshDriverIconsByZoom);
  refreshNearbyDrivers();                       
  setInterval(refreshNearbyDrivers, 10000);   
};


function getUserLocation(opts = { forceWatch:false }){
  if(!navigator.geolocation) return;
  const options = { enableHighAccuracy:true, timeout:12000, maximumAge:0 };

  navigator.geolocation.getCurrentPosition(
    (pos) => handlePosition(pos, { from:"single", setOrigin:true }),
    (err) => console.warn('No se pudo obtener ubicación (single)', err),
    options
  );
  if (opts.forceWatch) startWatch();
}

function startWatch(){
  if (watchId) navigator.geolocation.clearWatch(watchId);
  watchId = navigator.geolocation.watchPosition(
    (pos) => handlePosition(pos, { from:"watch", setOrigin:true }),
    (err) => console.warn('Error geolocalización (watch):', err),
    { enableHighAccuracy:true, maximumAge:0, timeout:20000 }
  );
  setTimeout(() => { if (watchId) { navigator.geolocation.clearWatch(watchId); watchId=null; } }, 15000);
}

function handlePosition(pos, meta = {}){
  const { latitude, longitude, accuracy } = pos.coords;
  const latLng = { lat: latitude, lng: longitude };

  showAccuracy(accuracy);
  drawAccuracyCircle(latLng, accuracy);
  putUserPulse(latLng);

  if (meta.setOrigin) {
    originLatLng = new google.maps.LatLng(latLng.lat, latLng.lng);
    reverseGeocode(originLatLng, (addr) => {
      document.getElementById('origin-input').value = addr || '';
      putOriginMarker(originLatLng);
      showLabel(originLatLng, addr || 'Mi ubicación');
    });
  }
  centerTo(latLng);

  if (meta.from === "watch" && accuracy <= 50 && watchId) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }


  refreshNearbyDrivers();
}

function drawAccuracyCircle(latLng, accuracy){
  if (accCircle) accCircle.setMap(null);
  accCircle = new google.maps.Circle({
    map, center: latLng, radius: Math.min(accuracy || 0, 800),
    strokeColor: "#ffd166", strokeOpacity: 0.8, strokeWeight: 1,
    fillColor: "#ffd166", fillOpacity: 0.18, clickable: false,
  });
}
function showAccuracy(m){
  const el = document.getElementById('geo-accuracy');
  el.textContent = `Precisión: ~${Math.round(m)} m`;
  el.style.display = 'block';
}


function centerTo(latLng){ map.panTo(latLng); map.setZoom(16); }


function putUserPulse(latLng){
  if(userMarker){ userMarker.setMap && userMarker.setMap(null); userMarker = null; }
  const div = document.createElement('div');
  div.className = 'pulse-marker';
  const overlay = new google.maps.OverlayView();
  overlay.onAdd = function(){ this.getPanes().overlayMouseTarget.appendChild(div); };
  overlay.draw = function(){
    const proj = this.getProjection(); if(!proj) return;
    const pos = proj.fromLatLngToDivPixel(new google.maps.LatLng(latLng.lat, latLng.lng));
    div.style.left = pos.x + 'px'; div.style.top = pos.y + 'px'; div.style.position = 'absolute';
  };
  overlay.onRemove = function(){ if(div.parentNode) div.parentNode.removeChild(div); };
  overlay.setMap(map);
  userMarker = overlay;
}


function putOriginMarker(latLng){
  if(originMarker){ originMarker.setPosition(latLng); originMarker.setMap(map); return; }
  originMarker = new google.maps.Marker({
    position: latLng,
    map,
    draggable: true,
    icon: {
      url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(`
        <svg width="42" height="42" viewBox="0 0 42 42" xmlns="http://www.w3.org/2000/svg">
          <defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#34d399"/>
            <stop offset="100%" stop-color="#10b981"/>
          </linearGradient></defs>
          <circle cx="21" cy="21" r="11" fill="url(#g)" stroke="#000" stroke-opacity=".25" stroke-width="2"/>
        </svg>
      `),
      scaledSize: new google.maps.Size(32,32)
    }
  });
  originMarker.addListener('dragend', () => {
    originLatLng = originMarker.getPosition();
    reverseGeocode(originLatLng, (addr) => {
      document.getElementById('origin-input').value = addr || '';
      tryRoute();
    });
  });
}


function putDestinationMarker(latLng, label){
  if(destinationMarker){
    destinationMarker.setPosition(latLng); destinationMarker.setMap(map);
  }else{
    destinationMarker = new google.maps.Marker({
      position: latLng,
      map,
      draggable: true,
      icon: {
        url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(`
          <svg width="46" height="46" viewBox="0 0 46 46" xmlns="http://www.w3.org/2000/svg">
            <defs><linearGradient id="g" x1="0" y="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#ffd166"/>
              <stop offset="100%" stop-color="#ff9f1c"/>
            </linearGradient></defs>
            <circle cx="23" cy="23" r="12" fill="url(#g)" stroke="#000" stroke-opacity=".25" stroke-width="2"/>
          </svg>
        `),
        scaledSize: new google.maps.Size(36,36)
      }
    });
    destinationMarker.addListener('dragend', () => {
      destLatLng = destinationMarker.getPosition();
      reverseGeocode(destLatLng, (addr) => {
        document.getElementById('dest-input').value = addr || '';
        tryRoute();
      });
    });
  }
  if(label) showLabel(latLng, label);
}


function showLabel(latLng, label){
  if(!label) return;
  const safe = (label+'').replace(/[<>&]/g, s => ({'<':'&lt;','>':'&gt;','&':'&amp;'}[s]));
  infoWindow.setContent('<div style="color:#111;font-size:13px;padding:4px 2px;">'+ safe +'</div>');
  infoWindow.setPosition(latLng);
  infoWindow.open(map);
}


function tryRoute(){
  if(!originLatLng || !destLatLng) {
    toggleCTA(false);
    directionsRenderer.set('directions', null);
    clearNeonRoute();
    return;
  }
  directionsService.route({
    origin: originLatLng,
    destination: destLatLng,
    travelMode: google.maps.TravelMode.DRIVING,
    provideRouteAlternatives: false
  }, (res, status) => {
    if(status === 'OK' && res.routes && res.routes.length){
      directionsRenderer.setDirections(res);          
      const path = res.routes[0].overview_path;       
      renderNeonRoute(path);
      toggleCTA(true);
      infoWindow.close(); 
    }else{
      console.warn('No se pudo trazar ruta', status);
      toggleCTA(false);
      directionsRenderer.set('directions', null);
      clearNeonRoute();
    }
  });
}
function toggleCTA(show){
  const btn = document.getElementById('solicitar-btn');
  btn.style.display = show ? 'inline-flex' : 'none';
}


function clearNeonRoute(){
  ['base','glow1','glow2','dash'].forEach(k => {
    if (neonRoute[k]) { neonRoute[k].setMap(null); neonRoute[k] = null; }
  });
  if (neonRoute.timer){ clearInterval(neonRoute.timer); neonRoute.timer = null; }
}

function renderNeonRoute(path){
  clearNeonRoute();

 
  neonRoute.glow1 = new google.maps.Polyline({
    map, path,
    strokeColor: '#ffb100',
    strokeOpacity: 0.18,
    strokeWeight: 9,
    clickable: false,
    zIndex: 4
  });
  neonRoute.glow2 = new google.maps.Polyline({
    map, path,
    strokeColor: '#ffb100',
    strokeOpacity: 0.32,
    strokeWeight: 7,
    clickable: false,
    zIndex: 6
  });

 
  neonRoute.base = new google.maps.Polyline({
    map, path,
    strokeColor: '#ffb100',
    strokeOpacity: 1,
    strokeWeight: 4.5,
    clickable: false,
    zIndex: 8
  });

  
  const dashSymbol = {
    path: 'M 0,-0.6 0,0.6',
    strokeOpacity: 0.92,
    strokeColor: '#fffdf2',
    scale: 1.6
  };

  neonRoute.dash = new google.maps.Polyline({
    map, path,
    strokeOpacity: 0,
    icons: [{
      icon: dashSymbol,
      offset: '0',
      repeat: '18px'
    }],
    clickable: false,
    zIndex: 12
  });

  let offset = 0;
  neonRoute.timer = setInterval(() => {
    offset = (offset + 1) % 18;
    neonRoute.dash.set('icons', [{
      icon: dashSymbol,
      offset: `${offset}px`,
      repeat: '18px'
    }]);
  }, 40);
}


function geocodeText(text, cb){
  if(!text) return;
  geocoder.geocode({ address: text, componentRestrictions: { country:'CO' } }, (res, status) => {
    if(status === 'OK' && res[0]) cb(res[0].geometry.location, res[0].formatted_address);
  });
}
function reverseGeocode(latLng, cb){
  geocoder.geocode({ location: latLng }, (res, status) => {
    if(status === 'OK' && res[0]) cb(res[0].formatted_address);
    else cb(null);
  });
}


function setupVoice(btnId, inputId, onText){
  const btn = document.getElementById(btnId);
  const input = document.getElementById(inputId);
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if(!SR){ if(btn) btn.style.display = 'none'; return; }
  const rec = new SR();
  rec.lang = 'es-CO'; rec.interimResults = false; rec.maxAlternatives = 1;

  btn.addEventListener('click', () => { try{ rec.start(); }catch(e){} btn.classList.add('listening'); });
  rec.onend = () => btn.classList.remove('listening');
  rec.onerror = () => btn.classList.remove('listening');
  rec.onresult = (evt) => {
    const txt = evt.results[0][0].transcript;
    input.value = txt;
    onText && onText(txt);
  };
}


function togglePinMode(which){
  if (pinMode === which) { disablePinMode(); return; }
  enablePinMode(which);
}
function enablePinMode(which){
  pinMode = which; 
  document.getElementById('pin-origin').classList.toggle('active', which==='origin');
  document.getElementById('pin-dest').classList.toggle('active', which==='dest');

  map.setOptions({ draggableCursor: 'crosshair' });
  if (pinClickListener) { google.maps.event.removeListener(pinClickListener); pinClickListener=null; }

  pinClickListener = map.addListener('click', (ev) => {
    const latLng = ev.latLng;
    if (pinMode === 'origin') {
      originLatLng = latLng;
      putOriginMarker(latLng);
      reverseGeocode(latLng, (addr) => {
        document.getElementById('origin-input').value = addr || '';
        showLabel(latLng, addr || 'Origen');
        tryRoute();
      });
    } else if (pinMode === 'dest') {
      destLatLng = latLng;
      putDestinationMarker(latLng, null);
      reverseGeocode(latLng, (addr) => {
        document.getElementById('dest-input').value = addr || '';
        showLabel(latLng, addr || 'Destino');
        tryRoute();
      });
    }
    disablePinMode();
  });
}
function disablePinMode(){
  pinMode = null;
  map.setOptions({ draggableCursor: null });
  if (pinClickListener) { google.maps.event.removeListener(pinClickListener); pinClickListener=null; }
  document.getElementById('pin-origin').classList.remove('active');
  document.getElementById('pin-dest').classList.remove('active');
}


function scheduleIdleRefresh(){
  clearTimeout(refreshIdleTimer);
  refreshIdleTimer = setTimeout(refreshNearbyDrivers, 350); 
}

function refreshNearbyDrivers(){
  if(!map || driversFetchInFlight) return;


  let center;
  if (originLatLng && typeof originLatLng.lat === 'function') {
    center = originLatLng;
  } else {
    center = map.getCenter();
  }

  const centerLat = typeof center.lat === 'function' ? center.lat() : center.lat;
  const centerLng = typeof center.lng === 'function' ? center.lng() : center.lng;

  driversFetchInFlight = true;
  fetch(`${API_NEARBY_URL}?lat=${centerLat}&lng=${centerLng}&r=8`, { cache: 'no-store' })
    .then(r => r.json())
    .then(list => {
      const seen = new Set();
      const z = map.getZoom() || 15;

      list.forEach(d => {
        const id = d.conductor_id;
        const pos = new google.maps.LatLng(parseFloat(d.lat), parseFloat(d.lng));
        seen.add(id);

        let obj = driverMarkers.get(id);
        if(!obj){
      
          const halo = new google.maps.Marker({
            position: pos,
            map,
            clickable: false,
            zIndex: 450,
            icon: haloIconForZoom(z)
          });
          
          const car = new google.maps.Marker({
            position: pos,
            map,
            clickable: true,
            zIndex: 500,
            icon: carIconForZoom(z),
            optimized: true
          });
          obj = { halo, car };
          driverMarkers.set(id, obj);
        } else {
          obj.halo.setPosition(pos);
          obj.car.setPosition(pos);
        }
      });

     
      for(const [id, obj] of driverMarkers){
        if(!seen.has(id)){
          if (obj.halo) obj.halo.setMap(null);
          if (obj.car) obj.car.setMap(null);
          driverMarkers.delete(id);
        }
      }
    })
    .catch(err => console.warn('nearby-drivers error', err))
    .finally(() => {
      driversFetchInFlight = false;
     
      refreshDriverIconsByZoom();
    });
}
</script>
<script>
(() => {
  const ok = document.getElementById('txp-final-ok');
  if (ok && !ok._bound){
    ok._bound = true;
    ok.addEventListener('click', resetMapUI);
  }
  const bd = document.querySelector('#txp-sheet-final [data-close]');
  if (bd && !bd._bound){
    bd._bound = true;
    bd.addEventListener('click', resetMapUI);
  }
})();
</script>
<script>
(function () {
  const C = window.Capacitor;
  const isNative = !!(C && C.isNativePlatform && C.isNativePlatform());
  console.log('[Capacitor Pasajero] isNative?', isNative, 'platform:', C?.getPlatform?.());
  if (isNative) { try { navigator.vibrate?.(10); } catch(e){} }
})();
</script>
<script type="module">
 
  import { Device } from "https://cdn.skypack.dev/@capacitor/device";
  import { PushNotifications } from "https://cdn.skypack.dev/@capacitor/push-notifications";
  import { FirebaseMessaging } from "https://cdn.skypack.dev/@capacitor-firebase/messaging";
  import { LocalNotifications } from "https://cdn.skypack.dev/@capacitor/local-notifications";

  const isNative = !!(window.Capacitor?.isNativePlatform?.() === true);


  const PUSH_REGISTER_URL = "{{ route('push.register') }}";

  async function ensureAndroidChannels(){
    if (window.Capacitor?.getPlatform?.() !== 'android') return;
    try{
      
      await LocalNotifications.createChannel({
        id: 'taxpiya_chat',
        name: 'Chat',
        description: 'Mensajes de chat (sin sonido)',
        importance: 2,    
        sound: null,
        vibration: false,
        lights: false,
        visibility: 1    
      });
      
      await LocalNotifications.createChannel({
        id: 'taxpiya_arrivals',
        name: 'Llegadas',
        description: 'Avisos de llegada del conductor',
        importance: 5,  
        sound: 'conductorllego', 
        vibration: true,
        lights: true,
        visibility: 1
      });
    } catch(e){ console.warn('[Pasajero][NotifChannel]', e); }
  }

  async function registrarTokenEnBackend(token){
    if (!token) return;
    try {
      const { identifier: device_uuid } = await Device.getId();
      const info = await Device.getInfo();
      const plataforma = (info.platform === 'android' ? 'android'
                        : info.platform === 'ios' ? 'ios' : 'web');
      const is_emulator = !!info.isVirtual;

      const res = await fetch(PUSH_REGISTER_URL, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": (typeof getCsrf === 'function')
                           ? getCsrf()
                           : document.querySelector('meta[name="csrf-token"]')?.content || ""
        },
        body: JSON.stringify({
          provider: "fcm",
          token,
          device_uuid,
          plataforma,
          is_emulator
          
        })
      });
      const j = await res.json().catch(() => ({}));
      if (!res.ok || !j.ok) console.warn("[Pasajero][FCM] register FAIL", j);
      else console.log("[Pasajero][FCM] register OK", j);
    } catch (e) {
      console.warn("[Pasajero][FCM] error enviando token", e);
    }
  }

  async function initPushPasajero(){
    
    if (!isNative) return;

    try{
      await ensureAndroidChannels();

     
      const perm = await PushNotifications.requestPermissions();
      if (perm.receive !== "granted") {
        console.warn("[Pasajero][Push] permiso denegado");
        return;
      }

     
      await PushNotifications.register();

    
      const { token } = await FirebaseMessaging.getToken();
      console.log("[Pasajero][FCM] token:", token);
      await registrarTokenEnBackend(token);

     
      FirebaseMessaging.addListener("tokenChanged", async ({ token }) => {
        console.log("[Pasajero][FCM] tokenChanged:", token);
        await registrarTokenEnBackend(token);
      });

      PushNotifications.addListener("pushNotificationReceived", (n) => {
        
        handlePushData(n?.data || {});
      });

     
      PushNotifications.addListener("pushNotificationActionPerformed", (a) => {
        const data = a?.notification?.data || a?.notification?.extra || {};
        handlePushData(data || {}, { fromTap: true });
      });

      
      document.addEventListener('resume', () => {
        try { PushNotifications.register(); } catch(_) {}
      });

    }catch(e){
      console.warn("[Pasajero][Push] init error", e);
    }
  }


  function handlePushData(data = {}, meta = {}){
    const t   = (data.t || data.type || '').toString();
    const vId = data.viaje_id ? +data.viaje_id : null;

    try { navigator.vibrate?.(20); } catch(e){}

    
    if (vId) window.currentViajeId = window.currentViajeId || vId;

    if (t === 'assigned') {
      if (typeof checkTripStateOnce === 'function') checkTripStateOnce();
      if (typeof showBanner === 'function') showBanner('Conductor asignado', 'fa-taxi');
      return;
    }

    if (t === 'arrived') {
      if (typeof showBanner === 'function') showBanner('Tu conductor ha llegado', 'fa-flag-checkered');
      if (typeof showAbordoAction === 'function') showAbordoAction(true);
      try {
        const sh = document.getElementById('txp-sheet-asignado');
        if (sh && sh.getAttribute('aria-hidden') === 'true') {
          sh.setAttribute('aria-hidden','false');
        }
      } catch(_) {}
      return;
    }

    if (t === 'chat') {
      
      if (typeof showBanner === 'function') showBanner('Nuevo mensaje del conductor', 'fa-comments');
      if (typeof openChat === 'function') openChat();
      return;
    }

    
  }

  if (isNative) initPushPasajero();
</script>



<script src="https://maps.googleapis.com/maps/api/js?key={{ config('taxpiya.google_maps_key') }}&libraries=places&callback=initMap" async defer></script>

@endsection
