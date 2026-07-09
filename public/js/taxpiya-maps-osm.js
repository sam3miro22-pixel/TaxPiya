/**
 * TaxPiya — capa compatible con google.maps usando Leaflet + OSM (gratis).
 */
(function () {
  'use strict';

  if (typeof L === 'undefined') {
    console.error('[TaxpiyaMaps] Leaflet no cargó');
    return;
  }

  const CFG = window.TAXPIYA_MAP || {};
  const listeners = new WeakMap();
  const CO_BOUNDS = L.latLngBounds(
    L.latLng(-4.3, -82.0),
    L.latLng(13.5, -66.8)
  );

  function isInColombia(lat, lng) {
    return lat >= -4.3 && lat <= 13.5 && lng >= -82.0 && lng <= -66.8;
  }

  function toNum(v) {
    return typeof v === 'function' ? v() : +v;
  }

  function LatLng(lat, lng) {
    const la = toNum(lat);
    const ln = toNum(lng);
    return {
      lat: () => la,
      lng: () => ln,
    };
  }

  function toLatLng(o) {
    if (!o) return LatLng(0, 0);
    if (typeof o.lat === 'function') return o;
    return LatLng(o.lat, o.lng);
  }

  function toLeaflet(o) {
    const ll = toLatLng(o);
    return [ll.lat(), ll.lng()];
  }

  function pathToLeaflet(path) {
    return (path || []).map((p) => toLeaflet(p));
  }

  function Size(w, h) {
    this.width = w;
    this.height = h;
  }

  function Point(x, y) {
    this.x = x;
    this.y = y;
  }

  function Map(el, opts) {
    opts = opts || {};
    const center = opts.center || { lat: 4.711, lng: -74.0721 };
    this._map = L.map(el, {
      zoomControl: false,
      attributionControl: true,
      maxBounds: CO_BOUNDS,
      maxBoundsViscosity: 0.85,
    }).setView(toLeaflet(center), opts.zoom || 15);

    L.tileLayer(CFG.tiles || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: CFG.attribution || '&copy; OpenStreetMap',
      maxZoom: 19,
    }).addTo(this._map);

    setTimeout(() => {
      try {
        this._map.invalidateSize(true);
      } catch (e) {}
    }, 400);
    setTimeout(() => {
      try {
        this._map.invalidateSize(true);
      } catch (e) {}
    }, 1200);

    this._listeners = {};
    this.__centeredOnce = false;
  }

  Map.prototype.getCenter = function () {
    const c = this._map.getCenter();
    return LatLng(c.lat, c.lng);
  };

  Map.prototype.getZoom = function () {
    return this._map.getZoom();
  };

  Map.prototype.setZoom = function (z) {
    this._map.setZoom(z);
  };

  Map.prototype.panTo = function (latLng) {
    this._map.panTo(toLeaflet(latLng));
  };

  Map.prototype.setCenter = function (latLng) {
    this._map.setView(toLeaflet(latLng), this._map.getZoom());
  };

  Map.prototype.setOptions = function (opts) {
    if (opts && Object.prototype.hasOwnProperty.call(opts, 'draggableCursor')) {
      this._map.getContainer().style.cursor = opts.draggableCursor || '';
    }
  };

  Map.prototype.addListener = function (event, fn) {
    const self = this;
    let ev = event;
    let wrapper = fn;
    if (event === 'click') {
      ev = 'click';
      wrapper = (e) => fn({ latLng: LatLng(e.latlng.lat, e.latlng.lng) });
    } else if (event === 'zoom_changed') {
      ev = 'zoomend';
    } else if (event === 'idle') {
      ev = 'moveend';
    }
    this._map.on(ev, wrapper);
    return { remove: () => self._map.off(ev, wrapper) };
  };

  function Marker(opts) {
    opts = opts || {};
    this._iconOpts = opts.icon || null;
    this._listeners = {};
    this._create(opts);
    if (opts.map) this.setMap(opts.map);
  }

  Marker.prototype._create = function (opts) {
    const pos = toLeaflet(opts.position || { lat: 0, lng: 0 });
    const icon = this._buildIcon(this._iconOpts);
    this._marker = L.marker(pos, {
      icon,
      draggable: !!opts.draggable,
      interactive: opts.clickable !== false,
      zIndexOffset: opts.zIndex || 0,
    });
    this._marker.on('dragend', () => {
      (this._listeners.dragend || []).forEach((fn) => fn());
    });
  };

  Marker.prototype._buildIcon = function (iconOpts) {
    if (!iconOpts || !iconOpts.url) {
      return new L.Icon.Default();
    }
    const size = iconOpts.scaledSize || { width: 32, height: 32 };
    const w = size.width || 32;
    const h = size.height || 32;
    const anchor = iconOpts.anchor || { x: w / 2, y: h };
    return L.icon({
      iconUrl: iconOpts.url,
      iconSize: [w, h],
      iconAnchor: [anchor.x || w / 2, anchor.y || h],
    });
  };

  Marker.prototype.setMap = function (map) {
    if (!map) {
      this._marker.remove();
      return;
    }
    this._marker.addTo(map._map);
  };

  Marker.prototype.setPosition = function (pos) {
    this._marker.setLatLng(toLeaflet(pos));
  };

  Marker.prototype.getPosition = function () {
    const p = this._marker.getLatLng();
    return LatLng(p.lat, p.lng);
  };

  Marker.prototype.setIcon = function (iconOpts) {
    this._iconOpts = iconOpts;
    this._marker.setIcon(this._buildIcon(iconOpts));
  };

  Marker.prototype.setOpacity = function (opacity) {
    const el = this._marker.getElement();
    if (el) el.style.opacity = String(opacity ?? 1);
  };

  Marker.prototype.setZIndex = function (zIndex) {
    this._marker.setZIndexOffset(Number(zIndex) || 0);
  };

  Marker.prototype.addListener = function (event, fn) {
    if (!this._listeners[event]) this._listeners[event] = [];
    this._listeners[event].push(fn);
  };

  function Polyline(opts) {
    opts = opts || {};
    this._opts = opts;
    this._layer = L.polyline(pathToLeaflet(opts.path || []), {
      color: opts.strokeColor || '#3388ff',
      opacity: opts.strokeOpacity ?? 0.8,
      weight: opts.strokeWeight || 4,
      dashArray: opts.icons ? '8 12' : null,
    });
    if (opts.map) this.setMap(opts.map);
  }

  Polyline.prototype.setMap = function (map) {
    if (!map) {
      this._layer.remove();
      return;
    }
    this._layer.addTo(map._map);
  };

  Polyline.prototype.set = function (key, val) {
    if (key === 'icons') {
      this._opts.icons = val;
    }
  };

  Polyline.prototype.setOpacity = function (opacity) {
    this._layer.setStyle({ opacity: opacity ?? 1 });
  };

  function Circle(opts) {
    opts = opts || {};
    this._layer = L.circle(toLeaflet(opts.center), {
      radius: opts.radius || 50,
      color: opts.strokeColor || '#3388ff',
      opacity: opts.strokeOpacity ?? 0.8,
      weight: opts.strokeWeight || 1,
      fillColor: opts.fillColor || '#3388ff',
      fillOpacity: opts.fillOpacity ?? 0.2,
      interactive: !!opts.clickable,
    });
    if (opts.map) this.setMap(opts.map);
  }

  Circle.prototype.setMap = function (map) {
    if (!map) {
      this._layer.remove();
      return;
    }
    this._layer.addTo(map._map);
  };

  function Geocoder() {}

  Geocoder.prototype.geocode = function (req, cb) {
    req = req || {};
    const finish = (results, status) => {
      if (typeof cb === 'function') {
        cb(results, status);
        return;
      }
      if (status !== 'OK') return Promise.reject(new Error(status));
      return results;
    };

    if (req.location) {
      const ll = toLatLng(req.location);
      return fetch(`${CFG.reverseUrl}?lat=${ll.lat()}&lng=${ll.lng()}`, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then((r) => r.json())
        .then((j) => {
          if (!j.ok || !j.label) return finish([], 'ZERO_RESULTS');
          return finish([{ formatted_address: j.label, geometry: { location: ll } }], 'OK');
        })
        .catch(() => finish([], 'ERROR'));
    }

    const q = req.address || '';
    return fetch(`${CFG.geocodeUrl}?q=${encodeURIComponent(q)}`, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then((r) => r.json())
        .then((j) => {
          if (!j.ok || !j.results?.length) return finish([], 'ZERO_RESULTS');
          const filtered = j.results.filter((r) => isInColombia(+r.lat, +r.lng));
          if (!filtered.length) return finish([], 'ZERO_RESULTS');
          return finish(
            filtered.map((r) => ({
            formatted_address: r.label,
            geometry: { location: LatLng(r.lat, r.lng) },
          })),
          'OK'
        );
      })
      .catch(() => finish([], 'ERROR'));
  };

  function DirectionsService() {}

  DirectionsService.prototype.route = function (req, cb) {
    const o = toLatLng(req.origin);
    const d = toLatLng(req.destination);
    const url = `${CFG.routeUrl}?from_lat=${o.lat()}&from_lng=${o.lng()}&to_lat=${d.lat()}&to_lng=${d.lng()}`;

    const run = fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then((r) => r.json())
      .then((j) => {
        if (!j.ok) return { res: null, status: 'ZERO_RESULTS' };
        const path = (j.path || []).map((p) => LatLng(p.lat, p.lng));
        return {
          res: {
            routes: [
              {
                overview_path: path,
                legs: [j.leg],
              },
            ],
          },
          status: 'OK',
        };
      })
      .catch(() => ({ res: null, status: 'ERROR' }));

    if (typeof cb === 'function') {
      run.then(({ res, status }) => cb(res, status));
      return;
    }

    return run.then(({ res, status }) => {
      if (status !== 'OK' || !res) throw new Error(status || 'ROUTE_ERROR');
      return res;
    });
  };

  function DirectionsRenderer(opts) {
    opts = opts || {};
    this._opts = opts;
    this._line = null;
    this._directions = null;
    if (opts.map) this.setMap(opts.map);
  }

  DirectionsRenderer.prototype.setMap = function (map) {
    this._map = map;
    if (this._line && map) this._line.addTo(map._map);
  };

  DirectionsRenderer.prototype.setDirections = function (res) {
    this._directions = res;
    if (this._opts.suppressPolylines) return;
    if (this._line) this._line.remove();
    const path = res?.routes?.[0]?.overview_path || [];
    if (!this._map || !path.length) return;
    const po = this._opts.polylineOptions || {};
    this._line = L.polyline(pathToLeaflet(path), {
      color: po.strokeColor || '#ffd166',
      opacity: po.strokeOpacity ?? 0.95,
      weight: po.strokeWeight || 5,
    }).addTo(this._map._map);
  };

  DirectionsRenderer.prototype.set = function (key, val) {
    if (key === 'directions') {
      this._directions = val;
      if (!val && this._line) {
        this._line.remove();
        this._line = null;
      }
    }
  };

  function InfoWindow() {
    this._popup = L.popup({ closeButton: true, className: 'txp-map-popup' });
    this._pos = null;
  }

  InfoWindow.prototype.setContent = function (html) {
    this._popup.setContent(html);
  };

  InfoWindow.prototype.setPosition = function (pos) {
    this._pos = toLatLng(pos);
  };

  InfoWindow.prototype.open = function (map) {
    if (!map || !this._pos) return;
    this._popup.setLatLng(toLeaflet(this._pos)).openOn(map._map);
  };

  InfoWindow.prototype.close = function () {
    this._popup.remove();
  };

  function OverlayView() {
    this._map = null;
    this._leafletMap = null;
    this._div = null;
    this._draw = null;
    this._onAdd = null;
    this._onRemove = null;
  }

  OverlayView.prototype.onAdd = function () {};
  OverlayView.prototype.draw = function () {};
  OverlayView.prototype.onRemove = function () {};

  OverlayView.prototype._detach = function () {
    if (this._leafletMap && this._draw) {
      this._leafletMap.off('move zoom', this._draw);
    }
    this._leafletMap = null;
  };

  OverlayView.prototype.setMap = function (map) {
    this._detach();

    if (this._map && !map) {
      this.onRemove();
      if (this._div?.parentNode) this._div.parentNode.removeChild(this._div);
      this._map = null;
      return;
    }

    this._map = map;
    if (!map || !map._map) return;

    this._div = this._div || document.createElement('div');
    this.onAdd();

    const self = this;
    this._draw = function () {
      if (!self._map || !self._map._map) return;
      self.draw();
    };

    this._leafletMap = map._map;
    map._map.on('move zoom', this._draw);
    this._draw();
  };

  OverlayView.prototype.getPanes = function () {
    if (!this._map || !this._map._map) {
      return { overlayMouseTarget: document.body };
    }
    return { overlayMouseTarget: this._map._map.getContainer() };
  };

  OverlayView.prototype.getProjection = function () {
    if (!this._map || !this._map._map) return null;
    const map = this._map._map;
    return {
      fromLatLngToDivPixel: (latLng) => {
        const pt = map.latLngToContainerPoint(toLeaflet(latLng));
        return { x: pt.x, y: pt.y };
      },
    };
  };

  function Autocomplete(input, opts) {
    this._input = input;
    this._opts = opts || {};
    this._listeners = { place_changed: [] };
    this._place = null;
    this._box = document.createElement('div');
    this._box.className = 'txp-ac-list';
    this._box.style.cssText =
      'position:absolute;z-index:9999;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.25);max-height:220px;overflow:auto;display:none;';
    input.parentElement.style.position = 'relative';
    input.parentElement.appendChild(this._box);
    this._timer = null;
    input.addEventListener('input', () => this._search());
    input.addEventListener('blur', () => setTimeout(() => (this._box.style.display = 'none'), 200));
  }

  Autocomplete.prototype._search = function () {
    clearTimeout(this._timer);
    const q = (this._input.value || '').trim();
    if (q.length < 3) {
      this._box.style.display = 'none';
      return;
    }
    this._timer = setTimeout(() => {
      fetch(`${CFG.geocodeUrl}?q=${encodeURIComponent(q)}`, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then((r) => r.json())
        .then((j) => {
          this._box.innerHTML = '';
          if (!j.ok || !j.results?.length) {
            this._box.style.display = 'none';
            return;
          }
          const results = j.results.filter((r) => isInColombia(+r.lat, +r.lng));
          if (!results.length) {
            this._box.style.display = 'none';
            return;
          }
          results.forEach((r) => {
            const item = document.createElement('div');
            item.textContent = r.label;
            item.style.cssText = 'padding:10px 12px;cursor:pointer;color:#111;font-size:14px;border-bottom:1px solid #eee;';
            item.addEventListener('mousedown', (e) => {
              e.preventDefault();
              this._place = {
                formatted_address: r.label,
                name: r.name || r.label,
                geometry: { location: LatLng(r.lat, r.lng) },
              };
              this._input.value = r.label;
              this._box.style.display = 'none';
              (this._listeners.place_changed || []).forEach((fn) => fn());
            });
            this._box.appendChild(item);
          });
          this._box.style.width = this._input.offsetWidth + 'px';
          this._box.style.display = 'block';
        })
        .catch(() => {});
    }, 350);
  };

  Autocomplete.prototype.addListener = function (event, fn) {
    if (!this._listeners[event]) this._listeners[event] = [];
    this._listeners[event].push(fn);
  };

  Autocomplete.prototype.getPlace = function () {
    return this._place || {};
  };

  const eventApi = {
    removeListener: (token) => {
      if (token && typeof token.remove === 'function') token.remove();
    },
  };

  window.google = {
    maps: {
      LatLng,
      Map,
      Marker,
      Polyline,
      Circle,
      Geocoder,
      DirectionsService,
      DirectionsRenderer,
      InfoWindow,
      OverlayView,
      Size,
      Point,
      event: eventApi,
      TravelMode: { DRIVING: 'DRIVING' },
      places: {
        Autocomplete,
      },
    },
  };

  function boot() {
    const fn = window.__txpInitMap || window.initMap;
    if (typeof fn === 'function') {
      try {
        fn();
      } catch (e) {
        console.error('[TaxpiyaMaps] initMap error', e);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
