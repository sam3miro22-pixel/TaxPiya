<?php
/**
 * Configura GITHUB_BACKUP_TOKEN en Render (si tienes RENDER_API_KEY).
 * Uso: set RENDER_API_KEY=rnd_xxx && php scripts/sync-render-github-env.php
 */

$renderKey = getenv('RENDER_API_KEY') ?: '';
$ghToken = getenv('GITHUB_BACKUP_TOKEN') ?: '';

if ($ghToken === '') {
    $ghToken = trim((string) shell_exec('gh auth token 2>/dev/null'));
}

if ($renderKey === '') {
    fwrite(STDERR, "Falta RENDER_API_KEY. Obtén una en: https://dashboard.render.com/u/settings#api-keys\n");
    fwrite(STDERR, "Luego en Render Dashboard → Environment → añade manualmente:\n");
    fwrite(STDERR, "  GITHUB_BACKUP_TOKEN=<token GitHub con acceso a taxpiya-db-backup>\n");
    exit(1);
}

if ($ghToken === '') {
    fwrite(STDERR, "No hay token GitHub. Ejecuta: gh auth login\n");
    exit(1);
}

$serviceName = getenv('RENDER_SERVICE_NAME') ?: 'taxpiya';

$services = renderApi('GET', '/v1/services?limit=50', $renderKey);
$serviceId = null;

foreach ($services as $svc) {
    if (($svc['service']['name'] ?? '') === $serviceName || str_contains($svc['service']['slug'] ?? '', 'taxpiya')) {
        $serviceId = $svc['service']['id'] ?? null;
        break;
    }
}

if (!$serviceId && !empty($services[0]['service']['id'])) {
    $serviceId = $services[0]['service']['id'];
}

if (!$serviceId) {
    fwrite(STDERR, "No se encontró servicio Render.\n");
    exit(1);
}

$envVars = [
    ['key' => 'TAXPIYA_GITHUB_BACKUP', 'value' => 'true'],
    ['key' => 'TAXPIYA_GITHUB_BACKUP_MINUTES', 'value' => '5'],
    ['key' => 'GITHUB_BACKUP_OWNER', 'value' => 'sam3miro22-pixel'],
    ['key' => 'GITHUB_BACKUP_REPO', 'value' => 'taxpiya-db-backup'],
    ['key' => 'GITHUB_BACKUP_PATH', 'value' => 'taxpiya.sqlite'],
    ['key' => 'GITHUB_BACKUP_TOKEN', 'value' => $ghToken],
];

foreach ($envVars as $var) {
    renderApi('POST', "/v1/services/{$serviceId}/env-vars", $renderKey, $var);
    echo "OK {$var['key']}\n";
}

echo "Variables actualizadas. Render redeployará automáticamente.\n";

function renderApi(string $method, string $path, string $key, ?array $body = null)
{
    $url = 'https://api.render.com' . $path;
    $ch = curl_init($url);
    $headers = [
        'Authorization: Bearer ' . $key,
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 60,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code >= 300 && $method === 'POST') {
        // puede existir — intentar PUT vía delete+create no disponible; ignorar duplicados
    }

    $data = json_decode((string) $raw, true);
    return is_array($data) ? ($data ?? []) : [];
}
