/**
 * TaxPiya — ubicación GPS (Capacitor Geolocation + navigator.geolocation).
 */
(function (global) {
  'use strict';

  function isNative() {
    return !!(global.Capacitor?.isNativePlatform?.());
  }

  function geoPlugin() {
    return global.Capacitor?.Plugins?.Geolocation || null;
  }

  function toPosition(pos) {
    const c = pos?.coords || pos;
    return {
      coords: {
        latitude: c.latitude,
        longitude: c.longitude,
        accuracy: c.accuracy ?? null,
      },
    };
  }

  async function requestPermission() {
    const Geo = geoPlugin();
    if (isNative() && Geo?.requestPermissions) {
      const perm = await Geo.requestPermissions();
      const ok = perm?.location === 'granted'
        || perm?.coarseLocation === 'granted'
        || perm === 'granted';
      return ok;
    }
    return true;
  }

  function getCurrentPosition(options) {
    const opts = Object.assign(
      { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 },
      options || {}
    );

    return new Promise(async (resolve, reject) => {
      const Geo = geoPlugin();
      if (isNative() && Geo?.getCurrentPosition) {
        try {
          const ok = await requestPermission();
          if (!ok) {
            reject(Object.assign(new Error('permission denied'), { code: 1 }));
            return;
          }
          const pos = await Geo.getCurrentPosition({
            enableHighAccuracy: opts.enableHighAccuracy,
            timeout: opts.timeout,
          });
          resolve(toPosition(pos));
          return;
        } catch (e) {
          reject(e);
          return;
        }
      }

      if (!global.navigator?.geolocation) {
        reject(new Error('Geolocation not supported'));
        return;
      }
      global.navigator.geolocation.getCurrentPosition(resolve, reject, opts);
    });
  }

  async function watchPosition(success, error, options) {
    const opts = Object.assign({ enableHighAccuracy: true, timeout: 20000 }, options || {});
    const Geo = geoPlugin();
    if (isNative() && Geo?.watchPosition) {
      try {
        const ok = await requestPermission();
        if (!ok) throw Object.assign(new Error('permission denied'), { code: 1 });
        return await Geo.watchPosition(
          { enableHighAccuracy: opts.enableHighAccuracy, timeout: opts.timeout },
          (pos, err) => {
            if (err) { if (error) error(err); return; }
            if (pos && success) success(toPosition(pos));
          }
        );
      } catch (e) {
        if (error) error(e);
        return null;
      }
    }
    if (!global.navigator?.geolocation) return null;
    return global.navigator.geolocation.watchPosition(success, error, opts);
  }

  function clearWatch(id) {
    const Geo = geoPlugin();
    if (isNative() && Geo?.clearWatch && id != null) {
      Geo.clearWatch({ id }).catch(() => {});
      return;
    }
    if (id != null && global.navigator?.geolocation) {
      global.navigator.geolocation.clearWatch(id);
    }
  }

  global.TxpGeo = {
    getCurrentPosition,
    watchPosition,
    clearWatch,
    requestPermission,
    isNative,
  };
})(window);
