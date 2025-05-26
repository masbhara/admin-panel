<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AddDocumentPermissions::class,
        Commands\SetupWhatsappNotification::class,
        Commands\AddWhatsappNotificationPermissions::class,
        Commands\EnsureStorageDirectoriesExist::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * These schedules are run in a single process, immediately after the
     * Artisan command is executed.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Auto-update check setiap 30 menit
        $schedule->command('bash ' . base_path('deploy.sh') . ' auto-update')
                ->everyThirtyMinutes()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/auto-update.log'));

        // Backup database setiap hari
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('01:30');

        // Clear cache yang expired setiap hari
        $schedule->command('cache:prune-stale-tags')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 