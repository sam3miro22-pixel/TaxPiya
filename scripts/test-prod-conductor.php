<?php
/**
 * Prueba login conductor + endpoints en producción.
 * Uso: php scripts/test-prod-conductor.php [base_url]
 */
$base = rtrim($argv[1] ?? 'https://taxpiya.onrender.com', '/');
$cookie = tempnam(sys_get_temp_dir(), 'txp');

function txp_request(string $url, array $opts, string $cookie): array
{
    $ch = curl_init($url);
    $defaults = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 60,
    ];
    curl_setopt_array($ch, $defaults + $opts);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'body' => (string) $body];
}

echo "=== TaxPiya prod conductor test ===\nBase: {$base}\n\n";

$loginPage = txp_request("{$base}/conductor/login", [], $cookie);
preg_match('/name="_token"\s+value="([^"]+)"/', $loginPage['body'], $m);
$token = $m[1] ?? '';
echo 'CSRF: ' . ($token ? 'ok' : 'MISSING') . "\n";

$login = txp_request("{$base}/auth/login", [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'username' => '3109001001',
        'password' => 'Taxpiya2026!',
        'app'      => 'conductor',
        '_token'   => $token,
    ]),
], $cookie);
echo "Login HTTP: {$login['code']}\n";

$home = txp_request("{$base}/home", [], $cookie);
preg_match('/meta name="csrf-token" content="([^"]+)"/', $home['body'], $m2);
$csrf = $m2[1] ?? $token;
echo 'Home HTTP: ' . $home['code'] . ' (auth: ' . (str_contains($home['body'], 'NO DISPONIBLE') || str_contains($home['body'], 'DISPONIBLE') ? 'ok' : 'maybe not') . ")\n\n";

$jsonHeaders = [
    'Accept: application/json',
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: ' . $csrf,
];

$disp = txp_request("{$base}/conductor/disponible", [
    CURLOPT_POST       => true,
    CURLOPT_HTTPHEADER => $jsonHeaders,
    CURLOPT_POSTFIELDS => json_encode(['disponible' => true]),
], $cookie);
echo "POST /conductor/disponible → {$disp['code']}\n{$disp['body']}\n\n";

$pos = txp_request("{$base}/conductor/posicion", [
    CURLOPT_POST       => true,
    CURLOPT_HTTPHEADER => $jsonHeaders,
    CURLOPT_POSTFIELDS => json_encode(['lat' => 4.711, 'lng' => -74.072]),
], $cookie);
echo "POST /conductor/posicion → {$pos['code']}\n{$pos['body']}\n";

@unlink($cookie);
