<?php
/**
 * Prueba POST /viaje/solicitar en producción.
 */
$base = rtrim($argv[1] ?? 'https://taxpiya.onrender.com', '/');
$cookie = tempnam(sys_get_temp_dir(), 'txp');

function txp_request(string $url, array $opts, string $cookie): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 90,
    ] + $opts);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => (string) $body];
}

echo "=== Test viaje/solicitar ===\n";
$loginPage = txp_request("{$base}/pasajero/login", [], $cookie);
preg_match('/name="_token"\s+value="([^"]+)"/', $loginPage['body'], $m);
txp_request("{$base}/auth/login", [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'username' => '3009001001',
        'password' => 'Taxpiya2026!',
        'app'      => 'pasajero',
        '_token'   => $m[1] ?? '',
    ]),
], $cookie);

$home = txp_request("{$base}/home", [], $cookie);
preg_match('/meta name="csrf-token" content="([^"]+)"/', $home['body'], $m2);
$csrf = $m2[1] ?? '';

$payload = json_encode([
    'categoria' => 'taxi',
    'ciudad'    => 'Medellín',
    'o_lat'     => 6.2442,
    'o_lng'     => -75.5812,
    'o_txt'     => 'Calle 85A, Medellín',
    'd_lat'     => 4.6097,
    'd_lng'     => -74.0817,
    'd_txt'     => 'San Jose, Marsella',
]);

$resp = txp_request("{$base}/viaje/solicitar", [
    CURLOPT_POST       => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . $csrf,
    ],
    CURLOPT_POSTFIELDS => $payload,
], $cookie);

echo "HTTP {$resp['code']}\n{$resp['body']}\n";
@unlink($cookie);
