<?php

namespace App\Jobs;

use App\Models\ReservationReminder;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendReservationReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        
        // Find reservations that need h-1 reminders (24 hours before)
        $this->sendH1Reminders($now);
        
        // Find reservations that need h-0 reminders (same day)
        $this->sendH0Reminders($now);
        
        Log::info('Reservation reminders job completed');
    }

    private function sendH1Reminders(Carbon $now)
    {
        $tomorrow = $now->copy()->addDay();
        
        $reservations = Reservation::with(['patient', 'user', 'service'])
            ->where('status', 'confirmed')
            ->whereDate('tanggal_reservasi', $tomorrow->toDateString())
            ->whereDoesntHave('reservationReminders', function ($query) {
                $query->where('type', 'h-1');
            })
            ->get();

        foreach ($reservations as $reservation) {
            $waktu = $reservation->waktu_mulai instanceof Carbon ? $reservation->waktu_mulai->format('H:i') : Carbon::createFromFormat('H:i:s', $reservation->waktu_mulai)->format('H:i');
            $tanggal = Carbon::parse($reservation->tanggal_reservasi)->format('d/m/Y');
            
            // Create reminder for patient
            $patientMessage = "Pengingat: Anda memiliki janji temu besok ({$tanggal}) pukul {$waktu} untuk layanan {$reservation->service->nama_layanan} dengan Dr. {$reservation->user->name}.";
            ReservationReminder::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->patient->user_id,
                'type' => 'h-1',
                'message' => $patientMessage,
                'scheduled_at' => $now,
                'sent' => true,
                'sent_at' => $now,
            ]);

            // Create reminder for doctor
            $doctorMessage = "Jadwal Praktek: Besok ({$tanggal}) pukul {$waktu}, Anda memiliki janji dengan pasien {$reservation->patient->nama_lengkap} untuk layanan {$reservation->service->nama_layanan}.";
            ReservationReminder::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'type' => 'h-1',
                'message' => $doctorMessage,
                'scheduled_at' => $now,
                'sent' => true,
                'sent_at' => $now,
            ]);

            Log::info("H-1 Reminders sent for reservation #{$reservation->id} to patient and doctor");
        }
    }

    private function sendH0Reminders(Carbon $now)
    {
        $today = $now->toDateString();
        
        $reservations = Reservation::with(['patient', 'user', 'service'])
            ->where('status', 'confirmed')
            ->whereDate('tanggal_reservasi', $today)
            ->whereDoesntHave('reservationReminders', function ($query) {
                $query->where('type', 'h-0');
            })
            ->get();

        foreach ($reservations as $reservation) {
            $waktu = $reservation->waktu_mulai instanceof Carbon ? $reservation->waktu_mulai->format('H:i') : Carbon::createFromFormat('H:i:s', $reservation->waktu_mulai)->format('H:i');
            $tanggal = Carbon::parse($reservation->tanggal_reservasi)->format('d/m/Y');
            
            // Create reminder for patient
            $patientMessage = "Pengingat: Anda memiliki janji temu hari ini ({$tanggal}) pukul {$waktu} untuk layanan {$reservation->service->nama_layanan} dengan Dr. {$reservation->user->name}.";
            ReservationReminder::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->patient->user_id,
                'type' => 'h-0',
                'message' => $patientMessage,
                'scheduled_at' => $now,
                'sent' => true,
                'sent_at' => $now,
            ]);

            // Create reminder for doctor
            $doctorMessage = "Jadwal Praktek: Hari ini ({$tanggal}) pukul {$waktu}, Anda memiliki janji dengan pasien {$reservation->patient->nama_lengkap} untuk layanan {$reservation->service->nama_layanan}.";
            ReservationReminder::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'type' => 'h-0',
                'message' => $doctorMessage,
                'scheduled_at' => $now,
                'sent' => true,
                'sent_at' => $now,
            ]);

            Log::info("H-0 Reminders sent for reservation #{$reservation->id} to patient and doctor");
        }
    }
}
