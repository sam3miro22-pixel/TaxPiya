<?php

namespace App\Console\Commands;

use App\Services\SqlitePersistenceService;
use Illuminate\Console\Command;

class SqlitePersistenceStatusCommand extends Command
{
    protected $signature = 'taxpiya:sqlite-status';

    protected $description = 'Muestra estado de persistencia SQLite (local vs nube).';

    public function handle(SqlitePersistenceService $persistence): int
    {
        $status = $persistence->status();
        $this->table(['Clave', 'Valor'], collect($status)->map(fn ($v, $k) => [
            $k,
            is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string) $v,
        ])->values()->all());

        return self::SUCCESS;
    }
}
