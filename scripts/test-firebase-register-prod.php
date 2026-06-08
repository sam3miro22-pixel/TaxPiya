<?php
/**
 * Prueba registro Firebase + sync Laravel en producción.
 */
$base = getenv('TAXPIYA_BASE_URL') ?: 'https://taxpiya.onrender.com';
$apiKey = getenv('FIREBASE_API_KEY') ?: 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk';
$password = 'TaxpiyaTest2026!';
$email = 'test_reg_' . bin2hex(random_bytes(4)) . '@taxpiya-test.local';

function httpJson(string $method, string $url, ?array $body = null, array $headers = []): array
{
    $ch = curl_init($url);
    $h = array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_HTTPHEADER     => $h,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'body' => $raw, 'json' => json_decode($raw ?: '[]', true)];
}

echo "=== Test registro Firebase + sync ===\n";
echo "Email: {$email}\n\n";

$signup = httpJson('POST', "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$apiKey}", [
    'email'             => $email,
    'password'          => $password,
    'returnSecureToken' => true,
]);
echo "Firebase signUp: HTTP {$signup['code']}\n";
if (empty($signup['json']['idToken'])) {
    echo "FAIL: " . ($signup['json']['error']['message'] ?? $signup['body']) . "\n";
    exit(1);
}
$idToken = $signup['json']['idToken'];
echo "Firebase OK uid=" . ($signup['json']['localId'] ?? '?') . "\n";

$page = httpJson('GET', "{$base}/pasajero/registro");
preg_match('/name="_token" value="([^"]+)"/', $page['body'], $m);
preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $page['body'], $cm);
$csrf = $m[1] ?? '';
$cookie = implode('; ', $cm[1] ?? []);

$sync = httpJson('POST', "{$base}/auth/firebase/sync", [
    'id_token'    => $idToken,
    'app'         => 'pasajero',
    'name'        => 'Test Registro',
    'telefono'    => '399' . random_int(1000000, 9999999),
    'is_register' => true,
], [
    "X-CSRF-TOKEN: {$csrf}",
    "Cookie: {$cookie}",
    'X-Requested-With: XMLHttpRequest',
    "Referer: {$base}/pasajero/registro",
]);

echo "Sync Laravel: HTTP {$sync['code']}\n";
echo json_encode($sync['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

if (($sync['json']['ok'] ?? false) !== true) {
    echo "\nFAIL sync\n";
    if (!is_array($sync['json']) || $sync['json'] === []) {
        echo "Raw body (first 500 chars):\n" . substr($sync['body'], 0, 500) . "\n";
    }
    exit(1);
}

echo "\nOK — registro completo\n";
exit(0);
