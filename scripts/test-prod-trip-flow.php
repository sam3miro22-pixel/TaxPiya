<?php
/**
 * Prueba flujo pasajero solicita → conductor ve solicitud.
 * Uso: php scripts/test-prod-trip-flow.php [base_url]
 */
$base = rtrim($argv[1] ?? 'https://taxpiya.onrender.com', '/');

function txp_session(string $base, string $loginPath, string $username, string $app): array
{
    $cookie = tempnam(sys_get_temp_dir(), 'txp');
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 90,
    ]);
    curl_setopt($ch, CURLOPT_URL, "{$base}{$loginPath}");
    $html = curl_exec($ch);
    preg_match('/name="_token"\s+value="([^"]+)"/', (string) $html, $m);
    curl_setopt($ch, CURLOPT_URL, "{$base}/auth/login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => 'Taxpiya2026!',
        'app'      => $app,
        '_token'   => $m[1] ?? '',
    ]));
    curl_exec($ch);
    curl_setopt($ch, CURLOPT_URL, "{$base}/home");
    curl_setopt($ch, CURLOPT_POST, false);
    $home = curl_exec($ch);
    preg_match('/meta name="csrf-token" content="([^"]+)"/', (string) $home, $m2);
    curl_close($ch);
    return ['cookie' => $cookie, 'csrf' => $m2[1] ?? ''];
}

function txp_post(string $url, array $payload, string $cookie, string $csrf): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-CSRF-TOKEN: ' . $csrf,
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 90,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => (string) $body];
}

function txp_get(string $url, string $cookie): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_HTTPHEADER     => ['Accept: application/json', 'X-Requested-With: XMLHttpRequest'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 90,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => (string) $body];
}

echo "=== Trip flow test ===\n";

$driver = txp_session($base, '/conductor/login', '3109001001', 'conductor');
$pos = txp_post("{$base}/conductor/posicion", [
    'lat' => 6.2476,
    'lng' => -75.5658,
], $driver['cookie'], $driver['csrf']);
echo "conductor posicion: {$pos['code']} {$pos['body']}\n";
$disp = txp_post("{$base}/conductor/disponible", ['disponible' => true], $driver['cookie'], $driver['csrf']);
echo "conductor disponible: {$disp['code']} {$disp['body']}\n";

$passenger = txp_session($base, '/pasajero/login', '3009001001', 'pasajero');
$sol = txp_post("{$base}/viaje/solicitar", [
    'categoria' => 'taxi',
    'ciudad'    => 'Medellín',
    'o_lat'     => 6.2480,
    'o_lng'     => -75.5660,
    'o_txt'     => 'Origen demo Medellín',
    'd_lat'     => 6.2600,
    'd_lng'     => -75.5900,
    'd_txt'     => 'Destino demo Medellín',
], $passenger['cookie'], $passenger['csrf']);
echo "pasajero solicitar: {$sol['code']} {$sol['body']}\n";

sleep(2);
$offer = txp_get("{$base}/conductor/solicitud", $driver['cookie']);
echo "conductor solicitud: {$offer['code']} {$offer['body']}\n";

@unlink($driver['cookie']);
@unlink($passenger['cookie']);
