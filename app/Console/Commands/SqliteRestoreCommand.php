<?php

namespace App\Console\Commands;

use App\Services\SqlitePersistenceService;
use Illuminate\Console\Command;

class SqliteRestoreCommand extends Command
{
    protected $signature = 'taxpiya:sqlite-restore {--force : Restaurar aunque exista BD local}';

    protected $description = 'Restaura SQLite desde GitHub al arrancar el contenedor.';

    public function handle(SqlitePersistenceService $persistence): int
    {
        if (!$persistence->isEnabled()) {
            $this->warn('Restauración SQLite desactivada o Firebase no configurado.');
            return self::SUCCESS;
        }

        if ($persistence->restore()) {
            $this->info('Base de datos restaurada desde la nube.');
            return self::SUCCESS;
        }

        $this->line('Sin respaldo en la nube o restauración no necesaria.');
        return self::SUCCESS;
    }
}
