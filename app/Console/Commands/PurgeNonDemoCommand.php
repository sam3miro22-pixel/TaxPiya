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
                            {--reseed : Ejecutar seed demo después}
                            {--clean-transactions : Borrar viajes, calificaciones y billeteras de todos}
                            {--reset-once : Limpieza total una sola vez (marca en BD)}
                            {--once : Omitir si ya se ejecutó (marca en SQLite)}';

    protected $description = 'Elimina todas las cuentas excepto las demo (SQLite + Firebase)';

    public function handle(AccountPurgeService $purge): int
    {
        if ($this->option('reset-once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            $marker = '_taxpiya_reset_data_v1@internal.local';
            if (\Illuminate\Support\Facades\DB::table('users')->where('email', $marker)->exists()) {
                $this->line('Reset de datos demo ya ejecutado (--reset-once).');

                return self::SUCCESS;
            }
            $this->input->setOption('force', true);
            $this->input->setOption('clean-transactions', true);
            $this->input->setOption('reseed', true);
            $this->input->setOption('no-firebase', true);
        }

        if ($this->option('once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            $done = \Illuminate\Support\Facades\DB::table('users')
                ->where('email', '_taxpiya_purge_done@internal.local')
                ->exists();
            if ($done) {
                $this->line('Purga ya ejecutada anteriormente (--once).');

                return self::SUCCESS;
            }
        }

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

        if ($this->option('clean-transactions')) {
            $clean = $purge->purgeAllTransactionalData();
            $this->info('Datos transaccionales limpiados:');
            foreach ($clean as $k => $v) {
                $this->line("  {$k}: {$v}");
            }
        }

        if ($this->option('reseed')) {
            $this->call('taxpiya:seed-demo', ['--force' => true]);
        }

        if ($this->option('once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            $exists = \Illuminate\Support\Facades\DB::table('users')
                ->where('email', '_taxpiya_purge_done@internal.local')
                ->exists();
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('users')->insert([
                    'name'         => 'Purge Marker',
                    'email'        => '_taxpiya_purge_done@internal.local',
                    'telefono'     => '0000000000',
                    'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
                    'estado'       => 0,
                    'user_role_id' => 1,
                ]);
            }
        }

        if ($this->option('reset-once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            $marker = '_taxpiya_reset_data_v1@internal.local';
            if (!\Illuminate\Support\Facades\DB::table('users')->where('email', $marker)->exists()) {
                \Illuminate\Support\Facades\DB::table('users')->insert([
                    'name'         => 'Reset Marker',
                    'email'        => $marker,
                    'telefono'     => '0000000001',
                    'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
                    'estado'       => 0,
                    'user_role_id' => 1,
                ]);
            }
        }

        return self::SUCCESS;
    }
}
