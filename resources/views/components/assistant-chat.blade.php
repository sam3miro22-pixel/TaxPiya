@auth
<div id="txp-assistant-root" class="txp-assistant">
    <button type="button" id="txp-assistant-fab" class="txp-assistant-fab" aria-label="Asistente TaxPiya">
        <i class="fa-solid fa-robot"></i>
    </button>
    <div id="txp-assistant-panel" class="txp-assistant-panel" hidden>
        <div class="txp-assistant-head">
            <div style="display:flex;align-items:center;gap:8px;">
                <span id="txp-mode-icon">🤖</span>
                <strong id="txp-mode-label">Asistente TaxPiya</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <button type="button" id="txp-human-btn" class="txp-human-toggle" title="Soporte Humano por WhatsApp">
                    <span id="txp-human-icon">💬</span> <span id="txp-human-text">Soporte</span>
                </button>
                <button type="button" id="txp-assistant-close" class="txp-assistant-close" aria-label="Cerrar">&times;</button>
            </div>
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
.txp-assistant-panel{position:absolute;right:0;bottom:68px;width:min(360px,calc(100vw - 32px));height:440px;background:#1c2541;border:1px solid rgba(255,255,255,.12);border-radius:16px;display:flex;flex-direction:column;box-shadow:0 12px 40px rgba(0,0,0,.45)}
.txp-assistant-head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;gap:6px}
.txp-assistant-close{background:none;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1}
.txp-assistant-msgs{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:8px}
.txp-assistant-bubble{max-width:88%;padding:10px 12px;border-radius:14px;font-size:14px;line-height:1.45;color:#fff;white-space:pre-wrap}
.txp-assistant-bubble--bot{align-self:flex-start;background:rgba(255,255,255,.08)}
.txp-assistant-bubble--user{align-self:flex-end;background:#4c6ef5}
.txp-assistant-form{display:flex;gap:8px;padding:10px;border-top:1px solid rgba(255,255,255,.1)}
.txp-assistant-input{flex:1;border:1px solid rgba(255,255,255,.15);background:#0b132b;color:#fff;border-radius:10px;padding:10px 12px;font-size:14px}
.txp-assistant-send{width:42px;border:none;border-radius:10px;background:#ffd166;color:#1c2541;cursor:pointer}
.txp-assistant-typing{opacity:.7;font-size:13px;padding:4px 8px;color:#cbd5e1}
.txp-human-toggle{background:rgba(37,211,102,.15);border:1px solid rgba(37,211,102,.4);border-radius:8px;color:#25D366;font-size:12px;padding:4px 10px;cursor:pointer;white-space:nowrap;transition:background .2s}
.txp-human-toggle:hover{background:rgba(37,211,102,.3)}
.txp-human-toggle.active{background:#25D366;color:#fff;border-color:#25D366}
.txp-mode-banner{padding:6px 14px;font-size:12px;text-align:center;background:rgba(37,211,102,.1);color:#86efac;border-bottom:1px solid rgba(37,211,102,.2);display:none}
.txp-mode-banner.show{display:block}
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
  const humanBtn = document.getElementById('txp-human-btn');
  const humanIcon = document.getElementById('txp-human-icon');
  const humanText = document.getElementById('txp-human-text');
  const modeIcon = document.getElementById('txp-mode-icon');
  const modeLabel = document.getElementById('txp-mode-label');

  const sendUrl = @json(route('assistant.send'));
  const humanUrl = @json(route('assistant.human-support'));
  const msgsUrl = @json(route('assistant.messages'));
  let loaded = false;
  let humanMode = false;

  function bubble(text, who){
    const d = document.createElement('div');
    d.className = 'txp-assistant-bubble ' + (who === 'user' ? 'txp-assistant-bubble--user' : 'txp-assistant-bubble--bot');
    d.textContent = text;
    msgs.appendChild(d);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function setMode(human) {
    humanMode = human;
    if (human) {
      humanBtn.classList.add('active');
      humanIcon.textContent = '🟢';
      humanText.textContent = 'IA';
      modeIcon.textContent = '💬';
      modeLabel.textContent = 'Soporte Humano';
      input.placeholder = 'Escribe tu mensaje de soporte...';
      bubble('📲 Modo soporte humano activado. Tu siguiente mensaje será enviado por WhatsApp a nuestro equipo de soporte.', 'bot');
    } else {
      humanBtn.classList.remove('active');
      humanIcon.textContent = '💬';
      humanText.textContent = 'Soporte';
      modeIcon.textContent = '🤖';
      modeLabel.textContent = 'Asistente TaxPiya';
      input.placeholder = 'Escribe tu pregunta...';
      bubble('🤖 Volviste al asistente IA. ¿En qué puedo ayudarte?', 'bot');
    }
  }

  humanBtn?.addEventListener('click', () => setMode(!humanMode));

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
    typing.textContent = humanMode ? 'Enviando a soporte...' : 'Escribiendo...';
    msgs.appendChild(typing);
    msgs.scrollTop = msgs.scrollHeight;

    const targetUrl = humanMode ? humanUrl : sendUrl;

    try {
      const body = new FormData(form);
      body.set('message', text);
      const r = await fetch(targetUrl, {
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
      bubble(data.reply || '¿En qué puedo ayudarte?', 'bot');
      // After sending to human support, optionally switch back to AI mode
      if (humanMode) {
        setTimeout(() => setMode(false), 4000);
      }
    } catch (err) {
      typing.remove();
      bubble('Error de conexión. Intenta de nuevo.', 'bot');
    }
  });
})();
</script>
@endauth
