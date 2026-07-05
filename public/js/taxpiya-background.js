/**
 * TaxPiya — segundo plano: notificación persistente (burbuja del sistema) + no marcar offline al minimizar.
 */
(function (global) {
  'use strict';

  let watcherId = null;
  let bubbleEl = null;
  let activeMode = null;

  function isNative() {
    return !!(global.Capacitor?.isNativePlatform?.());
  }

  function bgPlugin() {
    return global.Capacitor?.Plugins?.BackgroundGeolocation || null;
  }

  function ensureBubble() {
    if (bubbleEl || !isNative()) return bubbleEl;
    bubbleEl = document.createElement('div');
    bubbleEl.id = 'txp-bg-bubble';
    bubbleEl.setAttribute('aria-hidden', 'true');
    bubbleEl.innerHTML = '<span class="txp-bg-bubble__ico"><i class="fa-solid fa-taxi"></i></span><span class="txp-bg-bubble__txt">TaxPiya activo</span>';
    document.body.appendChild(bubbleEl);
    return bubbleEl;
  }

  function showBubble(label) {
    const el = ensureBubble();
    if (!el) return;
    const txt = el.querySelector('.txp-bg-bubble__txt');
    if (txt && label) txt.textContent = label;
    el.setAttribute('aria-hidden', 'false');
  }

  function hideBubble() {
    if (bubbleEl) bubbleEl.setAttribute('aria-hidden', 'true');
  }

  async function startWatcher(mode, title, message) {
    const BG = bgPlugin();
    if (!isNative() || !BG) return false;

    activeMode = mode;
    showBubble(title || 'TaxPiya activo');

    if (watcherId) return true;

    try {
      const perm = await BG.requestPermissions();
      if (perm !== 'granted' && perm?.location !== 'granted') {
        try { await BG.openSettings(); } catch (_) {}
      }

      watcherId = await BG.addWatcher({
        backgroundMessage: message || 'TaxPiya sigue activo en segundo plano',
        backgroundTitle: title || 'TaxPiya activo',
        requestPermissions: true,
        stale: false,
        distanceFilter: 40,
        stopOnTerminate: false,
        startOnBoot: false,
      }, () => {
        /* mantener servicio en primer plano */
      });
      return true;
    } catch (e) {
      console.warn('[TxpBackground] start', e);
      return false;
    }
  }

  async function stopWatcher() {
    const BG = bgPlugin();
    if (watcherId && BG?.removeWatcher) {
      try { await BG.removeWatcher({ id: watcherId }); } catch (_) {}
    }
    watcherId = null;
    activeMode = null;
    hideBubble();
  }

  function syncPasajero() {
    const id = global.currentViajeId;
    if (id) {
      startWatcher('pasajero', 'TaxPiya — viaje activo', 'Seguimos tu viaje y avisos en segundo plano');
    } else if (activeMode === 'pasajero') {
      stopWatcher();
    }
  }

  function syncConductor() {
    if (global.isOnline) {
      activeMode = 'conductor';
      showBubble('TaxPiya — disponible');
      if (!global.__txpConductorBgActive) {
        startWatcher('conductor', 'TaxPiya — disponible', 'Recibiendo solicitudes de viaje');
      }
    } else if (activeMode === 'conductor') {
      if (!global.__txpConductorBgActive) stopWatcher();
      else hideBubble();
      activeMode = null;
    }
  }

  function installAppStateListener() {
    const App = global.Capacitor?.Plugins?.App;
    if (!App?.addListener) return;

    App.addListener('appStateChange', ({ isActive }) => {
      if (isActive) {
        showBubble(activeMode === 'conductor' ? 'TaxPiya — disponible' : 'TaxPiya — viaje activo');
        if (activeMode === 'conductor' && global.isOnline) syncConductor();
        if (activeMode === 'pasajero') syncPasajero();
      }
      /* No marcar offline al minimizar */
    });
  }

  function installPasajeroHooks() {
    const origStart = global.startTripStateLoop;
    if (typeof origStart === 'function' && !origStart._txpBgHook) {
      global.startTripStateLoop = function () {
        syncPasajero();
        return origStart.apply(this, arguments);
      };
      global.startTripStateLoop._txpBgHook = true;
    }

    const origStop = global.stopTripStateLoop;
    if (typeof origStop === 'function' && !origStop._txpBgHook) {
      global.stopTripStateLoop = function () {
        const out = origStop.apply(this, arguments);
        syncPasajero();
        return out;
      };
      global.stopTripStateLoop._txpBgHook = true;
    }

    document.addEventListener('visibilitychange', () => {
      if (global.currentViajeId) syncPasajero();
    });
  }

  function installConductorHooks() {
    global.TxpBackground = global.TxpBackground || {};
    global.TxpBackground.onOnlineChanged = function (online) {
      if (online) syncConductor();
      else stopWatcher();
    };
  }

  function injectStyles() {
    if (document.getElementById('txp-bg-bubble-style')) return;
    const s = document.createElement('style');
    s.id = 'txp-bg-bubble-style';
    s.textContent = `
#txp-bg-bubble{
  position:fixed; left:16px; top:calc(160px + env(safe-area-inset-top, 0px));
  z-index:10002; display:none; align-items:center; gap:10px;
  padding:10px 14px; border-radius:999px;
  background:linear-gradient(135deg,#1c2541,#0b132b);
  border:2px solid rgba(255,209,102,.45);
  color:#fff; font-size:13px; font-weight:700;
  box-shadow:0 12px 32px rgba(0,0,0,.45);
  pointer-events:none;
}
#txp-bg-bubble[aria-hidden="false"]{ display:flex; }
.txp-bg-bubble__ico{
  width:36px;height:36px;border-radius:50%;
  display:grid;place-items:center;
  background:linear-gradient(135deg,#ffd166,#f59e0b); color:#1a1a1a;
}
`;
    document.head.appendChild(s);
  }

  function init(role) {
    injectStyles();
    if (!isNative()) return;
    installAppStateListener();
    if (role === 'pasajero') {
      installPasajeroHooks();
      syncPasajero();
    }
    if (role === 'conductor') {
      installConductorHooks();
      syncConductor();
    }
  }

  global.TxpBackground = {
    init,
    start: startWatcher,
    stop: stopWatcher,
    syncPasajero,
    syncConductor,
    isNative,
  };
})(window);
