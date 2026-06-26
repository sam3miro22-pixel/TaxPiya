<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChatBotService
{
    public function postSystemMessage(int $viajeId, string $message): ?int
    {
        if (!Schema::hasTable('chat_mensajes')) {
            return null;
        }

        return (int) DB::table('chat_mensajes')->insertGetId([
            'viaje_id'      => $viajeId,
            'remitente_id'  => 0,
            'remitente_rol' => 'sistema',
            'tipo'          => 'system',
            'mensaje'       => $message,
            'created_at'    => now(),
        ]);
    }

    public function onTripAssigned(int $viajeId, ?string $codigoLlegada = null): void
    {
        $msg = 'Tu conductor fue asignado. Puedes escribir aqui si necesitas algo.';
        if ($codigoLlegada) {
            $msg .= " Cuando llegue, comparte el codigo {$codigoLlegada} si te lo pide.";
        }
        $this->postSystemMessage($viajeId, $msg);
    }

    public function onTripStateChange(int $viajeId, string $estado): void
    {
        $messages = [
            'en_camino' => 'El conductor va en camino hacia tu ubicacion.',
            'llego'     => 'El conductor ha llegado. Confirma cuando subas al taxi.',
            'iniciado'  => 'Viaje iniciado. Buen recorrido!',
            'terminado' => 'Viaje finalizado. Gracias por usar Taxpiya.',
        ];

        if (isset($messages[$estado])) {
            $this->postSystemMessage($viajeId, $messages[$estado]);
        }
    }

    public function maybeReply(int $viajeId, string $userRole, string $message): ?int
    {
        if (in_array($userRole, ['system', 'sistema'], true)) {
            return null;
        }

        $text = mb_strtolower(trim($message));
        if ($text === '') {
            return null;
        }

        $reply = $this->regexReply($viajeId, $text);
        if ($reply === null) {
            return null;
        }

        return $this->postSystemMessage($viajeId, $reply);
    }

    private function regexReply(int $viajeId, string $text): ?string
    {
        if (preg_match('/\b(ayuda|help|soporte)\b/u', $text)) {
            return 'Soy el asistente Taxpiya. Puedes preguntar por tarifa, tiempo, codigo o cancelacion.';
        }
        if (preg_match('/\b(tarifa|precio|cuanto|cuesta|valor)\b/u', $text)) {
            $viaje = DB::table('viajes')->where('id', $viajeId)->first();
            if ($viaje && $viaje->tarifa_aplicada !== null) {
                $monto = number_format((float) $viaje->tarifa_aplicada, 0, ',', '.');
                $moneda = $viaje->moneda ?? 'COP';
                return "La tarifa estimada de este viaje es \${$monto} {$moneda}.";
            }
            return 'La tarifa se confirmara segun la ruta acordada.';
        }
        if (preg_match('/\b(codigo|llegada)\b/u', $text)) {
            $viaje = DB::table('viajes')->where('id', $viajeId)->first();
            if ($viaje && !empty($viaje->codigo_llegada)) {
                return "Tu codigo de llegada es {$viaje->codigo_llegada}.";
            }
            return 'El codigo de llegada aparecera cuando se asigne un conductor.';
        }
        if (preg_match('/\b(cancelar|cancel)\b/u', $text)) {
            return 'Para cancelar usa el boton Cancelar servicio en la pantalla del viaje.';
        }
        if (preg_match('/\b(hola|buenas|buenos)\b/u', $text)) {
            return 'Hola! Estoy aqui para ayudarte durante el viaje.';
        }

        return null;
    }
}
