@auth
<div id="txp-assistant-root" class="txp-assistant">
    <button type="button" id="txp-assistant-fab" class="txp-assistant-fab" aria-label="Asistente TaxPiya">
        <i class="fa-solid fa-robot"></i>
    </button>
    <div id="txp-assistant-panel" class="txp-assistant-panel" hidden>
        <div class="txp-assistant-head">
            <strong><i class="fa-solid fa-robot"></i> Asistente TaxPiya</strong>
            <button type="button" id="txp-assistant-close" class="txp-assistant-close" aria-label="Cerrar">&times;</button>
        </div>
        <div id="txp-assistant-msgs" class="txp-assistant-msgs">
            <div class="txp-assistant-bubble txp-assistant-bubble--bot">Hola, soy tu asistente. Puedo ayudarte con viajes, tarifas, billetera y soporte.</div>
        </div>
        <form id="txp-assistant-form" class="txp-assistant-form">
            @csrf
            <input type="text" id="txp-assistant-input" name="message" class="txp-assistant-input" placeholder="Escribe tu pregunta..." maxlength="2000" autocomplete="off">
            <button type="submit" class="txp-assistant-send"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<style>
.txp-assistant{position:fixed;right:16px;bottom:calc(16px + env(safe-area-inset-bottom));z-index:9998;font-family:Inter,system-ui,sans-serif}
.txp-assistant-fab{width:56px;height:56px;border-radius:50%;border:none;background:linear-gradient(135deg,#ffd166,#f59e0b);color:#1c2541;font-size:22px;box-shadow:0 8px 24px rgba(0,0,0,.35);cursor:pointer}
.txp-assistant-panel{position:absolute;right:0;bottom:68px;width:min(360px,calc(100vw - 32px));height:420px;background:#1c2541;border:1px solid rgba(255,255,255,.12);border-radius:16px;display:flex;flex-direction:column;box-shadow:0 12px 40px rgba(0,0,0,.45)}
.txp-assistant-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.1);color:#fff}
.txp-assistant-close{background:none;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1}
.txp-assistant-msgs{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:8px}
.txp-assistant-bubble{max-width:88%;padding:10px 12px;border-radius:14px;font-size:14px;line-height:1.45;color:#fff;white-space:pre-wrap}
.txp-assistant-bubble--bot{align-self:flex-start;background:rgba(255,255,255,.08)}
.txp-assistant-bubble--user{align-self:flex-end;background:#4c6ef5}
.txp-assistant-form{display:flex;gap:8px;padding:10px;border-top:1px solid rgba(255,255,255,.1)}
.txp-assistant-input{flex:1;border:1px solid rgba(255,255,255,.15);background:#0b132b;color:#fff;border-radius:10px;padding:10px 12px;font-size:14px}
.txp-assistant-send{width:42px;border:none;border-radius:10px;background:#ffd166;color:#1c2541;cursor:pointer}
.txp-assistant-typing{opacity:.7;font-size:13px;padding:4px 8px;color:#cbd5e1}
</style>
<script>
(function(){
  const root = document.getElementById('txp-assistant-root');
  if (!root || window.__txpAssistantBooted) return;
  window.__txpAssistantBooted = true;
  const fab = document.getElementById('txp-assistant-fab');
  const panel = document.getElementById('txp-assistant-panel');
  const closeBtn = document.getElementById('txp-assistant-close');
  const msgs = document.getElementById('txp-assistant-msgs');
  const form = document.getElementById('txp-assistant-form');
  const input = document.getElementById('txp-assistant-input');
  const sendUrl = @json(route('assistant.send'));
  const msgsUrl = @json(route('assistant.messages'));
  let loaded = false;

  function bubble(text, who){
    const d = document.createElement('div');
    d.className = 'txp-assistant-bubble ' + (who === 'user' ? 'txp-assistant-bubble--user' : 'txp-assistant-bubble--bot');
    d.textContent = text;
    msgs.appendChild(d);
    msgs.scrollTop = msgs.scrollHeight;
  }

  async function loadHistory(){
    if (loaded) return;
    try {
      const r = await fetch(msgsUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      if (!r.ok) return;
      const data = await r.json();
      if (data.ok && Array.isArray(data.messages) && data.messages.length) {
        msgs.innerHTML = '';
        data.messages.forEach(m => bubble(m.mensaje, m.rol === 'user' ? 'user' : 'bot'));
      }
      loaded = true;
    } catch (e) {}
  }

  fab?.addEventListener('click', () => {
    panel.hidden = !panel.hidden;
    if (!panel.hidden) { loadHistory(); input?.focus(); }
  });
  closeBtn?.addEventListener('click', () => { panel.hidden = true; });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = (input.value || '').trim();
    if (!text) return;
    bubble(text, 'user');
    input.value = '';
    const typing = document.createElement('div');
    typing.className = 'txp-assistant-typing';
    typing.textContent = 'Escribiendo...';
    msgs.appendChild(typing);
    msgs.scrollTop = msgs.scrollHeight;
    try {
      const body = new FormData(form);
      body.set('message', text);
      const r = await fetch(sendUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body
      });
      const data = await r.json();
      typing.remove();
      if (!r.ok || data.ok !== true) {
        bubble(data.message || 'No pude responder. Intenta de nuevo.', 'bot');
        return;
      }
      bubble(data.reply || 'En que puedo ayudarte?', 'bot');
    } catch (err) {
      typing.remove();
      bubble('Error de conexion. Intenta de nuevo.', 'bot');
    }
  });
})();
</script>
@endauth
