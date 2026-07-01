@extends('layouts.app')
@section('title', 'WhatsApp - Panel de Administración')

@section('content')
<div style="padding:24px;max-width:900px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;">
        <span style="font-size:40px;">💬</span>
        <div>
            <h1 style="margin:0;font-size:26px;font-weight:700;color:#fff;">WhatsApp Soporte</h1>
            <p style="margin:4px 0 0;color:#94a3b8;font-size:14px;">Conecta un número de WhatsApp para redirigir el soporte humano desde el chat de la app.</p>
        </div>
    </div>

    @if(session('saved'))
        <div style="background:#14532d;border:1px solid #16a34a;border-radius:10px;padding:12px 18px;margin-bottom:20px;color:#bbf7d0;">
            {{ session('saved') }}
        </div>
    @endif

    {{-- STATUS CARD --}}
    <div id="wa-status-card" style="background:#1e293b;border:1px solid #334155;border-radius:16px;padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
            <div id="wa-dot" style="width:14px;height:14px;border-radius:50%;background:#64748b;flex-shrink:0;"></div>
            <div>
                <div id="wa-status-label" style="font-size:17px;font-weight:600;color:#fff;">Verificando estado...</div>
                <div id="wa-status-sub" style="font-size:13px;color:#94a3b8;margin-top:2px;"></div>
            </div>
            <div style="margin-left:auto;display:flex;gap:8px;">
                <button onclick="refreshStatus()" id="btn-refresh" style="background:#334155;border:none;border-radius:8px;padding:8px 14px;color:#cbd5e1;cursor:pointer;font-size:13px;">
                    🔄 Actualizar
                </button>
                <button onclick="doLogout()" id="btn-logout" style="background:#7f1d1d;border:none;border-radius:8px;padding:8px 14px;color:#fca5a5;cursor:pointer;font-size:13px;display:none;">
                    🚪 Desconectar
                </button>
            </div>
        </div>

        {{-- QR CODE AREA --}}
        <div id="qr-container" style="display:none;text-align:center;padding:20px 0;">
            <p style="color:#94a3b8;font-size:13px;margin-bottom:16px;">
                📲 Abre WhatsApp → Dispositivos vinculados → Vincular un dispositivo → Escanea el QR
            </p>
            <div style="display:inline-block;background:#fff;border-radius:16px;padding:16px;">
                <img id="qr-img" src="" alt="Código QR" style="width:220px;height:220px;display:block;">
            </div>
            <p style="color:#fbbf24;font-size:12px;margin-top:12px;">⏳ El código se renueva cada 20 segundos. Si expira, espera y haz clic en Actualizar.</p>
        </div>

        {{-- CONNECTED USER INFO --}}
        <div id="connected-info" style="display:none;background:#052e16;border:1px solid #16a34a;border-radius:10px;padding:14px 18px;">
            <div style="color:#86efac;font-size:14px;">
                ✅ WhatsApp conectado como: <strong id="wa-user-number" style="font-size:16px;"></strong>
            </div>
        </div>
    </div>

    {{-- CONFIG CARD --}}
    <div style="background:#1e293b;border:1px solid #334155;border-radius:16px;padding:24px;margin-bottom:24px;">
        <h2 style="font-size:17px;font-weight:600;color:#fff;margin:0 0 16px;">📞 Número de Soporte Humano</h2>
        <p style="color:#94a3b8;font-size:13px;margin-bottom:16px;">
            Cuando un usuario solicite "Soporte Humano" en el chat de la app, el mensaje se redirigirá a este número de WhatsApp.
        </p>

        <form method="POST" action="{{ route('admin.whatsapp.config') }}">
            @csrf
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:220px;">
                    <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">
                        Número de WhatsApp (con código país, sin +)
                    </label>
                    <input name="support_phone" type="tel"
                        value="{{ old('support_phone', $supportPhone) }}"
                        placeholder="Ej: 573001234567"
                        style="width:100%;background:#0f172a;border:1px solid #475569;border-radius:8px;padding:10px 14px;color:#fff;font-size:15px;box-sizing:border-box;">
                    @error('support_phone')
                        <div style="color:#f87171;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit"
                    style="background:linear-gradient(135deg,#25D366,#128C7E);border:none;border-radius:8px;padding:10px 22px;color:#fff;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">
                    💾 Guardar
                </button>
            </div>
        </form>
    </div>

    {{-- INFO CARD --}}
    <div style="background:#1e1b4b;border:1px solid #4338ca;border-radius:16px;padding:20px;">
        <h3 style="font-size:15px;font-weight:600;color:#a5b4fc;margin:0 0 10px;">ℹ️ Cómo funciona</h3>
        <ul style="color:#c7d2fe;font-size:13px;line-height:2;margin:0;padding-left:20px;">
            <li>Esta sección usa <strong>Baileys</strong> (WhatsApp Web API no oficial) sin costo y sin API de Meta.</li>
            <li>Escanea el código QR con tu WhatsApp para vincular el número de soporte.</li>
            <li>La sesión se guarda de forma persistente en el servidor. No necesitas escanear cada vez.</li>
            <li>Cuando un pasajero o conductor hace clic en "Soporte Humano" en el chat de la app, el mensaje llega directamente a este WhatsApp.</li>
            <li>Si el servicio se desconecta, se reconecta automáticamente.</li>
        </ul>
    </div>
</div>

<script>
const statusUrl = @json(route('admin.whatsapp.status'));
const logoutUrl = @json(route('admin.whatsapp.logout'));

const dot = document.getElementById('wa-dot');
const label = document.getElementById('wa-status-label');
const sub = document.getElementById('wa-status-sub');
const qrContainer = document.getElementById('qr-container');
const qrImg = document.getElementById('qr-img');
const connectedInfo = document.getElementById('connected-info');
const userNumber = document.getElementById('wa-user-number');
const btnLogout = document.getElementById('btn-logout');

async function refreshStatus() {
    try {
        const r = await fetch(statusUrl, {credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        const data = await r.json();
        applyStatus(data);
    } catch(e) {
        label.textContent = 'Error al contactar el servicio';
        dot.style.background = '#ef4444';
    }
}

function applyStatus(data) {
    qrContainer.style.display = 'none';
    connectedInfo.style.display = 'none';
    btnLogout.style.display = 'none';

    const s = data.status || 'unavailable';
    if (s === 'connected') {
        dot.style.background = '#22c55e';
        label.textContent = '✅ WhatsApp Conectado';
        sub.textContent = 'El servicio está activo y listo para enviar mensajes.';
        connectedInfo.style.display = 'block';
        userNumber.textContent = data.user || '';
        btnLogout.style.display = 'inline-block';
    } else if (s === 'qr') {
        dot.style.background = '#f59e0b';
        label.textContent = '📲 Escanea el código QR';
        sub.textContent = 'El servicio está esperando que vincules un WhatsApp.';
        if (data.qrImage) {
            qrImg.src = data.qrImage;
            qrContainer.style.display = 'block';
        }
    } else if (s === 'connecting') {
        dot.style.background = '#3b82f6';
        label.textContent = '⏳ Conectando...';
        sub.textContent = 'El servicio está iniciando. Espera unos segundos.';
    } else {
        dot.style.background = '#64748b';
        label.textContent = '❌ Servicio no disponible';
        sub.textContent = data.error || 'No se puede contactar el servicio de WhatsApp.';
    }
}

async function doLogout() {
    if (!confirm('¿Seguro que quieres desconectar el WhatsApp? Tendrás que volver a escanear el QR.')) return;
    try {
        await fetch(logoutUrl, {method:'POST', credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'}});
        setTimeout(refreshStatus, 2000);
    } catch(e) {}
}

// Poll every 8 seconds for QR updates
refreshStatus();
setInterval(refreshStatus, 8000);
</script>
@endsection
