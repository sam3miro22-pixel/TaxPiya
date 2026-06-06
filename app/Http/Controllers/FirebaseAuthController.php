<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Providers\RouteServiceProvider;
use App\Services\Firebase\FirestoreUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    /**
     * Verifica ID token de Firebase y crea sesión Laravel + usuario MySQL.
     */
    public function syncSession(Request $request)
    {
        $request->validate([
            'id_token'    => 'required|string',
            'app'         => 'nullable|in:pasajero,conductor',
            'name'        => 'nullable|string|max:255',
            'telefono'    => 'nullable|string|max:125',
            'is_register' => 'nullable|boolean',
        ]);

        if (!config('taxpiya.firebase.use_firebase_auth')) {
            return response()->json(['ok' => false, 'message' => 'Firebase Auth desactivado'], 503);
        }

        try {
            $claims = $this->verifyIdToken($request->input('id_token'));
        } catch (\Throwable $e) {
            Log::warning('Firebase token inválido', ['err' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Token de Firebase inválido'], 401);
        }

        $uid   = $claims['sub'] ?? $claims['user_id'] ?? null;
        $email = $claims['email'] ?? null;

        if (!$uid) {
            return response()->json(['ok' => false, 'message' => 'Token sin identificador'], 401);
        }

        $user = Users::query()->where('firebase_uid', $uid)->first();

        if (!$user && $email) {
            $user = Users::query()->where('email', $email)->first();
        }

        $isNew = false;
        if (!$user) {
            $isNew = true;
            $name = $request->input('name') ?: ($claims['name'] ?? 'Usuario Taxpiya');
            $tel  = $request->input('telefono') ?: ('fb_' . substr($uid, 0, 12));

            $user = Users::create([
                'firebase_uid'  => $uid,
                'name'          => $name,
                'email'         => $email ?: ($uid . '@firebase.taxpiya.local'),
                'telefono'      => $tel,
                'password'      => bcrypt(Str::random(32)),
                'estado'        => 1,
            ]);
            $user->assignRole('Pasajero');
        } else {
            if (empty($user->firebase_uid)) {
                $user->firebase_uid = $uid;
            }
            if ($request->filled('name')) {
                $user->name = $request->input('name');
            } elseif (!empty($claims['name'])) {
                $user->name = $claims['name'];
            }
            if ($request->filled('telefono')) {
                $user->telefono = $request->input('telefono');
            }
            $user->save();
        }

        if ((int) ($user->estado ?? 1) !== 1) {
            return response()->json(['ok' => false, 'message' => 'Cuenta inactiva'], 403);
        }

        $app = $request->input('app');
        if ($app === 'conductor' && !$user->hasRole('Conductor')) {
            return response()->json(['ok' => false, 'message' => 'Acceso exclusivo para Conductores'], 403);
        }
        if ($app === 'conductor') {
            $conductor = DB::table('conductores')->where('user_id', $user->id)->first();
            if (!$conductor || (int) ($conductor->estado_operitivo ?? 0) !== 1) {
                return response()->json(['ok' => false, 'message' => 'Conductor no activo'], 403);
            }
        }
        if ($app === 'pasajero' && !$user->hasRole('Pasajero')) {
            return response()->json(['ok' => false, 'message' => 'Acceso solo para Pasajeros'], 403);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        app(FirestoreUserService::class)->upsertFromUser($user, $app ?: 'pasajero');

        return response()->json([
            'ok'       => true,
            'user_id'  => $user->id,
            'is_new'   => $isNew,
            'redirect' => url(RouteServiceProvider::homeForUser($user, $app)),
        ]);
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

            $verified = $auth->verifyIdToken($idToken);

            return $verified->claims()->all();
        }

        // Fallback REST si kreait no está instalado aún
        return $this->verifyIdTokenViaGoogle($idToken);
    }

    /**
     * @return array<string, mixed>
     */
    private function verifyIdTokenViaGoogle(string $idToken): array
    {
        $url = 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode(config('firebase.web.api_key'));
        $client = new \GuzzleHttp\Client(['timeout' => 10]);
        $res = $client->post($url, [
            'json' => ['idToken' => $idToken],
        ]);
        $body = json_decode((string) $res->getBody(), true);
        $u = $body['users'][0] ?? null;
        if (!$u) {
            throw new \RuntimeException('Token no válido');
        }

        return [
            'sub'   => $u['localId'],
            'email' => $u['email'] ?? null,
            'name'  => $u['displayName'] ?? null,
        ];
    }
}
