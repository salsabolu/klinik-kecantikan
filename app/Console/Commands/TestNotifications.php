<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendReservationReminders;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the reservation notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing reservation notifications...');
        
        // Check today's date
        $today = \Carbon\Carbon::now();
        $tomorrow = $today->copy()->addDay();
        
        $this->info("Today: {$today->toDateString()}");
        $this->info("Tomorrow: {$tomorrow->toDateString()}");
        
        // Check reservations
        $allReservations = \App\Models\Reservation::all();
        $this->info("Total reservations: {$allReservations->count()}");
        
        $confirmedToday = \App\Models\Reservation::where('status', 'confirmed')
            ->whereDate('tanggal_reservasi', $today->toDateString())
            ->get();
        $this->info("Confirmed reservations today: {$confirmedToday->count()}");
        
        $confirmedTomorrow = \App\Models\Reservation::where('status', 'confirmed')
            ->whereDate('tanggal_reservasi', $tomorrow->toDateString())
            ->get();
        $this->info("Confirmed reservations tomorrow: {$confirmedTomorrow->count()}");
        
        foreach ($allReservations as $reservation) {
            $this->info("Reservation #{$reservation->id}: Status={$reservation->status}, Date={$reservation->tanggal_reservasi}");
        }
        
        $job = new SendReservationReminders();
        $job->handle();
        
        $reminderCount = \App\Models\ReservationReminder::count();
        $this->info("Reminders created: {$reminderCount}");
        
        $this->info('Notification test completed. Check the logs for details.');
    }
}
