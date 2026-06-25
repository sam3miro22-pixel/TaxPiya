<?php

namespace App\Console\Commands;

use App\Services\AccountPurgeService;
use App\Services\UserAccountService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FirebaseCleanupCommand extends Command
{
    protected $signature = 'taxpiya:firebase-cleanup
                            {--once : Ejecutar una sola vez (marca en BD)}
                            {--force : Sin confirmación}';

    protected $description = 'Elimina usuarios Firebase de prueba y cuentas SQLite sin firebase_uid (excepto demo)';

    public function handle(AccountPurgeService $purge): int
    {
        $marker = '_taxpiya_firebase_cleanup_v2@internal.local';

        if ($this->option('once') && Schema::hasTable('users')) {
            if (DB::table('users')->where('email', $marker)->exists()) {
                $this->line('Limpieza Firebase ya ejecutada (--once).');

                return self::SUCCESS;
            }
        }

        if (!$this->option('force') && !$this->confirm('¿Eliminar usuarios Firebase de prueba y cuentas locales huérfanas?')) {
            return self::SUCCESS;
        }

        $firebaseDeleted = 0;
        if (config('taxpiya.firebase.use_firebase_auth')) {
            try {
                $firebaseDeleted = $purge->purgeFirebaseAuth();
                $this->info("Firebase Auth: {$firebaseDeleted} usuarios eliminados.");
            } catch (\Throwable $e) {
                $this->warn('Firebase purge: ' . $e->getMessage());
            }
        }

        $localDeleted = $purge->purgeLocalShadowAccounts();
        $this->info("SQLite: {$localDeleted} cuentas locales sin Firebase eliminadas.");

        try {
            $repair = app(UserAccountService::class)->repairDuplicateAccounts();
            $this->info("Duplicados fusionados: {$repair['merged']} en {$repair['groups']} grupos.");
        } catch (\Throwable $e) {
            $this->warn('Repair duplicados: ' . $e->getMessage());
        }

        if ($this->option('once') && Schema::hasTable('users') && !DB::table('users')->where('email', $marker)->exists()) {
            DB::table('users')->insert([
                'name'         => 'Firebase Cleanup Marker',
                'email'        => $marker,
                'telefono'     => '0000000002',
                'password'     => bcrypt(\Illuminate\Support\Str::random(32)),
                'estado'       => 0,
                'user_role_id' => 1,
            ]);
        }

        return self::SUCCESS;
    }
}
