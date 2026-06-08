<?php

namespace App\Console\Commands;

use App\Services\AccountPurgeService;
use App\Services\DemoAccountCatalog;
use Illuminate\Console\Command;

class PurgeNonDemoCommand extends Command
{
    protected $signature = 'taxpiya:purge-non-demo
                            {--force : Sin confirmación}
                            {--no-firebase : No borrar cuentas en Firebase Auth}
                            {--reseed : Ejecutar seed demo después}';

    protected $description = 'Elimina todas las cuentas excepto las demo (SQLite + Firebase)';

    public function handle(AccountPurgeService $purge): int
    {
        if (!$this->option('force') && !$this->confirm('¿Eliminar TODAS las cuentas que no sean demo?')) {
            return self::SUCCESS;
        }

        $this->line('Cuentas demo que se conservan:');
        foreach (DemoAccountCatalog::keepEmails() as $email) {
            $this->line("  - {$email}");
        }

        try {
            $result = $purge->purgeNonDemo(!$this->option('no-firebase'));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Purga completada:');
        $this->table(['Recurso', 'Eliminados'], [
            ['Usuarios MySQL', $result['users']],
            ['Conductores', $result['conductores']],
            ['Empresas', $result['empresas']],
            ['Referidos', $result['referidos']],
            ['Viajes', $result['viajes']],
            ['Firebase Auth', $result['firebase']],
        ]);

        if ($this->option('reseed')) {
            $this->call('taxpiya:seed-demo', ['--force' => true]);
        }

        return self::SUCCESS;
    }
}
