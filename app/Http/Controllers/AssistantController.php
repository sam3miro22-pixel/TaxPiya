<?php

namespace App\Http\Controllers;

use App\Services\GroqAssistantService;
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

        $reply = 'Hola, soy el asistente TaxPiya. Puedo ayudarte con viajes, tarifas, billetera y soporte.';

        try {
            $groq = app(GroqAssistantService::class);
            $reply = $groq->chat($user, $message, [], $groq->buildUserContext($user));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            if (Schema::hasTable('assistant_mensajes')) {
                $now = now()->toDateTimeString();
                $uid = (int) $user->id;
                DB::table('assistant_mensajes')->insert([
                    ['user_id' => $uid, 'rol' => 'user', 'mensaje' => $message, 'created_at' => $now],
                    ['user_id' => $uid, 'rol' => 'assistant', 'mensaje' => $reply, 'created_at' => $now],
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['ok' => true, 'reply' => $reply]);
    }
}
