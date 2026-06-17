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
            'remitente_rol' => 'system',
            'tipo'          => 'system',
            'mensaje'       => $message,
            'created_at'    => now(),
        ]);
    }

    public function onTripAssigned(int $viajeId, ?string $codigoLlegada = null): void
    {
        $msg = 'Tu conductor fue asignado. Puedes escribir aquí si necesitas algo.';
        if ($codigoLlegada) {
            $msg .= " Cuando llegue, comparte el código {$codigoLlegada} si te lo pide.";
        }
        $this->postSystemMessage($viajeId, $msg);
    }

    public function onTripStateChange(int $viajeId, string $estado): void
    {
        $messages = [
            'en_camino' => 'El conductor va en camino hacia tu ubicación.',
            'llego'     => 'El conductor ha llegado. Confirma cuando subas al taxi.',
            'iniciado'  => 'Viaje iniciado. ¡Buen recorrido!',
            'terminado' => 'Viaje finalizado. Gracias por usar Taxpiya.',
        ];

        if (isset($messages[$estado])) {
            $this->postSystemMessage($viajeId, $messages[$estado]);
        }
    }

    public function maybeReply(int $viajeId, string $userRole, string $message): ?int
    {
        if ($userRole === 'system') {
            return null;
        }

        $text = mb_strtolower(trim($message));
        if ($text === '') {
            return null;
        }

        $reply = null;

        if (preg_match('/\b(ayuda|help|soporte)\b/u', $text)) {
            $reply = 'Soy el asistente Taxpiya. Puedes preguntar por tarifa, tiempo, código o cancelación.';
        } elseif (preg_match('/\b(tarifa|precio|cuanto|cuesta|valor)\b/u', $text)) {
            $viaje = DB::table('viajes')->where('id', $viajeId)->first();
            if ($viaje && $viaje->tarifa_aplicada !== null) {
                $monto = number_format((float) $viaje->tarifa_aplicada, 0, ',', '.');
                $moneda = $viaje->moneda ?? 'COP';
                $reply = "La tarifa estimada de este viaje es \${$monto} {$moneda}.";
            } else {
                $reply = 'La tarifa se confirmará según la ruta acordada.';
            }
        } elseif (preg_match('/\b(tiempo|eta|cuanto tarda|demora)\b/u', $text)) {
            $reply = 'El tiempo de llegada depende del tráfico. Verás la ETA en el mapa cuando el conductor esté en camino.';
        } elseif (preg_match('/\b(codigo|código|llegada)\b/u', $text)) {
            $viaje = DB::table('viajes')->where('id', $viajeId)->first();
            if ($viaje && !empty($viaje->codigo_llegada)) {
                $reply = "Tu código de llegada es {$viaje->codigo_llegada}. Compártelo con el conductor cuando llegue.";
            } else {
                $reply = 'El código de llegada aparecerá cuando se asigne un conductor.';
            }
        } elseif (preg_match('/\b(cancelar|cancel)\b/u', $text)) {
            $reply = 'Para cancelar usa el botón «Cancelar servicio» en la pantalla del viaje.';
        } elseif (preg_match('/\b(hola|buenas|buenos)\b/u', $text)) {
            $reply = '¡Hola! Estoy aquí para ayudarte durante el viaje.';
        } elseif (preg_match('/\b(donde|ubicacion|ubicación|mapa)\b/u', $text)) {
            $reply = 'Puedes ver la ubicación del conductor en el mapa. La burbuja amarilla se actualiza en tiempo real.';
        }

        if ($reply === null) {
            return null;
        }

        return $this->postSystemMessage($viajeId, $reply);
    }
}
