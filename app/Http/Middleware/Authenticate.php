<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        $path = $request->path();
        if (str_starts_with($path, 'empresa')) {
            return route('empresa.login');
        }
        if (str_starts_with($path, 'conductor')) {
            return route('conductor.login');
        }
        if (str_starts_with($path, 'pasajero')) {
            return route('pasajero.login');
        }

        return route('pasajero.login');
    }
}
