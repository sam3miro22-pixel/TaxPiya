<?php
/**
 * Prueba ciclo completo: solicitar → aceptar → llego → terminar.
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

function txp_json(string $method, string $url, ?array $payload, string $cookie, string $csrf): array
{
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'X-CSRF-TOKEN: ' . $csrf];
    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POSTFIELDS     => $payload !== null ? json_encode($payload) : null,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 90,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => (string) $body, 'json' => json_decode((string) $body, true)];
}

echo "=== Trip lifecycle test ===\n";

$driver = txp_session($base, '/conductor/login', '3109001001', 'conductor');
$passenger = txp_session($base, '/pasajero/login', '3009001001', 'pasajero');

$steps = [];

$steps[] = ['conductor disponible', txp_json('POST', "{$base}/conductor/disponible", ['disponible' => true], $driver['cookie'], $driver['csrf'])];
$steps[] = ['conductor posicion', txp_json('POST', "{$base}/conductor/posicion", ['lat' => 6.2476, 'lng' => -75.5658], $driver['cookie'], $driver['csrf'])];

$sol = txp_json('POST', "{$base}/viaje/solicitar", [
    'categoria' => 'taxi',
    'ciudad'    => 'Medellín',
    'o_lat'     => 6.2480,
    'o_lng'     => -75.5660,
    'o_txt'     => 'Origen test',
    'd_lat'     => 6.2600,
    'd_lng'     => -75.5900,
    'd_txt'     => 'Destino test',
], $passenger['cookie'], $passenger['csrf']);
$steps[] = ['pasajero solicitar', $sol];
$viajeId = (int) ($sol['json']['viaje_id'] ?? 0);

$aceptar = txp_json('POST', "{$base}/viaje/aceptar", ['viaje_id' => $viajeId], $driver['cookie'], $driver['csrf']);
$steps[] = ['conductor aceptar', $aceptar];

$llego = txp_json('POST', "{$base}/viaje/llego", ['viaje_id' => $viajeId], $driver['cookie'], $driver['csrf']);
$steps[] = ['conductor llego', $llego];

$terminar = txp_json('POST', "{$base}/viaje/terminar", ['viaje_id' => $viajeId], $driver['cookie'], $driver['csrf']);
$steps[] = ['conductor terminar', $terminar];

$estado = txp_json('GET', "{$base}/viaje/estado/{$viajeId}", null, $passenger['cookie'], $passenger['csrf']);
$steps[] = ['estado final', $estado];

$failed = 0;
foreach ($steps as [$label, $resp]) {
    $ok = $resp['code'] >= 200 && $resp['code'] < 300;
    if (!$ok) {
        $failed++;
    }
    echo str_pad($label, 20) . " HTTP {$resp['code']} " . ($ok ? 'OK' : 'FAIL') . "\n";
    if (!$ok) {
        echo "  {$resp['body']}\n";
    }
}

echo $failed === 0 ? "\nALL OK\n" : "\n{$failed} step(s) failed\n";
@unlink($driver['cookie']);
@unlink($passenger['cookie']);
exit($failed === 0 ? 0 : 1);
