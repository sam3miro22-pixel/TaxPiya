<?php

namespace App\Services;

use App\Models\Users;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FormLoginService
{
    public function handle(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'app'      => 'nullable|in:pasajero,conductor,empresa',
        ]);

        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('rememberme', (bool) config('taxpiya.session.remember_default', true));
        $app      = $request->input('app');
        $isEmail  = filter_var($username, FILTER_VALIDATE_EMAIL) !== false;

        if ($isEmail && PortalAuthService::firebasePasajeroConductorEnabled() && in_array($app, ['pasajero', 'conductor'], true)) {
            return $this->errorResponse($request, $app, 'Para entrar con correo usa Google o el botón «Correo electrónico (Firebase)» debajo del formulario.');
        }

        $portalAuth = app(PortalAuthService::class);
        if ($app && in_array($app, ['pasajero', 'conductor', 'empresa'], true)) {
            $candidate = $this->findUser($username, $isEmail);
            if ($candidate) {
                $roleError = $portalAuth->validateRoleForPortal($candidate, $app);
                if ($roleError) {
                    return $this->errorResponse($request, $app, $roleError);
                }
            }
        }

        if (!$this->attemptLogin($username, $password, $isEmail, $remember)) {
            return $this->errorResponse($request, $app, 'Nombre de usuario o contraseña no correctos');
        }

        try {
            $request->session()->regenerate();
            $request->session()->save();
        } catch (\Throwable $e) {
            report($e);
            if (Auth::user()) {
                Auth::login(Auth::user(), false);
                $request->session()->regenerate();
                $request->session()->save();
            }
        }

        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse($request, $app, 'No se pudo iniciar sesión. Intenta de nuevo.');
        }

        $gateError = $portalAuth->validateLoginGate($user, $app);
        if ($gateError) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->errorResponse($request, $app, $gateError);
        }

        return redirect()->intended(RouteServiceProvider::homeForUser($user, $app));
    }

    private function findUser(string $username, bool $isEmail): ?Users
    {
        return Users::query()
            ->when($isEmail, fn ($q) => $q->whereRaw('LOWER(email) = ?', [strtolower($username)]))
            ->when(!$isEmail, fn ($q) => $q->where('telefono', $username))
            ->first();
    }

    private function attemptLogin(string $username, string $password, bool $isEmail, bool $remember): bool
    {
        $credentials = $isEmail
            ? ['email' => $username, 'password' => $password]
            : ['telefono' => $username, 'password' => $password];

        try {
            if (Auth::attempt($credentials, false)) {
                if ($remember && Auth::user()) {
                    Auth::login(Auth::user(), true);
                }

                return true;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $user = $this->findUser($username, $isEmail);
        if (!$user || !Hash::check($password, (string) $user->password)) {
            return false;
        }

        Auth::login($user, $remember);

        return true;
    }

    private function errorResponse(Request $request, ?string $app, string $message): RedirectResponse
    {
        return redirect($this->loginPathForApp($app))
            ->with('auth_error', $message)
            ->with('old_username', (string) $request->input('username', ''));
    }

    private function loginPathForApp(?string $app): string
    {
        return match ($app) {
            'pasajero'  => '/pasajero/login',
            'conductor' => '/conductor/login',
            'empresa'   => '/empresa/login',
            default     => '/index/login',
        };
    }
}
