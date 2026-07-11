<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class SystemKeepaliveController extends Controller
{
    public function __invoke(Request $request, WhatsAppService $wa)
    {
        $expected = (string) config('taxpiya.keepalive_key', '');
        $provided = (string) $request->query('key', '');
        if ($expected === '' || !hash_equals($expected, $provided)) {
            abort(403);
        }

        $before = $wa->getStatus();
        $action = null;

        if (($before['status'] ?? '') === 'unavailable') {
            $action = ['type' => 'restart', 'result' => $wa->restartProcess()];
            sleep(3);
        } elseif (($before['status'] ?? '') !== 'connected') {
            $action = ['type' => 'reconnect', 'result' => $wa->reconnect()];
            sleep(2);
        } elseif ($this->isDeafSession($before)) {
            $action = ['type' => 'restart', 'result' => $wa->restartProcess()];
            sleep(3);
        }

        $after = $wa->getStatus();
        $groq = (string) config('services.groq.api_key', '') !== ''
            || (string) config('taxpiya.assistant.groq_api_key', '') !== '';

        return response()->json([
            'ok'        => true,
            'ts'        => now()->toIso8601String(),
            'whatsapp'  => [
                'before'    => $before['status'] ?? 'unknown',
                'after'     => $after['status'] ?? 'unknown',
                'user'      => $after['user'] ?? null,
                'lastEventAt' => $after['lastEventAt'] ?? null,
                'action'    => $action,
            ],
            'assistant' => ['groq' => $groq],
        ]);
    }

    /** @param array<string,mixed> $status */
    private function isDeafSession(array $status): bool
    {
        if (($status['status'] ?? '') !== 'connected') {
            return false;
        }

        $lastEventAt = (int) ($status['lastEventAt'] ?? 0);
        if ($lastEventAt <= 0) {
            return false;
        }

        return (time() * 1000 - $lastEventAt) > (12 * 60 * 1000);
    }
}
