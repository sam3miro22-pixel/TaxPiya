<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionUserValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        $row = DB::table('users')->where('id', $user->id)->first();
        if (!$row) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Tu sesión ya no es válida. Cierra sesión e inicia de nuevo.',
                ], 401);
            }

            return redirect()->route('pasajero.login')
                ->with('auth_error', 'Tu sesión ya no es válida. Inicia sesión de nuevo.');
        }

        return $next($request);
    }
}
