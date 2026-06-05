<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class PushService
{
    private string $projectId;
    private string $credsPath;
    private string $scope;
    private Client $http;

    private static ?string $cachedToken = null;
    private static int $cachedExp = 0;

    public function __construct()
{
    $cfg = config('services.fcm', []);
    $this->projectId = (string)($cfg['project_id']  ?? '');
    $this->credsPath = (string)($cfg['credentials'] ?? '');
    $this->scope     = (string)($cfg['scope']       ?? 'dev');
    $this->http      = new Client(['timeout' => 8]);

    if ($this->projectId === '' || $this->credsPath === '') {
        throw new \RuntimeException('FCM no configurado: revisa services.fcm (FIREBASE_PROJECT_ID / FIREBASE_CREDENTIALS).');
    }
}

    private function accessToken(): string
    {
        $now = time();
        if (self::$cachedToken && $now < self::$cachedExp - 60) {
            return self::$cachedToken;
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds  = new ServiceAccountCredentials($scopes, $this->credsPath);
        $tokArr = $creds->fetchAuthToken();

        $token = $tokArr['access_token'] ?? null;
        $exp   = ($tokArr['created'] ?? $now) + ($tokArr['expires_in'] ?? 3600);

        if (!$token) {
            throw new \RuntimeException('No se pudo obtener access_token de FCM');
        }
        self::$cachedToken = $token;
        self::$cachedExp   = $exp;
        return $token;
    }

    /** Trae tokens válidos para una lista de usuarios (filtrando por scope/dev|prod). */
    public function tokensForUsers(array $userIds): array
    {
        if (empty($userIds)) return [];

        $tokens = DB::table('push_tokens as pt')
            ->join('usuario_dispositivos as d', 'd.id', '=', 'pt.dispositivo_id')
            ->whereIn('d.user_id', $userIds)
            ->where('pt.provider', 'fcm')
            ->where('pt.estado', 'valido')
            ->where('pt.scope', $this->scope)
            ->where('d.notificaciones_activas', 1)
            ->where('d.activo', 1)
            ->pluck('pt.token')
            ->unique()
            ->values()
            ->all();

        return $tokens;
    }

    /** Envía a 1 token. Retorna true si FCM respondió 200 OK. */
public function sendToToken(string $token, string $title, string $body, array $data = []): bool
{
    // FCM v1 requiere strings en data
    $data = array_map(fn($v) => is_scalar($v) ? (string)$v : json_encode($v), $data);

    // Detecta tipo de evento para elegir canal/sonido
    $type = isset($data['t'])
        ? (string)$data['t']
        : (isset($data['type']) ? (string)$data['type'] : '');

    // Config Android base (entrega)
    $android = [
        'priority' => 'HIGH', // prioridad de envío FCM
        'notification' => [
            // valores por defecto (chat/otros): canal sin sonido
            'channel_id'            => 'taxpiya_chat',
            // sin 'sound' para que NO suene en chat
            'notification_priority' => 'PRIORITY_DEFAULT',
            // Opcional estándar:
            // 'visibility' => 'PUBLIC',
            // 'default_vibrate_timings' => true,
        ],
    ];

    // Si es una OFERTA => canal alto + sonido personalizado
    if ($type === 'offer') {
        $android['notification'] = [
            'channel_id'            => 'taxpiya_offers',     // ⬅️ mismo ID que en tu MainActivity
            'sound'                 => 'solicitudtaxpiya',   // ⬅️ sin .mp3 (debe existir en res/raw)
            'notification_priority' => 'PRIORITY_MAX',
            // 'visibility' => 'PUBLIC',
            // 'default_vibrate_timings' => true,
        ];
    }
	
	if ($type === 'arrived' || $type === 'llego') {
        $android['notification'] = [
            'channel_id'            => 'taxpiya_arrivals',
            'sound'                 => 'conductorllego',
            'notification_priority' => 'PRIORITY_MAX',
        ];
    }

    if ($type === 'assigned') {
        $android['notification'] = [
            'channel_id'            => 'taxpiya_arrivals',
            'sound'                 => 'conductorllego',
            'notification_priority' => 'PRIORITY_HIGH',
        ];
    }

    $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    $payload = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data'    => $data,     // extras (todo string)
            'android' => $android,  // config por tipo
        ],
    ];

    try {
        $res = $this->http->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
        ]);
        return $res->getStatusCode() === 200;
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        // Token inválido o caducado → marcarlo
        $body = (string) $e->getResponse()?->getBody();
        if (stripos($body, 'UNREGISTERED') !== false
         || stripos($body, 'NOT_FOUND') !== false
         || stripos($body, 'INVALID_ARGUMENT') !== false) {
            DB::table('push_tokens')
                ->where('token', $token)
                ->update([
                    'estado' => 'invalidado',
                    'invalidado_at' => now(),
                    'motivo_invalidez' => 'fcm:' . ($body ?: 'unknown'),
                    'updated_at' => now(),
                ]);
        }
        return false;
    }
}


    /** Orquesta: busca tokens y envía uno por uno. Retorna cantidad enviada OK. */
    public function notifyUsers(array $userIds, string $title, string $body, array $data = []): int
    {
        $tokens = $this->tokensForUsers($userIds);
        $ok = 0;
        foreach ($tokens as $t) {
            if ($this->sendToToken($t, $title, $body, $data)) $ok++;
        }
        return $ok;
    }
}
