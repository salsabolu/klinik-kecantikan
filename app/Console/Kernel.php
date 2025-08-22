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
        // Send reservation reminders every hour during business hours (8 AM - 8 PM)
        $schedule->job(new \App\Jobs\SendReservationReminders())
                 ->hourlyAt(0) // Run at the top of every hour
                 ->between('08:00', '20:00'); // Only during business hours
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
