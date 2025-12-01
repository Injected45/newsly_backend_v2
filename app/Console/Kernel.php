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
        // Dispatch RSS fetch jobs every minute
        $schedule->command('rss:dispatch')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Cleanup old articles (run daily at 3 AM)
        $schedule->command('news:cleanup --days=30 --logs-days=7')
            ->dailyAt('03:00')
            ->runInBackground();

        // Clear expired tokens (run daily)
        $schedule->command('sanctum:prune-expired --hours=24')
            ->daily()
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
