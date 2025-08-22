<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;

class MedicalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil dokter dan pasien yang ada
        $doctors = User::where('role', 'dokter')->get();
        $patients = Patient::all();

        // Get specific doctor and patient for testing
        $drSarahWijaya = User::where('email', 'sarah.wijaya@klinik.com')->first();
        $antonLee = Patient::where('email', 'anton.lee@gmail.com')->first();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->command->info('No doctors or patients found. Please seed users and patients first.');
            return;
        }

        // Create specific medical records for Anton Lee with Dr. Sarah Wijaya
        if ($antonLee && $drSarahWijaya) {
            MedicalRecord::create([
                'patient_id' => $antonLee->id,
                'user_id' => $drSarahWijaya->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'diagnosa' => 'Kulit berminyak dengan jerawat ringan di area T-zone. Terdapat beberapa komedo hitam dan pori-pori yang membesar.',
                'tindakan' => 'Deep cleansing facial, ekstraksi komedo, aplikasi masker clay untuk mengontrol sebum. Perawatan anti-acne dengan salicylic acid.',
                'catatan' => 'Pasien Anton Lee menunjukkan respon baik terhadap treatment. Disarankan rutinitas perawatan harian dengan cleanser gentle dan moisturizer oil-free. Kontrol 2 minggu.',
            ]);

            MedicalRecord::create([
                'patient_id' => $antonLee->id,
                'user_id' => $drSarahWijaya->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(14)->format('Y-m-d'),
                'diagnosa' => 'Konsultasi awal - Evaluasi kondisi kulit untuk treatment anti-aging. Kulit terlihat sehat namun memerlukan pencegahan aging.',
                'tindakan' => 'Konsultasi dan analisis kulit menggunakan skin analyzer. Rekomendasi treatment dan produk home care yang sesuai.',
                'catatan' => 'Pasien pertama kali datang untuk konsultasi. Kondisi kulit overall baik. Direkomendasikan untuk memulai program perawatan preventif anti-aging.',
            ]);
        }

        $medicalRecords = [
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'diagnosa' => 'Jerawat ringan pada area wajah. Terlihat beberapa komedo hitam dan putih di area T-zone. Kondisi kulit berminyak dengan pori-pori yang agak besar.',
                'tindakan' => 'Pembersihan wajah mendalam (deep cleansing), ekstraksi komedo, aplikasi masker clay untuk mengontrol minyak berlebih.',
                'catatan' => 'Disarankan untuk menggunakan produk pembersih yang gentle dan tidak mengandung alkohol berlebih. Hindari memencet jerawat sendiri. Kontrol kembali dalam 2 minggu.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'diagnosa' => 'Kulit kering dan kusam. Terdapat garis-garis halus di area mata. Kulit terlihat dehidrasi dan kurang elastisitas.',
                'tindakan' => 'Hydrafacial treatment, aplikasi serum hyaluronic acid, moisturizing mask intensive. Perawatan anti-aging untuk area mata.',
                'catatan' => 'Tingkatkan konsumsi air putih minimal 2 liter per hari. Gunakan sunscreen SPF 30+ setiap hari. Aplikasi moisturizer pagi dan malam.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'diagnosa' => 'Hiperpigmentasi post-acne di area pipi kanan dan kiri. Bekas jerawat berwarna kecoklatan yang cukup mengganggu penampilan.',
                'tindakan' => 'Chemical peeling menggunakan glycolic acid 30%, aplikasi whitening serum dengan vitamin C dan niacinamide.',
                'catatan' => 'Wajib menggunakan sunscreen SPF 50+ selama proses perawatan. Hindari paparan sinar matahari langsung. Treatment dilakukan bertahap setiap 2 minggu.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'diagnosa' => 'Melasma grade 2 di area pipi dan dahi. Bercak hiperpigmentasi bilateral yang cukup luas dan gelap.',
                'tindakan' => 'Laser Q-Switch untuk melasma, aplikasi hydroquinone 4%, tretinoin 0.025% untuk malam hari.',
                'catatan' => 'Perawatan memerlukan waktu 3-6 bulan. Sangat penting menggunakan sunscreen broad spectrum. Kontrol setiap 2 minggu untuk monitoring progress.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'diagnosa' => 'Rosacea ringan dengan kemerahan di area hidung dan pipi. Kulit sensitif dengan mudah iritasi.',
                'tindakan' => 'Gentle cleansing, aplikasi gel anti-inflammatory, red light therapy untuk mengurangi inflamasi.',
                'catatan' => 'Hindari produk yang mengandung alkohol, parfum, dan bahan keras lainnya. Gunakan produk khusus kulit sensitif. Konsultasi kembali jika ada perburukan.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'diagnosa' => 'Penuaan dini dengan kerutan halus di area mata dan mulut. Kulit kendur dan elastisitas berkurang.',
                'tindakan' => 'Microneedling dengan radiofrequency, aplikasi serum anti-aging dengan retinol dan peptide.',
                'catatan' => 'Treatment dilakukan dalam 6 sesi dengan jarak 2 minggu. Gunakan produk anti-aging secara konsisten. Pola hidup sehat dengan tidur cukup.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'diagnosa' => 'Kulit berminyak berlebihan dengan pori-pori besar dan blackhead. Tekstur kulit kasar dan tidak rata.',
                'tindakan' => 'Ekstraksi komedo, carbon laser peel untuk mengecilkan pori, aplikasi toner dengan BHA.',
                'catatan' => 'Gunakan produk oil-free dan non-comedogenic. Lakukan eksfoliasi 2x seminggu. Hindari over-cleansing yang dapat memicu produksi minyak berlebih.',
            ],
            [
                'patient_id' => $patients->random()->id,
                'user_id' => $doctors->random()->id,
                'tanggal_pemeriksaan' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'diagnosa' => 'Acne scarring berupa ice pick scars dan rolling scars di area pipi. Tekstur kulit tidak rata dengan crater yang cukup dalam.',
                'tindakan' => 'Fractional CO2 laser resurfacing, microneedling dengan PRP (Platelet Rich Plasma).',
                'catatan' => 'Recovery time 7-10 hari dengan kemungkinan peeling dan kemerahan. Aplikasi wound healing cream dan sunscreen wajib. Follow up 1 minggu.',
            ]
        ];

        foreach ($medicalRecords as $record) {
            MedicalRecord::create($record);
        }

        $this->command->info('Medical records seeded successfully!');
    }
}
