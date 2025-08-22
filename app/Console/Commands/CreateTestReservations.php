<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class CreateTestReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test reservations for notification testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test reservations for notification testing...');
        
        // Get existing data
        $patient = Patient::first();
        $service = Service::first();
        $doctor = User::where('role', 'dokter')->first();
        
        if (!$patient || !$service || !$doctor) {
            $this->error('Missing required data (patient, service, or doctor)');
            return;
        }
        
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        // Create reservation for today (should trigger H-0 reminder)
        $todayReservation = Reservation::create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'user_id' => $doctor->id,
            'tanggal_reservasi' => $today->format('Y-m-d'),
            'waktu_mulai' => '14:00:00',
            'waktu_selesai' => '15:30:00',
            'status' => 'confirmed',
            'catatan' => 'Test reservation for H-0 notification',
        ]);
        
        // Create reservation for tomorrow (should trigger H-1 reminder)
        $tomorrowReservation = Reservation::create([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'user_id' => $doctor->id,
            'tanggal_reservasi' => $tomorrow->format('Y-m-d'),
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '11:30:00',
            'status' => 'confirmed',
            'catatan' => 'Test reservation for H-1 notification',
        ]);
        
        $this->info("Created test reservation for today (ID: {$todayReservation->id})");
        $this->info("Created test reservation for tomorrow (ID: {$tomorrowReservation->id})");
        $this->info('Now run: php artisan test:notifications to generate reminders');
    }
}
