<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Services\Firebase\FirestoreUserService;
use App\Services\PortalAuthService;
use App\Services\ReferralService;
use App\Services\UserAccountService;
use App\Services\SqlitePersistenceService;
use App\Services\WalletLedgerService;
use App\Services\SessionGuardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    /**
     * Verifica ID token de Firebase y crea sesión Laravel + usuario MySQL.
     */
    public function syncSession(Request $request): JsonResponse
    {
        return $this->diagSyncProbe($request);
    }

    /**
     * Diagnóstico y sync Firebase (endpoint principal).
     */
    public function diagSyncProbe(Request $request): JsonResponse
    {
        try {
            return $this->performFirebaseSync($request);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'Error al sincronizar la sesión. Intenta de nuevo.',
            ], 500);
        }
    }

    private function performFirebaseSync(Request $request): JsonResponse
    {
        $request->validate([
            'id_token'      => 'required|string',
            'app'           => 'nullable|in:pasajero,conductor',
            'name'          => 'nullable|string|max:255',
            'telefono'      => 'nullable|string|max:125',
            'is_register'   => 'nullable|boolean',
            'referral_code' => 'nullable|string|max:20',
        ]);

        if (!config('taxpiya.firebase.use_firebase_auth')) {
            return response()->json(['ok' => false, 'message' => 'Firebase Auth desactivado'], 503);
        }

        $app = $request->input('app', 'pasajero');
        if (!in_array($app, ['pasajero', 'conductor'], true)) {
            return response()->json(['ok' => false, 'message' => 'Firebase Auth solo está disponible para pasajero y conductor'], 422);
        }

        $referrals = app(ReferralService::class);
        $refCode   = $request->input('referral_code');
        if ($request->boolean('is_register') && $referrals->normalizeCode($refCode)) {
            $check = $referrals->validateCode($refCode);
            if (!$check['ok']) {
                return response()->json(['ok' => false, 'message' => $check['message'] ?? 'Código de referido inválido'], 422);
            }
        }

        try {
            $claims = $this->verifyIdToken($request->input('id_token'));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'message' => 'Token de Firebase inválido'], 401);
        }

        $uid   = (string) ($claims['sub'] ?? $claims['user_id'] ?? '');
        $email = $claims['email'] ?? null;

        if ($uid === '') {
            return response()->json(['ok' => false, 'message' => 'Token sin identificador'], 401);
        }

        $result = $this->provisionFirebaseUser($request, $claims, $uid, $email, $app);
        if ($result instanceof JsonResponse) {
            return $result;
        }

        $user  = $result['user'];
        $isNew = $result['is_new'];

        $referralResult = null;
        if ($app === 'pasajero' && $referrals->normalizeCode($refCode)) {
            $referralResult = $referrals->applyPasajeroReferral(
                $refCode,
                (int) $user->id,
                $isNew,
                $request->boolean('is_register')
            );
        }

        try {
            app(FirestoreUserService::class)->upsertFromUser($user, $app);
        } catch (\Throwable) {
        }

        $payload = [
            'ok'       => true,
            'user_id'  => $user->id,
            'is_new'   => $isNew,
            'redirect' => '/home',
        ];
        if (is_array($referralResult)) {
            $payload['referral'] = [
                'applied'    => empty($referralResult['skipped']),
                'referido_id'=> $referralResult['referido_id'] ?? null,
                'bonus_ok'   => $referralResult['bonus']['ok'] ?? null,
            ];
            if (!empty($referralResult['bonus']['ok'])) {
                SqlitePersistenceService::scheduleBackupAfterRequest();
            }
        }

        if ($isNew) {
            SqlitePersistenceService::scheduleBackupAfterRequest();
        }

        return response()->json($payload);
    }

    /**
     * Crea o vincula usuario MySQL, billetera, código referido y sesión Laravel.
     *
     * @return JsonResponse|array{user:Users,is_new:bool}
     */
    private function provisionFirebaseUser(Request $request, array $claims, string $uid, ?string $email, string $app)
    {
        try {
            return $this->doProvisionFirebaseUser($request, $claims, $uid, $email, $app);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok'      => false,
                'message' => 'Error al preparar la cuenta. Intenta de nuevo.',
            ], 500);
        }
    }

    /**
     * @return JsonResponse|array{user:Users,is_new:bool}
     */
    private function doProvisionFirebaseUser(Request $request, array $claims, string $uid, ?string $email, string $app)
    {
        $accounts = app(UserAccountService::class);
        $telefono = $accounts->normalizeTelefono($request->input('telefono'));
        $user     = $accounts->findByFirebaseIdentity($uid, $email, $telefono);
        $isNew    = false;

        if (!$user) {
            if ($app === 'conductor') {
                return response()->json([
                    'ok'      => false,
                    'message' => 'No tienes cuenta de conductor activa. Solicita registro en /conductor/aplicar y espera la aprobación del administrador.',
                ], 403);
            }

            $isNew = true;
            $name  = $request->input('name') ?: ($claims['name'] ?? 'Usuario Taxpiya');
            $tel   = $telefono ?: ('fb_' . preg_replace('/[^a-zA-Z0-9]/', '', $uid));

            $user = Users::create([
                'firebase_uid'  => $uid,
                'name'          => $name,
                'email'         => $email ?: ($uid . '@firebase.taxpiya.local'),
                'telefono'      => $tel,
                'password'      => bcrypt(Str::random(32)),
                'estado'        => 1,
                'user_role_id'  => 2,
            ]);
            $user->assignRole('Pasajero');
        } else {
            try {
                $accounts->linkFirebaseUid($user, $uid);
            } catch (\Throwable $e) {
                report($e);
                try {
                    DB::table('users')
                        ->where('firebase_uid', $uid)
                        ->where('id', '!=', $user->id)
                        ->update(['firebase_uid' => null]);
                    $user->firebase_uid = $uid;
                    $user->save();
                } catch (\Throwable $e2) {
                    report($e2);
                }
            }

            if ($request->filled('name')) {
                $user->name = $request->input('name');
            } elseif (!empty($claims['name']) && str_starts_with((string) $user->name, 'Usuario ')) {
                $user->name = $claims['name'];
            }

            if ($telefono && (str_starts_with((string) $user->telefono, 'fb_') || empty($user->telefono))) {
                $conflict = Users::query()
                    ->where('telefono', $telefono)
                    ->where('id', '!=', $user->id)
                    ->exists();
                if (!$conflict) {
                    $user->telefono = $telefono;
                }
            }

            if ($email && (str_contains((string) $user->email, '@firebase.taxpiya.local') || str_contains((string) $user->email, '@conductor.taxpiya.local'))) {
                $conflict = Users::query()
                    ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
                    ->where('id', '!=', $user->id)
                    ->exists();
                if (!$conflict) {
                    $user->email = $email;
                }
            }

            try {
                $user->save();
                $user->refresh();
            } catch (\Throwable $e) {
                report($e);
                $user = Users::query()->find($user->id);
                if (!$user) {
                    return response()->json(['ok' => false, 'message' => 'Error al actualizar la cuenta.'], 500);
                }
            }
        }

        $portal = app(PortalAuthService::class);
        if (!$portal->userMatchesPortal($user, $app)) {
            Auth::logout();
            return response()->json([
                'ok'      => false,
                'message' => $portal->roleMismatchMessage($app) ?? 'No tienes acceso a este portal.',
            ], 403);
        }

        $gateError = $portal->validateLoginGate($user, $app);
        if ($gateError) {
            Auth::logout();
            return response()->json(['ok' => false, 'message' => $gateError], 403);
        }

        if ($app === 'conductor') {
            $roleId = DB::table('roles')->where('role_name', 'Conductor')->value('role_id');
            if ($roleId && (int) $user->user_role_id !== (int) $roleId) {
                Auth::logout();
                return response()->json(['ok' => false, 'message' => 'Acceso exclusivo para Conductores.'], 403);
            }
        }

        try {
            app(ReferralService::class)->ensureUserCode($user);
            app(WalletLedgerService::class)->ensureCuenta('pasajero', (int) $user->id);
            app(ReferralService::class)->processPendingBonusesForReferrerUser((int) $user->id);
        } catch (\Throwable) {
        }

        $user = Users::query()->find($user->id);
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        if (!Auth::loginUsingId((int) $user->id, false)) {
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo iniciar sesión. Recarga la página e intenta de nuevo.',
            ], 500);
        }

        try {
            $request->session()->regenerate();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            $request->session()->save();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            app(SessionGuardService::class)->invalidateOtherSessions($request, (int) $user->id);
        } catch (\Throwable $e) {
            report($e);
        }

        return ['user' => $user, 'is_new' => $isNew];
    }

    /**
     * @return array<string, mixed>
     */
    private function verifyIdToken(string $idToken): array
    {
        $credentials = config('firebase.credentials');
        if (!is_readable($credentials)) {
            throw new \RuntimeException(
                'Falta service-account.json en storage/app/firebase/. Descárgalo desde Firebase Console > Configuración del proyecto > Cuentas de servicio.'
            );
        }

        if (class_exists(\Kreait\Firebase\Factory::class)) {
            $auth = (new \Kreait\Firebase\Factory())
                ->withServiceAccount($credentials)
                ->createAuth();

            try {
                $verified = $auth->verifyIdToken($idToken);

                return $verified->claims()->all();
            } catch (\Throwable) {
                return $this->verifyIdTokenViaGoogle($idToken);
            }
        }

        return $this->verifyIdTokenViaGoogle($idToken);
    }

    /**
     * @return array<string, mixed>
     */
    private function verifyIdTokenViaGoogle(string $idToken): array
    {
        $apiKey = config('firebase.web.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('FIREBASE_API_KEY no configurada');
        }

        $url = 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode($apiKey);
        $client = new \GuzzleHttp\Client(['timeout' => 10]);
        $res = $client->post($url, [
            'json' => ['idToken' => $idToken],
        ]);

        if ($res->getStatusCode() >= 300) {
            throw new \RuntimeException('Token lookup failed: HTTP ' . $res->getStatusCode());
        }

        $body = json_decode((string) $res->getBody(), true);
        $u = $body['users'][0] ?? null;
        if (!$u) {
            throw new \RuntimeException('Token no válido');
        }

        return [
            'sub'      => $u['localId'],
            'user_id'  => $u['localId'],
            'email'    => $u['email'] ?? null,
            'name'     => $u['displayName'] ?? null,
        ];
    }
}
