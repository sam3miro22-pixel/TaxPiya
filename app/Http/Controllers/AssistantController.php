<?php

namespace App\Http\Controllers;

use App\Services\GroqAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssistantController extends Controller
{
    public function messages(Request $request)
    {
        try {
            $userId = (int) auth()->id();
            if (!Schema::hasTable('assistant_mensajes')) {
                return response()->json(['ok' => true, 'messages' => []]);
            }

            $rows = DB::table('assistant_mensajes')
                ->where('user_id', $userId)
                ->orderBy('id')
                ->limit(60)
                ->get(['id', 'rol', 'mensaje', 'created_at']);

            return response()->json([
                'ok'       => true,
                'messages' => $rows,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => true, 'messages' => []]);
        }
    }

    public function send(Request $request, GroqAssistantService $groq)
    {
        try {
            $data = $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
            }

            $userId = (int) $user->id;
            $message = trim($data['message']);
            $now = now()->toDateTimeString();
            $context = $groq->buildUserContext($user);

            if (!Schema::hasTable('assistant_mensajes')) {
                $reply = $groq->chat($user, $message, [], $context);
                return response()->json(['ok' => true, 'reply' => $reply]);
            }

            DB::table('assistant_mensajes')->insert([
                'user_id'    => $userId,
                'rol'        => 'user',
                'mensaje'    => $message,
                'created_at' => $now,
            ]);

            $history = DB::table('assistant_mensajes')
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->limit(14)
                ->get(['rol', 'mensaje'])
                ->reverse()
                ->map(fn ($r) => ['role' => $r->rol, 'content' => $r->mensaje])
                ->values()
                ->all();

            $reply = $groq->chat($user, $message, $history, $context);

            DB::table('assistant_mensajes')->insert([
                'user_id'    => $userId,
                'rol'        => 'assistant',
                'mensaje'    => $reply,
                'created_at' => $now,
            ]);

            return response()->json(['ok' => true, 'reply' => $reply]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'ok'    => true,
                'reply' => 'Hola, soy el asistente TaxPiya. Puedo ayudarte con viajes, tarifas y soporte. ¿Qué necesitas?',
            ]);
        }
    }
}
