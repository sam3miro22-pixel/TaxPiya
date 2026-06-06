<?php

namespace App\Services\Firebase;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseIdentityService
{
    public function isConfigured(): bool
    {
        return config('taxpiya.firebase.use_firebase_auth')
            && (string) config('firebase.web.api_key') !== '';
    }

    /**
     * @return array{localId: string, idToken: string, email: string|null}
     */
    public function signUp(string $email, string $password): array
    {
        $data = $this->post('accounts:signUp', [
            'email'             => $email,
            'password'          => $password,
            'returnSecureToken' => true,
        ]);

        if (!empty($data['localId'])) {
            return [
                'localId' => (string) $data['localId'],
                'idToken' => (string) ($data['idToken'] ?? ''),
                'email'   => $data['email'] ?? $email,
            ];
        }

        $code = $data['error']['message'] ?? 'UNKNOWN';
        if ($code === 'EMAIL_EXISTS') {
            return $this->signInWithPassword($email, $password);
        }

        throw new \RuntimeException($this->humanMessage($code));
    }

    /**
     * @return array{localId: string, idToken: string, email: string|null}
     */
    public function signInWithPassword(string $email, string $password): array
    {
        $data = $this->post('accounts:signInWithPassword', [
            'email'             => $email,
            'password'          => $password,
            'returnSecureToken' => true,
        ]);

        if (!empty($data['localId'])) {
            return [
                'localId' => (string) $data['localId'],
                'idToken' => (string) ($data['idToken'] ?? ''),
                'email'   => $data['email'] ?? $email,
            ];
        }

        throw new \RuntimeException($this->humanMessage($data['error']['message'] ?? 'INVALID_LOGIN_CREDENTIALS'));
    }

    /**
     * @return array<string, mixed>
     */
    private function post(string $action, array $payload): array
    {
        $apiKey = (string) config('firebase.web.api_key');
        if ($apiKey === '') {
            throw new \RuntimeException('Firebase API key no configurada');
        }

        $url = "https://identitytoolkit.googleapis.com/v1/{$action}?key=" . urlencode($apiKey);

        try {
            $response = Http::timeout(12)
                ->acceptJson()
                ->post($url, $payload);
        } catch (\Throwable $e) {
            Log::warning('Firebase Identity API error', ['action' => $action, 'err' => $e->getMessage()]);
            throw new \RuntimeException('No se pudo conectar con Firebase Auth');
        }

        return $response->json() ?: [];
    }

    private function humanMessage(string $code): string
    {
        if (str_contains($code, 'WEAK_PASSWORD')) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        return match ($code) {
            'EMAIL_EXISTS'              => 'Este correo ya está registrado. Inicia sesión.',
            'INVALID_LOGIN_CREDENTIALS',
            'INVALID_PASSWORD',
            'EMAIL_NOT_FOUND'           => 'Correo o contraseña incorrectos.',
            'USER_DISABLED'             => 'Esta cuenta está deshabilitada.',
            'TOO_MANY_ATTEMPTS_TRY_LATER' => 'Demasiados intentos. Espera un momento e intenta de nuevo.',
            default                     => 'No se pudo autenticar con Firebase (' . $code . ')',
        };
    }
}
