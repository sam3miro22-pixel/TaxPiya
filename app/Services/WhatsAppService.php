<?php

namespace App\Services;

class WhatsAppService
{
    private string $baseUrl;

    public function __construct()
    {
        $port = (int) config('taxpiya.whatsapp.port', 8051);
        $this->baseUrl = 'http://127.0.0.1:' . $port;
    }

    /**
     * Get current connection status and QR data from the Baileys service.
     */
    public function getStatus(): array
    {
        try {
            $ch = curl_init($this->baseUrl . '/status');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_FAILONERROR    => false,
            ]);
            $body = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($body === false || $code < 200 || $code >= 300) {
                return ['ok' => false, 'status' => 'unavailable', 'error' => 'Service unreachable'];
            }

            $data = json_decode($body, true);
            return $data ?? ['ok' => false, 'status' => 'unavailable'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 'unavailable', 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a WhatsApp message to the given phone number.
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            $ch = curl_init($this->baseUrl . '/send');
            $payload = json_encode(['phone' => $phone, 'message' => $message]);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            ]);
            $body = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($body === false || $code < 200 || $code >= 300) {
                return ['ok' => false, 'error' => 'Failed to send message (HTTP ' . $code . ')'];
            }

            return json_decode($body, true) ?? ['ok' => false];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Logout and clear the WhatsApp session so a new QR can be scanned.
     */
    public function logout(): array
    {
        try {
            $ch = curl_init($this->baseUrl . '/logout');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => '{}',
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            ]);
            $body = curl_exec($ch);
            curl_close($ch);
            return json_decode($body, true) ?? ['ok' => false];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fuerza reconexión del socket Baileys (localhost).
     */
    public function reconnect(): array
    {
        try {
            $ch = curl_init($this->baseUrl . '/reconnect');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 25,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => '{}',
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            ]);
            $body = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($body === false || $code < 200 || $code >= 300) {
                return ['ok' => false, 'status' => 'unavailable', 'message' => 'Reconnect failed HTTP ' . $code];
            }

            return json_decode($body, true) ?? ['ok' => false];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Envía mensaje; si WhatsApp está caído intenta reconectar una vez.
     */
    public function sendMessageResilient(string $phone, string $message): array
    {
        $result = $this->sendMessage($phone, $message);
        if ($result['ok'] ?? false) {
            return $result;
        }

        $status = $this->getStatus();
        if (($status['status'] ?? '') !== 'connected') {
            $this->reconnect();
            sleep(2);
            return $this->sendMessage($phone, $message);
        }

        return $result;
    }
}
