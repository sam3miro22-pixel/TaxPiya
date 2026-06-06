<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedDemoCommand extends Command
{
    protected $signature = 'taxpiya:seed-demo {--force : Ejecutar sin confirmación}';

    protected $description = 'Crea/actualiza usuarios demo (admin, pasajero, conductor, empresa) con contraseña unificada';

    public function handle(): int
    {
        $script = base_path('scripts/seed-all-demo.php');
        if (!is_file($script)) {
            $this->error('No se encontró scripts/seed-all-demo.php');
            return self::FAILURE;
        }

        passthru('php ' . escapeshellarg($script), $code);
        return $code === 0 ? self::SUCCESS : self::FAILURE;
    }
}
