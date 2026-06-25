<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedDemoCommand extends Command
{
    protected $signature = 'taxpiya:seed-demo
                            {--force : Ejecutar sin confirmación}
                            {--no-firebase : No crear/actualizar cuentas Firebase}
                            {--bootstrap-once : Recrear cuentas demo canónicas una sola vez (SQLite + Firebase)}';

    protected $description = 'Crea/actualiza usuarios demo (admin, pasajero, conductor, empresa) con contraseña unificada';

    public function handle(): int
    {
        $script = base_path('scripts/seed-all-demo.php');
        if (!is_file($script)) {
            $this->error('No se encontró scripts/seed-all-demo.php');
            return self::FAILURE;
        }

        $bootstrapMarker = '_taxpiya_demo_bootstrap_v2@internal.local';
        if ($this->option('bootstrap-once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            if (\Illuminate\Support\Facades\DB::table('users')->where('email', $bootstrapMarker)->exists()) {
                $this->line('Bootstrap demo canónico ya ejecutado (--bootstrap-once).');

                return self::SUCCESS;
            }
            $this->input->setOption('force', true);
        }

        if ($this->option('no-firebase') && !$this->option('bootstrap-once')) {
            putenv('TAXPIYA_SEED_NO_FIREBASE=1');
        }

        passthru('php ' . escapeshellarg($script), $code);

        if ($code === 0 && $this->option('bootstrap-once') && \Illuminate\Support\Facades\Schema::hasTable('users')) {
            if (!\Illuminate\Support\Facades\DB::table('users')->where('email', $bootstrapMarker)->exists()) {
                \Illuminate\Support\Facades\DB::table('users')->insert([
                    'name'         => 'Demo Bootstrap Marker',
                    'email'        => $bootstrapMarker,
                    'telefono'     => '0000000004',
                    'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
                    'estado'       => 0,
                    'user_role_id' => 1,
                ]);
            }
            $this->info('Bootstrap demo canónico completado (4 SQLite + 2 Firebase).');
        }

        return $code === 0 ? self::SUCCESS : self::FAILURE;
    }
}
