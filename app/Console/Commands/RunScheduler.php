<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendReservationReminders;

class RunScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually run the scheduler for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running reservation reminder job...');
        
        // Manually dispatch the job
        $job = new SendReservationReminders();
        $job->handle();
        
        $this->info('Job completed! Check reservation_reminders table for new notifications.');
        
        // Show recent reminders
        $this->info('Recent reminders:');
        $reminders = \App\Models\ReservationReminder::with('reservation')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($reminders as $reminder) {
            $this->line("- [{$reminder->type}] Reservation #{$reminder->reservation_id}: {$reminder->message}");
        }
    }
}
