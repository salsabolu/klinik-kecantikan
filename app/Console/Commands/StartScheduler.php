<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Laravel scheduler for development (Windows)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Laravel Scheduler for Reservation Notifications...');
        $this->info('⏰ Notifications will be sent every hour between 8 AM - 8 PM');
        $this->info('📧 H-1 Reminders: 24 hours before appointment');
        $this->info('📧 H-0 Reminders: Same day as appointment');
        $this->info('');
        $this->info('Press Ctrl+C to stop the scheduler');
        $this->info('');
        
        // Run the schedule:work command
        $this->call('schedule:work');
    }
}
