<?php

namespace App\Services;

use App\Models\Users;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GroqAssistantService
{
    public function isConfigured(): bool
    {
        return (string) config('taxpiya.assistant.groq_api_key') !== '';
    }

    /**
     * @param  list<array{role:string,content:string}>  $history
     */
    public function chat(Users|object $user, string $message, array $history = [], ?string $context = null): string
    {
        if (!$this->isConfigured()) {
            return $this->fallbackReply($message);
        }

        $userId = (int) ($user->id ?? 0);
        if ($userId > 0 && Cache::get("groq_rl:{$userId}", 0) >= 30) {
            return 'Has enviado muchos mensajes. Espera un minuto e intenta de nuevo.';
        }

        $roleName = $this->roleLabel($user);
        $system = $this->systemPrompt($roleName, $context);

        $messages = [['role' => 'system', 'content' => $system]];
        foreach (array_slice($history, -12) as $row) {
            $messages[] = [
                'role'    => ($row['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                'content' => (string) ($row['content'] ?? ''),
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::timeout(20)
                ->withToken((string) config('taxpiya.assistant.groq_api_key'))
                ->acceptJson()
                ->post(rtrim((string) config('taxpiya.assistant.groq_base_url'), '/') . '/chat/completions', [
                    'model'       => config('taxpiya.assistant.groq_model'),
                    'messages'    => $messages,
                    'temperature' => 0.4,
                    'max_tokens'  => 500,
                ]);

            if (!$response->successful()) {
                Log::warning('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallbackReply($message);
            }

            $reply = trim((string) ($response->json('choices.0.message.content') ?? ''));
            if ($reply === '') {
                return $this->fallbackReply($message);
            }

            if ($userId > 0) {
                Cache::put("groq_rl:{$userId}", Cache::get("groq_rl:{$userId}", 0) + 1, 60);
            }

            return $reply;
        } catch (\Throwable $e) {
            Log::warning('Groq request failed', ['err' => $e->getMessage()]);
            return $this->fallbackReply($message);
        }
    }

    public function tripReply(int $viajeId, string $userRole, string $message): ?string
    {
        $viaje = Schema::hasTable('viajes')
            ? DB::table('viajes')->where('id', $viajeId)->first()
            : null;

        $context = 'Chat durante un viaje activo. Rol del usuario: ' . $userRole . '.';
        if ($viaje) {
            $context .= ' Estado viaje: ' . ($viaje->estado ?? 'desconocido') . '.';
            if ($viaje->tarifa_aplicada !== null) {
                $context .= ' Tarifa: $' . number_format((float) $viaje->tarifa_aplicada, 0, ',', '.') . ' ' . ($viaje->moneda ?? 'COP') . '.';
            }
            if (!empty($viaje->codigo_llegada)) {
                $context .= ' Codigo llegada: ' . $viaje->codigo_llegada . '.';
            }
        }

        $fakeUser = (object) ['id' => 0, 'user_role_id' => $userRole === 'conductor' ? 3 : 2];
        $reply = $this->chat($fakeUser, $message, [], $context);

        return $reply !== '' ? $reply : null;
    }

    public function buildUserContext(Users|object $user): string
    {
        $parts = ['Rol: ' . $this->roleLabel($user)];

        if (Schema::hasTable('viajes')) {
            $role = $this->roleLabel($user);
            $active = null;
            if ($role === 'Pasajero') {
                $active = DB::table('viajes')
                    ->where('pasajero_id', $user->id)
                    ->whereNotIn('estado', ['terminado', 'cancelado', 'cancelado_sistema'])
                    ->orderByDesc('id')
                    ->first();
            } elseif ($role === 'Conductor' && Schema::hasTable('conductores')) {
                $conductorId = DB::table('conductores')->where('user_id', $user->id)->value('id');
                if ($conductorId) {
                    $active = DB::table('viajes')
                        ->where('conductor_id', $conductorId)
                        ->whereNotIn('estado', ['terminado', 'cancelado', 'cancelado_sistema'])
                        ->orderByDesc('id')
                        ->first();
                }
            }
            if ($active) {
                $parts[] = 'Viaje activo #' . $active->id . ' estado ' . $active->estado;
            }
        }

        return implode('. ', $parts);
    }

    private function roleLabel(Users|object $user): string
    {
        $roleId = (int) ($user->user_role_id ?? 0);
        $name = DB::table('roles')->where('role_id', $roleId)->value('role_name');

        return $name ?: 'Usuario';
    }

    private function systemPrompt(string $roleName, ?string $extra = null): string
    {
        $base = <<<TXT
Eres TaxPiya Assistant, asistente oficial de la app de taxis TaxPiya en Colombia.
Responde en español, claro y breve (maximo 3 parrafos cortos).
Ayuda con: solicitar viajes (explica usar el mapa y boton Solicitar taxi), tarifas, billetera, codigo de llegada, cancelaciones, estado del viaje y soporte.
No inventes precios exactos si no los tienes; indica que se calculan en el mapa.
Si piden un viaje, guialos: abrir mapa, elegir destino, confirmar solicitud.
Usuario actual: {$roleName}.
TXT;

        if ($extra) {
            $base .= "\nContexto: {$extra}";
        }

        return $base;
    }

    private function fallbackReply(string $message): string
    {
        $text = mb_strtolower(trim($message));

        if (preg_match('/\b(viaje|taxi|llevar|lleve|recoger)\b/u', $text)) {
            return 'Para pedir un taxi abre el mapa, marca tu destino y pulsa «Solicitar taxi». Si ya tienes un viaje activo, revisa el estado en pantalla.';
        }
        if (preg_match('/\b(ayuda|help|hola|buenas)\b/u', $text)) {
            return 'Hola, soy el asistente TaxPiya. Puedo ayudarte con viajes, tarifas, billetera y soporte. ¿Qué necesitas?';
        }
        if (preg_match('/\b(tarifa|precio|cuanto|cuesta)\b/u', $text)) {
            return 'La tarifa se calcula según origen y destino en el mapa antes de confirmar el viaje.';
        }

        return 'Estoy aquí para ayudarte con TaxPiya: viajes, tarifas, billetera y soporte. Cuéntame qué necesitas.';
    }
}
