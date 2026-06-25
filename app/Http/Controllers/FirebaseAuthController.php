<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Services\PortalAuthService;
use App\Services\SessionGuardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    /**
     * Verifica token Firebase y abre sesión Laravel (endpoint principal).
     */
    public function syncSession(Request $request): JsonResponse
    {
        try {
            $idToken = (string) $request->input('id_token', '');
            $app     = (string) $request->input('app', 'pasajero');

            if ($idToken === '') {
                return response()->json(['ok' => false, 'message' => 'id_token requerido'], 422);
            }

            if (!in_array($app, ['pasajero', 'conductor'], true)) {
                return response()->json(['ok' => false, 'message' => 'Portal no válido'], 422);
            }

            if (!config('taxpiya.firebase.use_firebase_auth')) {
                return response()->json(['ok' => false, 'message' => 'Firebase Auth desactivado'], 503);
            }

            $fbUser = $this->lookupFirebaseUser($idToken);
            if (!$fbUser) {
                return response()->json(['ok' => false, 'message' => 'Token de Firebase inválido'], 401);
            }

            $uid   = (string) ($fbUser['localId'] ?? '');
            $email = isset($fbUser['email']) ? strtolower(trim((string) $fbUser['email'])) : '';

            if ($email === '') {
                return response()->json(['ok' => false, 'message' => 'El token no incluye correo electrónico'], 422);
            }

            $user  = $this->findUserForFirebaseLogin($email, $uid);
            $isNew = false;

            if (!$user) {
                if ($app === 'conductor') {
                    return response()->json([
                        'ok'      => false,
                        'message' => 'No tienes cuenta de conductor activa. Solicita registro y espera aprobación.',
                    ], 403);
                }

                $isNew = true;
                $user  = Users::create([
                    'firebase_uid' => $uid,
                    'name'         => $request->input('name') ?: ($fbUser['displayName'] ?? 'Usuario Taxpiya'),
                    'email'        => $email,
                    'telefono'     => 'fb_' . preg_replace('/[^a-zA-Z0-9]/', '', $uid),
                    'password'     => bcrypt(Str::random(32)),
                    'estado'       => 1,
                    'user_role_id' => 2,
                ]);
                $user->assignRole('Pasajero');
            }

            $portal = app(PortalAuthService::class);
            if (!$portal->userMatchesPortal($user, $app)) {
                return response()->json([
                    'ok'      => false,
                    'message' => $portal->roleMismatchMessage($app) ?? 'No tienes acceso a este portal.',
                ], 403);
            }

            if ($err = $portal->validateLoginGate($user, $app)) {
                return response()->json(['ok' => false, 'message' => $err], 403);
            }

            if (!Auth::loginUsingId((int) $user->id, false)) {
                return response()->json(['ok' => false, 'message' => 'No se pudo iniciar sesión.'], 500);
            }

            $request->session()->save();

            try {
                if ($uid !== '' && (string) ($user->firebase_uid ?? '') !== $uid) {
                    DB::table('users')
                        ->where('firebase_uid', $uid)
                        ->where('id', '!=', $user->id)
                        ->update(['firebase_uid' => null]);
                    DB::table('users')->where('id', $user->id)->update(['firebase_uid' => $uid]);
                }
                app(SessionGuardService::class)->invalidateOtherSessions($request, (int) $user->id);
            } catch (\Throwable $e) {
                report($e);
            }

            return response()->json([
                'ok'       => true,
                'user_id'  => $user->id,
                'is_new'   => $isNew,
                'redirect' => '/home',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'Sync: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** @deprecated alias */
    public function diagSyncProbe(Request $request): JsonResponse
    {
        return $this->syncSession($request);
    }

    /**
     * Busca usuario por correo del token (misma lógica que sync-minimal probada en prod).
     */
    private function findUserForFirebaseLogin(string $email, string $uid): ?Users
    {
        $user = Users::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->orderByDesc('id')
            ->first();

        if ($user) {
            return $user;
        }

        if ($uid !== '') {
            return Users::query()->where('firebase_uid', $uid)->orderByDesc('id')->first();
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lookupFirebaseUser(string $idToken): ?array
    {
        $apiKey = config('firebase.web.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('FIREBASE_API_KEY no configurada');
        }

        $client = new \GuzzleHttp\Client(['timeout' => 15]);
        $res    = $client->post(
            'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode((string) $apiKey),
            ['json' => ['idToken' => $idToken]]
        );

        if ($res->getStatusCode() >= 300) {
            return null;
        }

        $body = json_decode((string) $res->getBody(), true);

        return $body['users'][0] ?? null;
    }
}
