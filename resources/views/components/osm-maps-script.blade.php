{{-- Mapas gratis: OpenStreetMap + Leaflet (sin API key) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script>
  window.TAXPIYA_MAP = {
    geocodeUrl: @json(url('/api/geocode')),
    reverseUrl: @json(url('/api/reverse-geocode')),
    routeUrl: @json(url('/api/route')),
    tiles: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
    attribution: '&copy; OpenStreetMap &copy; CARTO',
  };
  window.__txpInitMap = {{ $callback ?? 'initMap' }};
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="{{ secure_asset('js/taxpiya-maps-osm.js') }}?v=5"></script>
