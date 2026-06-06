@php
    $mapsKey = config('taxpiya.google_maps_key');
    $mapsLibraries = $libraries ?? '';
    $mapsCallback = $callback ?? 'initMap';
@endphp
@if($mapsKey)
<script>
window.__txpMapsFailed = function (reason) {
  var el = document.getElementById('map');
  if (!el || el.querySelector('.txp-map-error')) return;
  el.innerHTML = '<div class="txp-map-error"><div class="box"><strong>Mapa no disponible</strong><p class="mb-0 mt-2 small">' +
    (reason || 'No se pudo cargar Google Maps. Verifica la API key y el dominio taxpiya.onrender.com en Google Cloud.') +
    '</p></div></div>';
};
window.gm_authFailure = function () {
  window.__txpMapsFailed('Clave de Google Maps inválida o dominio no autorizado.');
};
setTimeout(function () {
  if (typeof google === 'undefined' || !google.maps) {
    window.__txpMapsFailed('Google Maps no respondió. Revisa conexión o restricciones de la API key.');
  }
}, 12000);
</script>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}{{ $mapsLibraries ? '&libraries=' . $mapsLibraries : '' }}&callback={{ $mapsCallback }}&loading=async&v=weekly">
</script>
@endif
