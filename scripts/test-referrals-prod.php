<?php
/**
 * Prueba referidos en producción (API + páginas).
 * Uso: php scripts/test-referrals-prod.php
 */
$base = getenv('TAXPIYA_BASE_URL') ?: 'https://taxpiya.onrender.com';

function req(string $url, array $opts = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, array_merge([
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 90,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ], $opts));
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
echo ($invalid['code'] === 422 ? '[OK]' : '[FAIL]') . " validate invalid HTTP {$invalid['code']}\n";

$demo = req($base . '/api/referral/validate?code=TXP-P000001');
echo ($demo['code'] === 200 ? '[OK]' : '[INFO]') . " validate TXP-P000001 HTTP {$demo['code']}\n";
if (!empty($demo['json']['ok'])) {
    echo "  Código demo válido (pasajero 1)\n";
}

$conductorHome = req($base . '/conductor/login');
echo ($conductorHome['code'] === 200 ? '[OK]' : '[FAIL]') . " conductor login page HTTP {$conductorHome['code']}\n";
if (str_contains($conductorHome['body'] ?? '', 'txp-brand-badge') || str_contains($conductorHome['body'] ?? '', 'txp-conductor-head')) {
    echo "[OK] Layout conductor mapa (logo/badge) presente en deploy\n";
} else {
    echo "[INFO] Layout nuevo aún no desplegado o requiere sesión\n";
}

echo "\nFlujo bono referido (manual admin):\n";
echo "  - Pasajero con código → bono inmediato al registrarse\n";
echo "  - Conductor aplicar → bono al activar en admin Conductores\n";
echo "  - Empresa afiliarse → bono al aprobar en admin Empresas\n";
echo "  - Monto: \$5.000 COP → saldo billetera del referidor\n";
echo "\nListo.\n";
