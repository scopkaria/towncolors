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
        // AI: alert admins about silenced freelancers on active projects
        $schedule->command('ai:alert-inactive-freelancers')->daily();

        // Automation: remind clients about invoices unpaid for 2+ days (runs at 09:00)
        $schedule->command('invoices:send-reminders')->dailyAt('09:00');

        // Subscriptions: notify clients expiring within 5 days, auto-expire overdue (runs at 08:00)
        $schedule->command('subscriptions:check-expiring --days=5')->dailyAt('08:00');

        // Live Chat: auto-close sessions inactive for 30 minutes
        $schedule->command('livechat:close-inactive --minutes=30')->everyFiveMinutes();
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
