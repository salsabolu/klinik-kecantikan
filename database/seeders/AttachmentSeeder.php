<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\MedicalRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Ambil medical record yang ada
        $medicalRecords = MedicalRecord::take(3)->get();

        if ($medicalRecords->count() > 0) {
            foreach ($medicalRecords as $index => $medicalRecord) {
                // Sample attachment untuk setiap medical record
                Attachment::create([
                    'medical_record_id' => $medicalRecord->id,
                    'type' => 'foto_sebelum',
                    'filename' => 'foto_sebelum_' . ($index + 1) . '.jpg',
                    'path' => 'attachments/sample_foto_sebelum_' . ($index + 1) . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(500000, 2000000) // 500KB - 2MB
                ]);

                Attachment::create([
                    'medical_record_id' => $medicalRecord->id,
                    'type' => 'foto_sesudah',
                    'filename' => 'foto_sesudah_' . ($index + 1) . '.jpg',
                    'path' => 'attachments/sample_foto_sesudah_' . ($index + 1) . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(500000, 2000000) // 500KB - 2MB
                ]);

                if ($index === 0) {
                    // Tambahan hasil lab untuk medical record pertama
                    Attachment::create([
                        'medical_record_id' => $medicalRecord->id,
                        'type' => 'hasil_lab',
                        'filename' => 'hasil_laboratorium.pdf',
                        'path' => 'attachments/sample_lab_result.pdf',
                        'mime_type' => 'application/pdf',
                        'size' => rand(100000, 500000) // 100KB - 500KB
                    ]);
                }
            }
        }
    }
}
