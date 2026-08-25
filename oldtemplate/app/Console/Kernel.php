<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdatePermissions::class,
        Commands\CreateRolePermission::class,
        Commands\PushZodPanelVersion::class,
        Commands\PullLiveZodPanelCustom::class,
        Commands\RunAllAutomationsCron::class,
        Commands\SeedAppleEmailTemplates::class,
        Commands\SeedEducationSeoGuides::class,
        Commands\TestTelegramNotification::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run all ZodPanel / WHMLab billing, expiry reminders, auto-suspensions, and DNS sync every minute
        $schedule->command('zodpanel:cron-run --silent')->everyMinute()->withoutOverlapping();
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
