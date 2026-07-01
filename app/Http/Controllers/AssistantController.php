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
        try {
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
        } catch (\Throwable $e) {
            report($e);
            $msg = isset($message) ? $message : '';
            return response()->json(['ok' => true, 'reply' => $this->fallbackReply($msg)]);
        }
    }

    /**
     * Forward a message to the human support WhatsApp number configured in admin.
     */
    public function humanSupport(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
            }

            $message = trim((string) $request->input('message', ''));
            if ($message === '') {
                return response()->json(['ok' => false, 'message' => 'Mensaje requerido'], 422);
            }

            // Read configured support phone
            $phone = null;
            if (Schema::hasTable('settings')) {
                $phone = DB::table('settings')
                    ->where('key', 'whatsapp_support_phone')
                    ->value('value');
            }
            if (!$phone) {
                $filePath = storage_path('app/whatsapp-support-phone.txt');
                if (file_exists($filePath)) {
                    $phone = trim(file_get_contents($filePath));
                }
            }

            if (!$phone) {
                return response()->json([
                    'ok'    => true,
                    'reply' => '⚠️ No hay un número de soporte configurado. Contacta al administrador.',
                ]);
            }

            $userName = $user->name ?? 'Usuario';
            $userRole = $user->tipo ?? 'usuario';
            $fullMessage = "🆘 *Soporte Humano - Taxpiya*\n\n👤 *Usuario:* {$userName}\n🏷️ *Rol:* {$userRole}\n\n💬 *Mensaje:*\n{$message}";

            $wa = app(\App\Services\WhatsAppService::class);
            $result = $wa->sendMessage($phone, $fullMessage);

            if ($result['ok'] ?? false) {
                return response()->json([
                    'ok'    => true,
                    'reply' => "✅ Tu mensaje ha sido enviado a nuestro equipo de soporte por WhatsApp. Te responderemos a la brevedad posible.",
                ]);
            } else {
                return response()->json([
                    'ok'    => true,
                    'reply' => "⚠️ No pudimos enviar tu mensaje en este momento. Intenta de nuevo o contáctanos directamente.",
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok'    => true,
                'reply' => "⚠️ Error al contactar soporte. Intenta de nuevo en unos minutos.",
            ]);
        }
    }

    /** @param object $user */
    private function buildReply(string $message, object $user): string
    {
        $apiKey = $this->groqApiKey();
        if ($apiKey !== '') {
            $roleName = $this->roleName($user);
            $messages = [['role' => 'system', 'content' => $this->systemPrompt($roleName, $user)]];
            foreach ($this->recentHistory((int) $user->id) as $row) {
                $messages[] = [
                    'role'    => $row['rol'] === 'user' ? 'user' : 'assistant',
                    'content' => $row['mensaje'],
                ];
            }
            $messages[] = ['role' => 'user', 'content' => $message];
            $groq = $this->askGroq($apiKey, $messages);
            if ($groq !== null && $groq !== '') {
                return $groq;
            }
        }

        return $this->fallbackReply($message);
    }

    /** @return list<array{rol:string,mensaje:string}> */
    private function recentHistory(int $userId): array
    {
        if (!Schema::hasTable('assistant_mensajes')) {
            return [];
        }

        return DB::table('assistant_mensajes')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->limit(8)
            ->get(['rol', 'mensaje'])
            ->reverse()
            ->values()
            ->map(fn ($r) => ['rol' => (string) $r->rol, 'mensaje' => (string) $r->mensaje])
            ->all();
    }

    private function groqApiKey(): string
    {
        $key = (string) config('services.groq.api_key', '');
        if ($key === '') {
            $key = (string) config('taxpiya.assistant.groq_api_key', '');
        }
        if ($key === '') {
            $key = (string) getenv('GROQ_API_KEY');
        }
        if ($key === '') {
            $key = (string) ($_ENV['GROQ_API_KEY'] ?? '');
        }
        if ($key === '') {
            $key = (string) ($_SERVER['GROQ_API_KEY'] ?? '');
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
            if (Schema::hasTable('wallet_cuentas') && strtolower($roleName) === 'pasajero') {
                $saldo = app(\App\Services\WalletLedgerService::class)->getSaldoPasajero((int) $user->id);
                $extra .= '. Saldo billetera pasajero: $' . number_format($saldo, 0, ',', '.') . ' COP';
            }
            if (Schema::hasTable('viajes') && strtolower($roleName) === 'pasajero') {
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

        return 'Eres TaxPiya Assistant, asistente oficial de la app de taxis TaxPiya en Colombia. '
            . 'Responde en español, concreto y útil (máximo 4 oraciones). '
            . 'Ayuda con: pedir viaje en el mapa, tarifas por distancia, billetera/recargas Nequi, código de llegada, estado del viaje y soporte. '
            . 'Si preguntan precio sin viaje activo, indica que la tarifa se calcula por km en el mapa antes de confirmar. '
            . 'No inventes montos ni prometas conductores si no hay viaje. Contexto usuario: ' . $extra;
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
            'model'       => config('taxpiya.assistant.groq_model', 'llama-3.1-8b-instant'),
            'messages'    => $messages,
            'temperature' => 0.35,
            'max_tokens'  => 400,
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
