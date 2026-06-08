<?php
/**
 * Prueba API de referidos en producción.
 * Uso: php scripts/test-referrals-prod.php
 */
$base = getenv('TAXPIYA_BASE_URL') ?: 'https://taxpiya.onrender.com';

function req(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'json' => json_decode($body ?: '[]', true)];
}

echo "=== TaxPiya Referidos PROD ===\n";
echo "Base: {$base}\n\n";

$pages = [
    '/pasajero/registro',
    '/conductor/aplicar',
    '/empresa/afiliarse',
    '/index/login',
];
foreach ($pages as $p) {
    $ch = curl_init($base . $p);
    curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 90]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo ($code === 200 ? '[OK]' : '[FAIL]') . " {$p} HTTP {$code}\n";
}

$invalid = req($base . '/api/referral/validate?code=TXP-P999999');
echo ($invalid['code'] === 422 ? '[OK]' : '[FAIL]') . " validate invalid code HTTP {$invalid['code']}\n";

$demo = req($base . '/api/referral/validate?code=TXP-P000001');
echo ($demo['code'] === 200 || $demo['code'] === 422 ? '[OK]' : '[FAIL]') . " validate demo code HTTP {$demo['code']}\n";
if (!empty($demo['json']['ok'])) {
    echo "  Demo code válido: {$demo['json']['code']}\n";
}

echo "\nListo.\n";
