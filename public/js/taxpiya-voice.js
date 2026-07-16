/**
 * TaxPiya — dictado por voz (Web Speech API + Capacitor SpeechRecognition).
 */
(function (global) {
  'use strict';

  const activeButtons = new Set();

  function isNative() {
    return !!(global.Capacitor?.isNativePlatform?.());
  }

  function speechPlugin() {
    return global.Capacitor?.Plugins?.SpeechRecognition || null;
  }

  function webSpeechCtor() {
    return global.SpeechRecognition || global.webkitSpeechRecognition || null;
  }

  function voiceToast(msg) {
    let el = document.getElementById('txp-voice-toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'txp-voice-toast';
      el.setAttribute('aria-live', 'polite');
      document.body.appendChild(el);
      if (!document.getElementById('txp-voice-toast-style')) {
        const s = document.createElement('style');
        s.id = 'txp-voice-toast-style';
        s.textContent = `
#txp-voice-toast{
  position:fixed;left:50%;bottom:calc(150px + env(safe-area-inset-bottom));
  transform:translateX(-50%) translateY(8px);opacity:0;
  background:rgba(17,24,39,.94);color:#fff;padding:10px 14px;border-radius:12px;
  font-size:13px;z-index:10006;pointer-events:none;max-width:min(90vw,360px);
  transition:opacity .2s,transform .2s;text-align:center;
}
#txp-voice-toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
`;
        document.head.appendChild(s);
      }
    }
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(voiceToast._t);
    voiceToast._t = setTimeout(() => el.classList.remove('show'), 3200);
  }

  function setListening(btn, on) {
    if (!btn) return;
    if (on) activeButtons.add(btn);
    else activeButtons.delete(btn);
    btn.classList.toggle('listening', on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    const ico = btn.querySelector('i');
    if (ico) {
      ico.className = on ? 'fa-solid fa-microphone' : 'fa-solid fa-microphone';
    }
  }

  function resetAllMicButtons() {
    [...activeButtons].forEach((btn) => setListening(btn, false));
    activeButtons.clear();
    document.querySelectorAll('.mic-btn.listening').forEach((btn) => setListening(btn, false));
    const SR = speechPlugin();
    if (SR?.stop) SR.stop().catch(() => {});
  }

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) resetAllMicButtons();
  });
  window.addEventListener('focus', resetAllMicButtons);
  window.addEventListener('pageshow', resetAllMicButtons);

  async function listenNative() {
    const SR = speechPlugin();
    if (!SR) return null;

    try {
      const avail = await SR.available();
      if (!avail?.available) return null;

      const perm = await SR.requestPermissions();
      const permOk = perm?.speechRecognition === 'granted'
        || perm?.microphone === 'granted'
        || perm?.speech === 'granted';
      if (!permOk) {
        voiceToast('Permite el micrófono en Ajustes → Apps → Taxpiya.');
        return null;
      }

      const res = await SR.start({
        language: 'es-CO',
        maxResults: 1,
        prompt: 'Di la dirección',
        partialResults: false,
        popup: true,
      });

      try { await SR.stop(); } catch (_) {}
      return (res?.matches?.[0] || '').trim() || null;
    } catch (e) {
      console.warn('[TaxpiyaVoice] native', e);
      try { await SR.stop(); } catch (_) {}
      return null;
    }
  }

  function listenWeb() {
    const Ctor = webSpeechCtor();
    if (!Ctor) return Promise.resolve(null);

    return new Promise((resolve) => {
      const rec = new Ctor();
      rec.lang = 'es-CO';
      rec.interimResults = false;
      rec.continuous = false;
      rec.maxAlternatives = 1;

      let done = false;
      const finish = (text) => {
        if (done) return;
        done = true;
        try { rec.abort(); } catch (_) {}
        resolve(text || null);
      };

      rec.onresult = (evt) => {
        finish(evt.results?.[0]?.[0]?.transcript?.trim() || '');
      };
      rec.onerror = (ev) => {
        if (ev.error === 'not-allowed' || ev.error === 'service-not-allowed') {
          voiceToast('Permite el micrófono en ajustes del navegador o de la app.');
        }
        finish(null);
      };
      rec.onend = () => finish(null);

      try { rec.start(); } catch (_) { finish(null); }
      setTimeout(() => finish(null), 15000);
    });
  }

  async function listen() {
    if (isNative() && speechPlugin()) {
      const nativeText = await listenNative();
      if (nativeText) return nativeText;
    }
    return listenWeb();
  }

  function bind(btnId, inputId, onText) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;

    let busy = false;
    const hasNative = isNative() && !!speechPlugin();
    const hasWeb = !!webSpeechCtor();

    btn.title = (hasNative || hasWeb) ? 'Dictar dirección' : 'Voz no disponible';

    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (busy) return;

      if (!hasNative && !hasWeb) {
        voiceToast('Dictado no disponible. Escribe la dirección o recompila la APK.');
        return;
      }

      busy = true;
      setListening(btn, true);

      try {
        const txt = await Promise.race([
          listen(),
          new Promise((r) => setTimeout(() => r(null), 25000)),
        ]);

        if (!txt) {
          voiceToast('No escuché nada. Intenta de nuevo.');
          return;
        }

        input.value = txt;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        if (typeof onText === 'function') onText(txt);
      } catch (err) {
        console.warn('[TaxpiyaVoice]', err);
        voiceToast('Error de micrófono. Revisa permisos.');
      } finally {
        busy = false;
        setListening(btn, false);
        resetAllMicButtons();
      }
    });
  }

  global.TaxpiyaVoice = {
    bind,
    listen,
    resetAll: resetAllMicButtons,
    isAvailable: () => !!(speechPlugin() || webSpeechCtor()),
    isNative,
  };
})(window);
