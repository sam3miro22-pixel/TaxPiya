<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionGuardService
{
    /**
     * Cierra otras sesiones Laravel del mismo usuario (una sesión activa por cuenta).
     */
    public function invalidateOtherSessions(Request $request, int $userId): void
    {
        try {
            if (!Schema::hasTable('sessions') || !Schema::hasColumn('sessions', 'user_id')) {
                return;
            }

            $currentId = $request->session()->getId();

            DB::table('sessions')
                ->where('user_id', $userId)
                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                ->delete();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
