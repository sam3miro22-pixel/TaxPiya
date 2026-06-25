@php $pageTitle = 'Guía por rol — TaxPiya'; @endphp
@extends('layouts.auth')
@section('title', $pageTitle)

@section('content')
<div class="txp-auth-scene txp-auth-scene--scroll">
    <div class="txp-auth-card" style="max-width:720px;text-align:left;">
        <div class="txp-auth-header">
            <x-taxpiya-logo :size="160" />
            <h1 class="txp-auth-title">Guía de <span>roles</span></h1>
            <p class="txp-auth-subtitle">Cómo funciona TaxPiya para cada tipo de usuario</p>
        </div>

        <div class="txp-guia-body" style="color:#e2e8f0;font-size:15px;line-height:1.55;">
            <p><strong>URL producción:</strong> <a href="https://taxpiya.onrender.com" style="color:#ffd166">taxpiya.onrender.com</a></p>

            <h2 class="h5 mt-4" style="color:#ffd166">Autenticación</h2>
            <ul>
                <li><strong>Pasajero y Conductor:</strong> solo Firebase (Google o correo/contraseña Firebase). Una sesión activa por cuenta.</li>
                <li><strong>Empresa y Admin:</strong> celular o correo + contraseña del servidor.</li>
                <li><strong>Cambiar contraseña:</strong> desde Mi perfil / Mi cuenta (no hay recuperación por correo por ahora).</li>
            </ul>

            <h2 class="h5 mt-4" style="color:#ffd166">1. Pasajero</h2>
            <p><strong>Login:</strong> /pasajero/login · Google o correo Firebase (demo: pasajero.demo1@taxpiya.com)</p>
            <ol>
                <li>Inicia sesión con Google o correo Firebase.</li>
                <li>En el mapa: origen, destino (texto, pin o micrófono).</li>
                <li>Confirma tarifa → busca conductor → ves burbuja GPS del taxi.</li>
                <li>Chat con conductor + asistente automático.</li>
                <li>Código de llegada de 4 dígitos cuando el conductor está en camino.</li>
                <li>Cancelar servicio, SOS, billetera NEQUI, historial de viajes.</li>
            </ol>

            <h2 class="h5 mt-4" style="color:#ffd166">2. Conductor</h2>
            <p><strong>Login:</strong> /conductor/login · Firebase (cuenta demo: conductor.demo1@taxpiya.com, tras aprobación admin)</p>
            <ol>
                <li>Firebase login → activar <strong>DISPONIBLE</strong>.</li>
                <li>Recibe solicitudes → Aceptar o Rechazar (solo antes de aceptar).</li>
                <li>Tras aceptar: no puede cancelar hasta llegar (GPS o código del pasajero).</li>
                <li>Botón ubicación (derecha) y SOS (izquierda, separados).</li>
                <li>Marcar llegada → pasajero abordo → terminar viaje.</li>
                <li>Wallet, historial, chat.</li>
            </ol>

            <h2 class="h5 mt-4" style="color:#ffd166">3. Empresa / Flota</h2>
            <p><strong>Login:</strong> /empresa/login · Demo: 3209002001</p>
            <ol>
                <li>Panel: taxis en línea, viajes, ingresos.</li>
                <li>Flota: registrar taxi, varios conductores por vehículo.</li>
                <li>Contabilidad: ingresos, movimientos wallet sincronizados con admin.</li>
                <li>Billetera: recargas y pagos a conductores.</li>
            </ol>

            <h2 class="h5 mt-4" style="color:#ffd166">4. Administrador</h2>
            <p><strong>Login:</strong> /index/login · Demo: 3001001001</p>
            <ol>
                <li>Dashboard con mapa Leaflet y KPIs en tiempo real.</li>
                <li>Usuarios, conductores, empresas, viajes, SOS.</li>
                <li>Finanzas: aprobar recargas NEQUI, movimientos, saldos.</li>
                <li>Referidos, notificaciones. Botón ← Panel en listados.</li>
            </ol>

            <h2 class="h5 mt-4" style="color:#ffd166">Demo — contraseña común</h2>
            <p><code>Taxpiya2026!</code></p>

            <p class="small text-muted mt-4 mb-0">
                Esta guía sustituye un video de demostración. Para grabar uno en tu PC: abre cada login en el navegador y sigue los pasos anteriores con OBS o la grabadora de Windows (Win+G).
            </p>
        </div>

        <div class="txp-auth-actions mt-4">
            <a href="{{ url('/') }}" class="txp-auth-btn txp-auth-btn--primary">
                <i class="fa-solid fa-house"></i> Ir a TaxPiya
            </a>
        </div>
    </div>
</div>
@endsection
