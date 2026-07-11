<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class WhatsAppHealthCommand extends Command
{
    protected $signature = 'taxpiya:whatsapp-health {--reconnect : Forzar reconexión si no está conectado}';

    protected $description = 'Verifica WhatsApp Baileys y reconecta o reinicia si hace falta';

    public function handle(WhatsAppService $wa): int
    {
        $status = $wa->getStatus();
        $state  = (string) ($status['status'] ?? 'unavailable');

        if ($state === 'connected') {
            if ($this->isDeafSession($status)) {
                $this->warn('WhatsApp: deaf session — restarting process');
                $wa->restartProcess();
                return self::SUCCESS;
            }

            $this->line('WhatsApp: connected (' . ($status['user'] ?? '?') . ')');
            return self::SUCCESS;
        }

        $this->warn('WhatsApp: ' . $state);

        if (!$this->option('reconnect') && $state === 'qr') {
            return self::SUCCESS;
        }

        if ($state === 'unavailable') {
            $result = $wa->restartProcess();
            $this->line('Restart: ' . ($result['message'] ?? json_encode($result)));
            return self::SUCCESS;
        }

        $result = $wa->reconnect();
        $newState = (string) ($result['status'] ?? $state);
        $this->line('Reconnect: ' . ($result['message'] ?? $newState));

        return ($newState === 'connected' || $newState === 'connecting') ? self::SUCCESS : self::FAILURE;
    }

    /** @param array<string,mixed> $status */
    private function isDeafSession(array $status): bool
    {
        $lastEventAt = (int) ($status['lastEventAt'] ?? 0);
        if ($lastEventAt <= 0) {
            return false;
        }

        return (time() * 1000 - $lastEventAt) > (12 * 60 * 1000);
    }
}
