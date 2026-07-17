/**
 * TaxPiya — dictado por voz (Web Speech API + Capacitor SpeechRecognition).
 * v6 — usa addListener('partialResults') en nativo para máxima compatibilidad Android.
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
  }

  function resetAllMicButtons() {
    [...activeButtons].forEach((btn) => setListening(btn, false));
    activeButtons.clear();
    document.querySelectorAll('.mic-btn.listening').forEach((btn) => setListening(btn, false));
    const SR = speechPlugin();
    if (SR?.stop) SR.stop().catch(() => {});
    if (SR?.removeAllListeners) SR.removeAllListeners().catch(() => {});
  }

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) resetAllMicButtons();
  });
  window.addEventListener('focus', resetAllMicButtons);
  window.addEventListener('pageshow', resetAllMicButtons);

  /**
   * Escucha con el plugin nativo de Capacitor.
   * Estrategia dual:
   *   1) addListener('partialResults') → captura el texto en tiempo real (Android principal).
   *   2) promesa de start()            → respaldo para dispositivos que resuelven al terminar.
   * El primero que traiga texto gana; un timer de silencio confirma el resultado parcial.
   */
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

      // Limpiar listeners previos para evitar duplicados
      try { await SR.removeAllListeners(); } catch (_) {}

      return await new Promise((resolve) => {
        let done = false;
        let lastPartial = '';
        let silenceTimer = null;

        const finish = (text) => {
          if (done) return;
          done = true;
          clearTimeout(silenceTimer);
          // Limpiar listeners del plugin
          try { SR.removeAllListeners(); } catch (_) {}
          resolve((text || '').trim() || null);
        };

        // ── Camino 1: partialResults (el más fiable en Android) ──────────────
        SR.addListener('partialResults', (data) => {
          // El plugin puede devolver .matches (array) o .value (string)
          const text = data?.matches?.[0] || data?.value || '';
          if (text && text.trim()) {
            lastPartial = text.trim();
            // Reiniciamos el timer de silencio: si pasan 1.8s sin nueva entrada, cerramos
            clearTimeout(silenceTimer);
            silenceTimer = setTimeout(() => finish(lastPartial), 1800);
          }
        }).catch(() => {});

        // ── Camino 2: promesa directa de start() (respaldo para popup:true) ─
        SR.start({
          language: 'es-CO',
          maxResults: 5,
          prompt: 'Di la dirección',
          partialResults: true,   // activamos para que dispare el listener anterior
          popup: true,
        }).then((res) => {
          // La promesa se resuelve al cerrar el popup nativo
          const text = res?.matches?.[0] || res?.value || lastPartial || '';
          finish(text);
        }).catch(() => {
          finish(lastPartial);
        });

        // Timeout global: 20 seg máximo
        setTimeout(() => finish(lastPartial), 20000);
      });

    } catch (e) {
      console.warn('[TaxpiyaVoice] native', e);
      try { SR.removeAllListeners(); } catch (_) {}
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
      voiceToast('🎙️ Escuchando… habla ahora');

      try {
        const txt = await Promise.race([
          listen(),
          new Promise((r) => setTimeout(() => r(null), 25000)),
        ]);

        if (!txt) {
          voiceToast('No escuché nada. Intenta de nuevo.');
          return;
        }

        // Escribir en el campo de texto
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
