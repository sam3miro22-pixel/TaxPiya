<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $minutes = max(1, (int) config('taxpiya.persistence.backup_minutes', 5));

        $schedule->command('taxpiya:sqlite-backup')
            ->everyMinutes($minutes)
            ->withoutOverlapping(10)
            ->when(fn () => config('database.default') === 'sqlite'
                && config('taxpiya.persistence.enabled', true));

        $schedule->command('taxpiya:whatsapp-health --reconnect')
            ->everyMinute()
            ->withoutOverlapping(2)
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
