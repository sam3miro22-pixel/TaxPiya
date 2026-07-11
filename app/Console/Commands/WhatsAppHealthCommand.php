<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class WhatsAppHealthCommand extends Command
{
    protected $signature = 'taxpiya:whatsapp-health {--reconnect : Forzar reconexión si no está conectado}';

    protected $description = 'Verifica WhatsApp Baileys y reconecta si hace falta';

    public function handle(WhatsAppService $wa): int
    {
        $status = $wa->getStatus();
        $state  = (string) ($status['status'] ?? 'unavailable');

        if ($state === 'connected') {
            $this->line('WhatsApp: connected (' . ($status['user'] ?? '?') . ')');
            return self::SUCCESS;
        }

        $this->warn('WhatsApp: ' . $state);

        if (!$this->option('reconnect') && $state === 'qr') {
            return self::SUCCESS;
        }

        $result = $wa->reconnect();
        $newState = (string) ($result['status'] ?? $state);
        $this->line('Reconnect: ' . ($result['message'] ?? $newState));

        return ($newState === 'connected' || $newState === 'connecting') ? self::SUCCESS : self::FAILURE;
    }
}
