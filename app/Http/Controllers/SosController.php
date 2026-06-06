<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SosController extends Controller
{
    public function reportar(Request $request)
    {
        if (!Schema::hasTable('sos_incidentes')) {
            return response()->json(['ok' => false, 'message' => 'SOS no disponible'], 503);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        $validated = $request->validate([
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
            'viaje_id'    => 'nullable|integer',
            'categoria'   => 'nullable|in:seguridad,accidente,salud,acoso,vehiculo,otro',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $actorTipo = $user->hasRole('conductor') ? 'conductor' : 'pasajero';
        $conductorId = null;

        if ($actorTipo === 'conductor') {
            $conductorId = DB::table('conductores')->where('user_id', $user->id)->value('id');
        } elseif (!empty($validated['viaje_id'])) {
            $conductorId = DB::table('viajes')->where('id', (int) $validated['viaje_id'])->value('conductor_id');
        }

        $now = now()->format('Y-m-d H:i:s');

        $id = DB::table('sos_incidentes')->insertGetId([
            'viaje_id'           => $validated['viaje_id'] ?? null,
            'actor_tipo'         => $actorTipo,
            'actor_user_id'      => $user->id,
            'conductor_id'       => $conductorId,
            'categoria'          => $validated['categoria'] ?? 'seguridad',
            'severidad'          => 'alta',
            'estado'             => 'abierto',
            'descripcion'        => $validated['descripcion'] ?? 'Alerta SOS desde la app móvil',
            'telefono_contacto'  => $user->telefono,
            'lat'                => $validated['lat'] ?? null,
            'lng'                => $validated['lng'] ?? null,
            'created_at'         => $now,
        ]);

        try {
            $adminIds = DB::table('users')->where('user_role_id', 1)->pluck('id')->map(fn ($v) => (int) $v)->all();
            if ($adminIds) {
                app(\App\Services\PushService::class)->notifyUsers(
                    $adminIds,
                    'Alerta SOS',
                    "Nuevo incidente #{$id} — {$actorTipo}",
                    ['t' => 'sos', 'id' => (string) $id]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('FCM SOS admin falló', ['id' => $id, 'err' => $e->getMessage()]);
        }

        return response()->json([
            'ok'      => true,
            'id'      => $id,
            'message' => 'SOS enviado. El equipo de soporte fue notificado.',
        ]);
    }
}
