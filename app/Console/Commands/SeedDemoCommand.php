<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedDemoCommand extends Command
{
    protected $signature = 'taxpiya:seed-demo {--force : Ejecutar sin confirmación} {--no-firebase : No crear/actualizar cuentas Firebase}';

    protected $description = 'Crea/actualiza usuarios demo (admin, pasajero, conductor, empresa) con contraseña unificada';

    public function handle(): int
    {
        $script = base_path('scripts/seed-all-demo.php');
        if (!is_file($script)) {
            $this->error('No se encontró scripts/seed-all-demo.php');
            return self::FAILURE;
        }

        if ($this->option('no-firebase')) {
            putenv('TAXPIYA_SEED_NO_FIREBASE=1');
        }

        passthru('php ' . escapeshellarg($script), $code);
        return $code === 0 ? self::SUCCESS : self::FAILURE;
    }
}
