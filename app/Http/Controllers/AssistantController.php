<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssistantController extends Controller
{
    public function messages()
    {
        $userId = (int) auth()->id();
        if ($userId <= 0) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        if (!Schema::hasTable('assistant_mensajes')) {
            return response()->json(['ok' => true, 'messages' => []]);
        }

        $rows = DB::table('assistant_mensajes')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->limit(60)
            ->get(['id', 'rol', 'mensaje', 'created_at']);

        return response()->json(['ok' => true, 'messages' => $rows]);
    }

    public function send(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        $message = trim((string) $request->input('message', ''));
        if ($message === '') {
            return response()->json(['ok' => false, 'message' => 'Mensaje requerido'], 422);
        }

        $reply = $this->buildReply($message, $user);

        try {
            if (Schema::hasTable('assistant_mensajes')) {
                $now = now()->toDateTimeString();
                $uid = (int) $user->id;
                DB::table('assistant_mensajes')->insert([
                    'user_id'    => $uid,
                    'rol'        => 'user',
                    'mensaje'    => $message,
                    'created_at' => $now,
                ]);
                DB::table('assistant_mensajes')->insert([
                    'user_id'    => $uid,
                    'rol'        => 'assistant',
                    'mensaje'    => $reply,
                    'created_at' => $now,
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['ok' => true, 'reply' => $reply]);
    }

    /** @param object $user */
    private function buildReply(string $message, object $user): string
    {
        $apiKey = $this->groqApiKey();
        if ($apiKey !== '') {
            $roleName = $this->roleName($user);
            $groq = $this->askGroq($apiKey, [
                ['role' => 'system', 'content' => $this->systemPrompt($roleName, $user)],
                ['role' => 'user', 'content' => $message],
            ]);
            if ($groq !== null && $groq !== '') {
                return $groq;
            }
        }

        return $this->fallbackReply($message);
    }

    private function groqApiKey(): string
    {
        $key = (string) config('services.groq.api_key', '');
        if ($key === '') {
            $key = (string) config('taxpiya.assistant.groq_api_key', '');
        }

        return $key;
    }

    /** @param object $user */
    private function roleName(object $user): string
    {
        try {
            $roleId = (int) ($user->user_role_id ?? 0);
            $name = DB::table('roles')->where('role_id', $roleId)->value('role_name');

            return $name ?: 'Usuario';
        } catch (\Throwable) {
            return 'Usuario';
        }
    }

    /** @param object $user */
    private function systemPrompt(string $roleName, object $user): string
    {
        $extra = 'Rol: ' . $roleName;
        try {
            if (Schema::hasTable('viajes') && $roleName === 'Pasajero') {
                $active = DB::table('viajes')
                    ->where('pasajero_id', $user->id)
                    ->whereNotIn('estado', ['terminado', 'cancelado', 'cancelado_sistema'])
                    ->orderByDesc('id')
                    ->first();
                if ($active) {
                    $extra .= '. Viaje activo #' . $active->id . ' estado ' . $active->estado;
                }
            }
        } catch (\Throwable) {
            // ignore
        }

        return 'Eres TaxPiya Assistant, asistente de taxis en Colombia. Responde en espanol, claro y breve. '
            . 'Ayuda con viajes (mapa + Solicitar taxi), tarifas, billetera, codigo de llegada y soporte. '
            . 'No inventes precios. Contexto: ' . $extra;
    }

    /** @param list<array{role:string,content:string}> $messages */
    private function askGroq(string $apiKey, array $messages): ?string
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $url = rtrim((string) config('taxpiya.assistant.groq_base_url', 'https://api.groq.com/openai/v1'), '/')
            . '/chat/completions';

        $payload = json_encode([
            'model'       => config('taxpiya.assistant.groq_model', 'llama-3.3-70b-versatile'),
            'messages'    => $messages,
            'temperature' => 0.4,
            'max_tokens'  => 500,
        ]);

        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $payload,
        ]);

        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code < 200 || $code >= 300) {
            return null;
        }

        $data = json_decode($body, true);

        return trim((string) ($data['choices'][0]['message']['content'] ?? '')) ?: null;
    }

    private function fallbackReply(string $message): string
    {
        $text = mb_strtolower(trim($message));

        if (preg_match('/\b(viaje|taxi|llevar|lleve|recoger)\b/u', $text)) {
            return 'Para pedir un taxi abre el mapa, marca tu destino y pulsa «Solicitar taxi».';
        }
        if (preg_match('/\b(ayuda|help|hola|buenas)\b/u', $text)) {
            return 'Hola, soy el asistente TaxPiya. Puedo ayudarte con viajes, tarifas, billetera y soporte.';
        }
        if (preg_match('/\b(tarifa|precio|cuanto|cuesta)\b/u', $text)) {
            return 'La tarifa se calcula en el mapa segun origen y destino antes de confirmar.';
        }

        return 'Estoy aqui para ayudarte con TaxPiya: viajes, tarifas, billetera y soporte.';
    }
}
