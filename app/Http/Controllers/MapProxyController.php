<?php

namespace App\Http\Controllers;

use App\Support\ColombiaGeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapProxyController extends Controller
{
    private function nominatimHeaders(): array
    {
        return [
            'User-Agent' => 'TaxPiya/1.0 (contacto@taxpiya.com)',
            'Accept-Language' => 'es',
        ];
    }

    public function geocode(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['ok' => false, 'message' => 'Query vacía'], 422);
        }

        try {
            $res = Http::timeout(12)
                ->withHeaders($this->nominatimHeaders())
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q'              => $q,
                    'format'         => 'json',
                    'addressdetails' => 1,
                    'limit'          => 5,
                    'countrycodes'   => 'co',
                ]);

            if (!$res->successful()) {
                return response()->json(['ok' => false, 'message' => 'Geocoder no disponible'], 502);
            }

            $items = collect($res->json())
                ->map(fn ($r) => [
                    'lat'   => (float) $r['lat'],
                    'lng'   => (float) $r['lon'],
                    'label' => $r['display_name'] ?? $q,
                    'name'  => $r['name'] ?? $q,
                ])
                ->filter(fn ($r) => ColombiaGeo::contains($r['lat'], $r['lng']))
                ->values();

            if ($items->isEmpty()) {
                return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 404);
            }

            return response()->json(['ok' => true, 'results' => $items]);
        } catch (\Throwable $e) {
            Log::warning('geocode proxy', ['err' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Error de geocodificación'], 500);
        }
    }

    public function reverse(Request $request)
    {
        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        if (!$lat && !$lng) {
            return response()->json(['ok' => false, 'message' => 'Coordenadas inválidas'], 422);
        }

        if (!ColombiaGeo::contains($lat, $lng)) {
            return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 422);
        }

        try {
            $res = Http::timeout(12)
                ->withHeaders($this->nominatimHeaders())
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'format'         => 'json',
                    'addressdetails' => 1,
                ]);

            if (!$res->successful()) {
                return response()->json(['ok' => false, 'message' => 'Reverse geocoder no disponible'], 502);
            }

            $data = $res->json();
            $country = strtolower((string) ($data['address']['country_code'] ?? ''));

            if ($country !== '' && $country !== 'co') {
                return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 422);
            }

            return response()->json([
                'ok'    => true,
                'label' => $data['display_name'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('reverse geocode proxy', ['err' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Error reverse geocode'], 500);
        }
    }

    public function route(Request $request)
    {
        $fromLat = (float) $request->query('from_lat');
        $fromLng = (float) $request->query('from_lng');
        $toLat   = (float) $request->query('to_lat');
        $toLng   = (float) $request->query('to_lng');

        if (!$fromLat || !$fromLng || !$toLat || !$toLng) {
            return response()->json(['ok' => false, 'message' => 'Ruta inválida'], 422);
        }

        if (!ColombiaGeo::contains($fromLat, $fromLng) || !ColombiaGeo::contains($toLat, $toLng)) {
            return response()->json(['ok' => false, 'message' => ColombiaGeo::rejectMessage()], 422);
        }

        $coords = "{$fromLng},{$fromLat};{$toLng},{$toLat}";
        $url    = "https://router.project-osrm.org/route/v1/driving/{$coords}";

        try {
            $res = Http::timeout(15)->get($url, [
                'overview'   => 'full',
                'geometries' => 'geojson',
                'steps'      => 'false',
            ]);

            if (!$res->successful()) {
                return response()->json(['ok' => false, 'message' => 'Router no disponible'], 502);
            }

            $data = $res->json();
            if (($data['code'] ?? '') !== 'Ok' || empty($data['routes'][0])) {
                return response()->json(['ok' => false, 'message' => 'No hay ruta'], 404);
            }

            $route = $data['routes'][0];
            $path  = [];
            foreach ($route['geometry']['coordinates'] ?? [] as $c) {
                $path[] = ['lat' => (float) $c[1], 'lng' => (float) $c[0]];
            }

            $distanceM = (float) ($route['distance'] ?? 0);
            $durationS = (float) ($route['duration'] ?? 0);

            return response()->json([
                'ok'   => true,
                'path' => $path,
                'leg'  => [
                    'distance' => [
                        'value' => (int) round($distanceM),
                        'text'  => $distanceM >= 995
                            ? round($distanceM / 1000) . ' km'
                            : number_format($distanceM / 1000, 1, '.', '') . ' km',
                    ],
                    'duration' => [
                        'value' => (int) round($durationS),
                        'text'  => max(1, (int) round($durationS / 60)) . ' min',
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('route proxy', ['err' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Error de ruta'], 500);
        }
    }
}
