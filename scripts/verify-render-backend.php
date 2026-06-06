<?php
/** Verifica endpoints del backend en Render. Uso: php scripts/verify-render-backend.php */

$base = getenv('APP_URL') ?: 'https://taxpiya.onrender.com';
$password = 'Taxpiya2026!';

$checks = [];

function req(string $method, string $url, array $opts = []): array {
    $ch = curl_init($url);
    $headers = $opts['headers'] ?? [];
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    if (!empty($opts['body'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['body']);
    }
    if (!empty($opts['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $opts['cookie']);
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hsz = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return [
        'code'    => $code,
        'headers' => substr((string) $raw, 0, $hsz),
        'body'    => substr((string) $raw, $hsz),
    ];
}

function ok(string $name, bool $pass, string $detail = ''): void {
    global $checks;
    $checks[] = ['name' => $name, 'pass' => $pass, 'detail' => $detail];
    echo ($pass ? '[OK] ' : '[FAIL] ') . $name . ($detail ? " — {$detail}" : '') . "\n";
}

echo "Verificando {$base}\n\n";

$r = req('GET', "{$base}/pasajero/login");
ok('Pasajero login page', $r['code'] === 200, "HTTP {$r['code']}");

$r = req('GET', "{$base}/conductor/login");
ok('Conductor login page', $r['code'] === 200, "HTTP {$r['code']}");

$r = req('GET', "{$base}/empresa/login");
ok('Empresa login page', $r['code'] === 200, "HTTP {$r['code']}");

$r = req('GET', "{$base}/index/login");
ok('Admin login page', $r['code'] === 200, "HTTP {$r['code']}");

$r = req('GET', "{$base}/");
ok('Home / redirect', in_array($r['code'], [200, 302], true), "HTTP {$r['code']}");

// CSRF + login pasajero
$r = req('GET', "{$base}/pasajero/login");
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
preg_match('/XSRF-TOKEN=([^;]+)/', $r['headers'], $xsrf);
$token = $m[1] ?? null;
$cookie = '';
if (preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $r['headers'], $cm)) {
    $cookie = implode('; ', $cm[1]);
}

if ($token) {
    $body = http_build_query([
        '_token'   => $token,
        'username' => '3009001001',
        'password' => $password,
        'app'      => 'pasajero',
        'rememberme' => '1',
    ]);
    $login = req('POST', "{$base}/auth/login", [
        'headers' => [
            'Content-Type: application/x-www-form-urlencoded',
            "Cookie: {$cookie}",
            'Referer: ' . "{$base}/pasajero/login",
        ],
        'body' => $body,
    ]);
    $loginOk = in_array($login['code'], [200, 302], true) && !str_contains($login['body'], 'no correctos');
    ok('Login pasajero demo (Laravel)', $loginOk, "HTTP {$login['code']}");
} else {
    ok('Login pasajero demo (Laravel)', false, 'Sin CSRF token');
}

// Firebase signIn
$apiKey = getenv('FIREBASE_API_KEY') ?: 'AIzaSyCIT1YV8eJRzmf7HSQbWe_7OzftDD1Vcnk';
$fb = req('POST', "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$apiKey}", [
    'headers' => ['Content-Type: application/json'],
    'body'    => json_encode([
        'email' => 'pasajero.demo1@taxpiya.com',
        'password' => $password,
        'returnSecureToken' => true,
    ]),
]);
$fbData = json_decode($fb['body'], true);
ok('Firebase Auth signIn pasajero', !empty($fbData['idToken']), $fbData['error']['message'] ?? 'token ok');

$idToken = $fbData['idToken'] ?? null;
if ($idToken) {
    $rCsrf = req('GET', "{$base}/pasajero/login");
    preg_match('/name="_token" value="([^"]+)"/', $rCsrf['body'], $csrfM);
    preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $rCsrf['headers'], $csrfCm);
    $csrfCookie = implode('; ', $csrfCm[1] ?? []);
    $sync = req('POST', "{$base}/auth/firebase/sync", [
        'headers' => [
            'Content-Type: application/json',
            'Accept: application/json',
            "X-CSRF-TOKEN: " . ($csrfM[1] ?? ''),
            "Cookie: {$csrfCookie}",
            'Referer: ' . "{$base}/pasajero/login",
        ],
        'body'    => json_encode([
            'id_token' => $idToken,
            'app'      => 'pasajero',
            'name'     => 'Pasajero Demo 1',
            'telefono' => '3009001001',
        ]),
    ]);
    $syncData = json_decode($sync['body'], true);
    ok('Firebase sync Laravel', ($syncData['ok'] ?? false) === true, $syncData['message'] ?? "HTTP {$sync['code']}");
}

// Conductor login
$r2 = req('GET', "{$base}/conductor/login");
preg_match('/name="_token" value="([^"]+)"/', $r2['body'], $m2);
preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $r2['headers'], $cm2);
$cookie2 = implode('; ', $cm2[1] ?? []);
if (!empty($m2[1])) {
    $body2 = http_build_query([
        '_token'   => $m2[1],
        'username' => '3109001001',
        'password' => $password,
        'app'      => 'conductor',
    ]);
    $loginC = req('POST', "{$base}/auth/login", [
        'headers' => [
            'Content-Type: application/x-www-form-urlencoded',
            "Cookie: {$cookie2}",
            'Referer: ' . "{$base}/conductor/login",
        ],
        'body' => $body2,
    ]);
    $cOk = in_array($loginC['code'], [200, 302], true) && !str_contains($loginC['body'], 'no correctos');
    ok('Login conductor demo (Laravel)', $cOk, "HTTP {$loginC['code']}");

    if ($cOk) {
        preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $loginC['headers'], $cm3);
        $sessionCookie = implode('; ', array_unique(array_merge($cm2[1] ?? [], $cm3[1] ?? [])));
        $walletPage = req('GET', "{$base}/conductor/wallet", [
            'headers' => ["Cookie: {$sessionCookie}"],
        ]);
        ok('Conductor wallet page', $walletPage['code'] === 200 && str_contains($walletPage['body'], 'Mi wallet'), "HTTP {$walletPage['code']}");

        $walletApi = req('GET', "{$base}/api/conductor/wallet", [
            'headers' => ["Cookie: {$sessionCookie}", 'Accept: application/json'],
        ]);
        $wData = json_decode($walletApi['body'], true);
        ok('Conductor wallet API', ($wData['ok'] ?? false) === true, 'saldo=' . ($wData['saldo'] ?? '?'));
    }
}

// Login admin demo
$rAdmin = req('GET', "{$base}/index/login");
preg_match('/name="_token" value="([^"]+)"/', $rAdmin['body'], $ma);
preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $rAdmin['headers'], $cma);
$cookieA = implode('; ', $cma[1] ?? []);
if (!empty($ma[1])) {
    $bodyA = http_build_query([
        '_token'   => $ma[1],
        'username' => '3001001001',
        'password' => $password,
    ]);
    $loginA = req('POST', "{$base}/auth/login", [
        'headers' => [
            'Content-Type: application/x-www-form-urlencoded',
            "Cookie: {$cookieA}",
            'Referer: ' . "{$base}/index/login",
        ],
        'body' => $bodyA,
    ]);
    $aOk = in_array($loginA['code'], [200, 302], true) && !str_contains($loginA['body'], 'no correctos');
    ok('Login admin demo (Laravel)', $aOk, "HTTP {$loginA['code']}");
}

// Login empresa demo
$rEmp = req('GET', "{$base}/empresa/login");
preg_match('/name="_token" value="([^"]+)"/', $rEmp['body'], $me);
preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $rEmp['headers'], $cme);
$cookieE = implode('; ', $cme[1] ?? []);
if (!empty($me[1])) {
    $bodyE = http_build_query([
        '_token'   => $me[1],
        'username' => '3209002001',
        'password' => $password,
        'app'      => 'empresa',
    ]);
    $loginE = req('POST', "{$base}/auth/login", [
        'headers' => [
            'Content-Type: application/x-www-form-urlencoded',
            "Cookie: {$cookieE}",
            'Referer: ' . "{$base}/empresa/login",
        ],
        'body' => $bodyE,
    ]);
    $eOk = in_array($loginE['code'], [200, 302], true) && !str_contains($loginE['body'], 'no correctos');
    ok('Login empresa demo (Laravel)', $eOk, "HTTP {$loginE['code']}");

    if ($eOk) {
        preg_match_all('/Set-Cookie: ([^;\r\n]+)/i', $loginE['headers'], $cm4);
        $sessionE = implode('; ', array_unique(array_merge($cme[1] ?? [], $cm4[1] ?? [])));
        $dash = req('GET', "{$base}/empresa", [
            'headers' => ["Cookie: {$sessionE}"],
        ]);
        ok('Empresa dashboard', $dash['code'] === 200 && str_contains($dash['body'], 'Flota'), "HTTP {$dash['code']}");
    }
}

$rAplicar = req('GET', "{$base}/conductor/aplicar");
ok('Conductor aplicar page', $rAplicar['code'] === 200 && str_contains($rAplicar['body'], 'Solicitar'), "HTTP {$rAplicar['code']}");

$failed = array_filter($checks, fn ($c) => !$c['pass']);

// Tarifa pública
$tarifa = req('GET', "{$base}/tarifa-fija");
$tarifaOk = $tarifa['code'] === 200 && str_contains($tarifa['body'], 'monto');
ok('Tarifa fija API', $tarifaOk, "HTTP {$tarifa['code']}");

echo "\n" . (count($failed) === 0 ? "TODOS OK (" . count($checks) . " checks)\n" : count($failed) . " fallos\n");
exit(count($failed) === 0 ? 0 : 1);
