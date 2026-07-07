/**
 * TaxPiya — dictado por voz (Web Speech API + Capacitor SpeechRecognition).
 *
 * Estrategia (Android nativo con plugin Capacitor):
 *   1. Si corre en Capacitor nativo Y el plugin SpeechRecognition está disponible → usa plugin nativo.
 *   2. Si no → usa Web Speech API (webkitSpeechRecognition).
 *   3. Si ninguna opción funciona → muestra mensaje de error con instrucción.
 */
(function (global) {
  'use strict';

  function isNative() {
    return !!(global.Capacitor?.isNativePlatform?.());
  }

  function speechPlugin() {
    return global.Capacitor?.Plugins?.SpeechRecognition || null;
  }

  function webSpeechCtor() {
    return global.SpeechRecognition || global.webkitSpeechRecognition || null;
  }

  function toast(msg) {
    if (typeof global.showBanner === 'function') {
      global.showBanner(msg, 'fa-microphone');
      return;
    }
    // fallback: alert only if no banner system
    const banner = document.getElementById('txp-banner');
    if (banner) {
      const txt = document.getElementById('txp-banner-txt');
      const ico = document.getElementById('txp-banner-ico');
      if (txt) txt.textContent = msg;
      if (ico) { ico.className = 'fa-solid fa-microphone me-2'; }
      banner.classList.add('show');
      setTimeout(() => banner.classList.remove('show'), 4000);
    }
  }

  function setListening(btn, on) {
    if (!btn) return;
    btn.classList.toggle('listening', on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    const ico = btn.querySelector('i');
    if (ico) {
      ico.className = on
        ? 'fa-solid fa-circle-stop'
        : 'fa-solid fa-microphone';
    }
  }

  async function listenNative() {
    const SR = speechPlugin();
    if (!SR) return null;

    try {
      const avail = await SR.available();
      if (!avail?.available) return null;

      const perm = await SR.requestPermissions();
      // Accept both 'granted' states
      const permOk = perm?.speechRecognition === 'granted'
                  || perm?.microphone === 'granted'
                  || perm?.speech === 'granted';
      if (!permOk) {
        toast('Permite el micrófono en ajustes de la app para usar voz.');
        return null;
      }

      let text = '';
      let partialListener = null;
      let stateListener = null;

      const cleanup = async () => {
        try { await SR.stop(); } catch (_) {}
        try { await partialListener?.remove(); } catch (_) {}
        try { await stateListener?.remove(); } catch (_) {}
      };

      return await new Promise(async (resolve) => {
        let settled = false;
        const finish = async (value) => {
          if (settled) return;
          settled = true;
          await cleanup();
          resolve(value);
        };

        try {
          partialListener = await SR.addListener('partialResults', (data) => {
            if (data?.matches?.[0]) text = data.matches[0];
          });
        } catch (_) {}

        try {
          stateListener = await SR.addListener('listeningState', async (data) => {
            if (data?.isListening === false) {
              if (!text && SR.getResults) {
                try {
                  const res = await SR.getResults();
                  text = res?.matches?.[0] || text;
                } catch (_) {}
              }
              finish((text || '').trim() || null);
            }
          });
        } catch (_) {}

        try {
          await SR.start({
            language: 'es-CO',
            maxResults: 1,
            prompt: 'Di la dirección',
            partialResults: true,
            popup: true,
          });
        } catch (e) {
          console.warn('[TaxpiyaVoice] native start error', e);
          finish(null);
          return;
        }

        setTimeout(() => finish((text || '').trim() || null), 20000);
      });
    } catch (e) {
      console.warn('[TaxpiyaVoice] native', e);
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
        const txt = evt.results?.[0]?.[0]?.transcript?.trim() || '';
        finish(txt);
      };
      rec.onerror = (ev) => {
        if (ev.error === 'not-allowed' || ev.error === 'service-not-allowed') {
          toast('Permite el micrófono. Ve a Ajustes → Apps → Taxpiya → Permisos.');
        } else if (ev.error === 'network') {
          toast('Necesitas conexión a internet para el dictado por voz.');
        } else if (ev.error !== 'aborted' && ev.error !== 'no-speech') {
          console.warn('[TaxpiyaVoice] web error', ev.error);
        }
        finish(null);
      };
      rec.onend = () => finish(null);

      try {
        rec.start();
      } catch (e) {
        console.warn('[TaxpiyaVoice] web start error', e);
        finish(null);
      }

      setTimeout(() => finish(null), 15000);
    });
  }

  async function listen() {
    if (isNative() && speechPlugin()) {
      const nativeText = await listenNative();
      if (nativeText) return nativeText;
    }
    const webText = await listenWeb();
    if (webText) return webText;
    if (isNative() && !speechPlugin()) {
      toast('Micrófono nativo no disponible. Recompila la APK con prepare-android.bat');
    }
    return null;
  }

  /**
   * @param {string} btnId   - ID del botón de micrófono
   * @param {string} inputId - ID del campo de texto donde escribir
   * @param {(text:string)=>void} onText - callback con el texto reconocido
   */
  function bind(btnId, inputId, onText) {
    const btn   = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;

    let busy = false;
    const hasNative = isNative() && !!speechPlugin();
    const hasWeb    = !!webSpeechCtor();

    if (!hasNative && !hasWeb) {
      btn.title = 'Voz no disponible — escribe la dirección';
      btn.style.opacity = '0.5';
    } else {
      btn.title = 'Dictar dirección';
    }

    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (busy) return;

      if (!hasNative && !hasWeb) {
        toast('El dictado por voz no está disponible. Actualiza la app TaxPiya o escribe la dirección manualmente.');
        return;
      }

      busy = true;
      setListening(btn, true);
      toast('🎤 Escuchando… Di la dirección');

      try {
        const txt = await listen();

        if (!txt) {
          toast('No escuché nada. Intenta de nuevo o escribe la dirección.');
          return;
        }

        // Escribe inmediatamente al input
        input.value = txt;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();

        // Notifica al callback para geocodificación
        if (typeof onText === 'function') onText(txt);

      } catch (err) {
        console.warn('[TaxpiyaVoice]', err);
        toast('No se pudo usar el micrófono. Revisa permisos en Ajustes → Apps → Taxpiya.');
      } finally {
        busy = false;
        setListening(btn, false);
      }
    });
  }

  global.TaxpiyaVoice = {
    bind,
    listen,
    isAvailable: () => !!(speechPlugin() || webSpeechCtor()),
    isNative,
  };
})(window);
