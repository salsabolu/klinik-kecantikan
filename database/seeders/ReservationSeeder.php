<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $services = Service::all();
        $doctors = User::where('role', 'dokter')->get();

        // Get Anton Lee patient
        $antonLee = Patient::where('email', 'anton.lee@gmail.com')->first();
        $drSarahWijaya = User::where('email', 'sarah.wijaya@klinik.com')->first();

        if ($patients->count() === 0 || $services->count() === 0 || $doctors->count() === 0) {
            $this->command->info('Please run PatientSeeder, ServiceSeeder, and DoctorSeeder first');
            return;
        }

        // Create specific reservations for Anton Lee with Dr. Sarah Wijaya
        if ($antonLee && $drSarahWijaya && $services->count() > 0) {
            // Past appointment (completed)
            Reservation::create([
                'patient_id' => $antonLee->id,
                'service_id' => $services->random()->id,
                'user_id' => $drSarahWijaya->id,
                'tanggal_reservasi' => Carbon::today()->subDays(7)->format('Y-m-d'),
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '11:30:00',
                'status' => 'completed',
                'catatan' => 'Konsultasi dan treatment pertama dengan Dr. Sarah',
            ]);

            // Upcoming appointment (confirmed)
            Reservation::create([
                'patient_id' => $antonLee->id,
                'service_id' => $services->random()->id,
                'user_id' => $drSarahWijaya->id,
                'tanggal_reservasi' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'waktu_mulai' => '14:00:00',
                'waktu_selesai' => '15:30:00',
                'status' => 'confirmed',
                'catatan' => 'Follow up treatment dengan Dr. Sarah Wijaya',
            ]);

            // Pending appointment
            Reservation::create([
                'patient_id' => $antonLee->id,
                'service_id' => $services->random()->id,
                'user_id' => $drSarahWijaya->id,
                'tanggal_reservasi' => Carbon::today()->addDays(10)->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '10:00:00',
                'status' => 'pending',
                'catatan' => 'Treatment lanjutan, menunggu konfirmasi',
            ]);
        }

        $reservations = [
            [
                'patient_id' => $patients->random()->id,
                'service_id' => $services->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_reservasi' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '10:30:00',
                'status' => 'confirmed',
                'catatan' => 'Pasien baru, treatment pertama',
            ],
            [
                'patient_id' => $patients->random()->id,
                'service_id' => $services->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_reservasi' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'waktu_mulai' => '11:00:00',
                'waktu_selesai' => '12:00:00',
                'status' => 'pending',
                'catatan' => 'Follow up treatment',
            ],
            [
                'patient_id' => $patients->random()->id,
                'service_id' => $services->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_reservasi' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'waktu_mulai' => '14:00:00',
                'waktu_selesai' => '15:30:00',
                'status' => 'confirmed',
                'catatan' => null,
            ],
            [
                'patient_id' => $patients->random()->id,
                'service_id' => $services->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_reservasi' => Carbon::today()->subDays(1)->format('Y-m-d'),
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '11:30:00',
                'status' => 'completed',
                'catatan' => 'Treatment selesai dengan baik',
            ],
            [
                'patient_id' => $patients->random()->id,
                'service_id' => $services->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_reservasi' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'waktu_mulai' => '15:30:00',
                'waktu_selesai' => '17:00:00',
                'status' => 'pending',
                'catatan' => 'Pasien reguler, treatment bulanan',
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::create($reservation);
        }

        $this->command->info('Sample reservations created successfully');
    }
}
