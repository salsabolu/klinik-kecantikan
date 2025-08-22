<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nama_layanan' => 'Facial Deep Cleansing',
                'deskripsi' => 'Perawatan pembersihan wajah mendalam dengan ekstraksi komedo dan masker',
                'harga' => 150000,
                'durasi_menit' => 90,
            ],
            [
                'nama_layanan' => 'Chemical Peeling',
                'deskripsi' => 'Perawatan pengelupasan kulit dengan asam kimia untuk mencerahkan dan meremajakan kulit',
                'harga' => 300000,
                'durasi_menit' => 60,
            ],
            [
                'nama_layanan' => 'Microdermabrasion',
                'deskripsi' => 'Perawatan eksfoliasi untuk menghaluskan tekstur kulit dan mengurangi bekas jerawat',
                'harga' => 250000,
                'durasi_menit' => 45,
            ],
            [
                'nama_layanan' => 'Hydrafacial',
                'deskripsi' => 'Perawatan hidrasi intensif untuk kulit kering dan kusam',
                'harga' => 400000,
                'durasi_menit' => 75,
            ],
            [
                'nama_layanan' => 'Anti-Aging Treatment',
                'deskripsi' => 'Perawatan anti-penuaan dengan radio frequency dan serum khusus',
                'harga' => 500000,
                'durasi_menit' => 120,
            ],
            [
                'nama_layanan' => 'Acne Treatment',
                'deskripsi' => 'Perawatan khusus untuk kulit berjerawat dengan LED therapy',
                'harga' => 200000,
                'durasi_menit' => 60,
            ],
            [
                'nama_layanan' => 'Brightening Facial',
                'deskripsi' => 'Perawatan pencerah kulit dengan vitamin C dan ekstrak alami',
                'harga' => 180000,
                'durasi_menit' => 75,
            ],
            [
                'nama_layanan' => 'Eye Treatment',
                'deskripsi' => 'Perawatan khusus area mata untuk mengurangi kerutan dan kantung mata',
                'harga' => 150000,
                'durasi_menit' => 30,
            ],
            [
                'nama_layanan' => 'Oxygen Facial',
                'deskripsi' => 'Perawatan oksigen untuk kulit segar dan bercahaya',
                'harga' => 350000,
                'durasi_menit' => 90,
            ],
            [
                'nama_layanan' => 'Konsultasi Dokter',
                'deskripsi' => 'Konsultasi dengan dokter spesialis kulit dan kecantikan',
                'harga' => 100000,
                'durasi_menit' => 30,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['nama_layanan' => $service['nama_layanan']],
                $service
            );
        }
    }
}
