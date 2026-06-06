<?php

namespace App\Console\Commands;

use App\Services\SqlitePersistenceService;
use Illuminate\Console\Command;

class SqliteBackupCommand extends Command
{
    protected $signature = 'taxpiya:sqlite-backup';

    protected $description = 'Respalda la base SQLite en GitHub (repo taxpiya-db-backup).';

    public function handle(SqlitePersistenceService $persistence): int
    {
        if (!$persistence->isEnabled()) {
            $this->warn('Respaldo SQLite desactivado o Firebase no configurado.');
            return self::SUCCESS;
        }

        if ($persistence->backup()) {
            $this->info('Respaldo SQLite subido correctamente.');
            return self::SUCCESS;
        }

        $this->error('No se pudo crear el respaldo SQLite.');
        return self::FAILURE;
    }
}
