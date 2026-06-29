/**
 * TaxPiya — dictado por voz (Web Speech API + Capacitor SpeechRecognition).
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
    alert(msg);
  }

  function setListening(btn, on) {
    if (!btn) return;
    btn.classList.toggle('listening', on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  }

  async function listenNative() {
    const SR = speechPlugin();
    if (!SR) return null;

    try {
      const avail = await SR.available();
      if (!avail?.available) return null;

      const perm = await SR.requestPermissions();
      if (perm?.speechRecognition !== 'granted' && perm?.microphone !== 'granted') {
        throw new Error('permission');
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

        partialListener = await SR.addListener('partialResults', (data) => {
          if (data?.matches?.[0]) text = data.matches[0];
        });

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

        try {
          await SR.start({
            language: 'es-ES',
            maxResults: 1,
            prompt: 'Di la dirección',
            partialResults: true,
            popup: true,
          });
        } catch (e) {
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
        try { rec.stop(); } catch (_) {}
        resolve(text || null);
      };

      rec.onresult = (evt) => {
        const txt = evt.results?.[0]?.[0]?.transcript?.trim() || '';
        finish(txt);
      };
      rec.onerror = (ev) => {
        if (ev.error === 'not-allowed' || ev.error === 'service-not-allowed') {
          toast('Permite el micrófono en ajustes del navegador o de la app.');
        } else if (ev.error !== 'aborted' && ev.error !== 'no-speech') {
          console.warn('[TaxpiyaVoice] web error', ev.error);
        }
        finish(null);
      };
      rec.onend = () => finish(null);

      try {
        rec.start();
      } catch (e) {
        console.warn('[TaxpiyaVoice] web start', e);
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
    return listenWeb();
  }

  /**
   * @param {string} btnId
   * @param {string} inputId
   * @param {(text:string)=>void} onText
   */
  function bind(btnId, inputId, onText) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;

    let busy = false;
    const hasNative = isNative() && !!speechPlugin();
    const hasWeb = !!webSpeechCtor();

    if (!hasNative && !hasWeb) {
      btn.title = 'Voz no disponible — escribe la dirección';
    } else {
      btn.title = 'Dictar dirección';
    }

    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (busy) return;

      if (!hasNative && !hasWeb) {
        toast('El dictado por voz no está disponible aquí. Escribe la dirección o actualiza la app TaxPiya.');
        return;
      }

      busy = true;
      setListening(btn, true);

      try {
        const txt = await listen();
        if (!txt) {
          toast('No escuché nada. Intenta de nuevo.');
          return;
        }
        input.value = txt;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        if (typeof onText === 'function') onText(txt);
      } catch (err) {
        console.warn('[TaxpiyaVoice]', err);
        toast('No se pudo usar el micrófono. Revisa permisos.');
      } finally {
        busy = false;
        setListening(btn, false);
      }
    });
  }

  global.TaxpiyaVoice = { bind, listen, isAvailable: () => !!speechPlugin() || !!webSpeechCtor() };
})(window);
