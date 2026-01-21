<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by the application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\NormalizeMediaAssets::class,
        \App\Console\Commands\ProcessRecurringCharges::class,
        \App\Console\Commands\ExpireSponsorshipProposals::class,
        \App\Console\Commands\GenerateAnnualReports::class,
        \App\Console\Commands\EncryptedBackupCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reminders:send')->daily();
        $schedule->command('reports:generate-daily-stats')->daily();
        $schedule->command('promises:check-monthly')->monthlyOn(1, '00:00'); // First day of each month
        $schedule->command('reports:generate-annual')->yearlyOn(12, 31, '23:59'); // End of each year
        $schedule->command('recurring:charge-due')->dailyAt('02:00');
        $schedule->command('sponsorship-proposals:expire')->hourly();
        $schedule->command('backups:encrypted')->dailyAt('03:30');
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
