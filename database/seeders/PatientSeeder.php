<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Semua patient dengan akun user
        $patientUsers = [
            [
                'user' => [
                    'name' => 'Lisa Dewi',
                    'email' => 'lisa.dewi@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'pasien',
                ],
                'patient' => [
                    'nama_lengkap' => 'Lisa Dewi',
                    'tanggal_lahir' => '1990-05-15',
                    'jenis_kelamin' => 'Perempuan',
                    'alamat' => 'Jl. Mawar No. 123, Jakarta Selatan',
                    'nomor_telepon' => '08123456789',
                    'email' => 'lisa.dewi@gmail .com',
                ]
            ],
            [
                'user' => [
                    'name' => 'Sari Dewi',
                    'email' => 'sari.dewi@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'pasien',
                ],
                'patient' => [
                    'nama_lengkap' => 'Sari Dewi',
                    'tanggal_lahir' => '1995-05-15',
                    'jenis_kelamin' => 'Perempuan',
                    'alamat' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                    'nomor_telepon' => '081234567890',
                    'email' => 'sari.dewi@gmail.com',
                ]
            ],
            [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.santoso@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'pasien',
                ],
                'patient' => [
                    'nama_lengkap' => 'Budi Santoso',
                    'tanggal_lahir' => '1988-12-08',
                    'jenis_kelamin' => 'Laki-laki',
                    'alamat' => 'Jl. Sudirman No. 456, Jakarta Selatan',
                    'nomor_telepon' => '081298765432',
                    'email' => 'budi.santoso@gmail.com',
                ]
            ],
            [
                'user' => [
                    'name' => 'Maya Putri',
                    'email' => 'maya.putri@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'pasien',
                ],
                'patient' => [
                    'nama_lengkap' => 'Maya Putri',
                    'tanggal_lahir' => '1992-03-22',
                    'jenis_kelamin' => 'Perempuan',
                    'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta Barat',
                    'nomor_telepon' => '081345678901',
                    'email' => 'maya.putri@gmail.com',
                ]
            ],
            [
                'user' => [
                    'name' => 'Anton Lee',
                    'email' => 'anton.lee@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'pasien',
                ],
                'patient' => [
                    'nama_lengkap' => 'Anton Lee',
                    'tanggal_lahir' => '1990-06-15',
                    'jenis_kelamin' => 'Laki-laki',
                    'alamat' => 'Jl. Kemang No. 123, Jakarta Selatan',
                    'nomor_telepon' => '081234567890',
                    'email' => 'anton.lee@gmail.com',
                ]
            ]
        ];

        foreach ($patientUsers as $data) {
            // Create atau update user
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            // Create atau update patient dengan user_id
            Patient::updateOrCreate(
                ['email' => $data['patient']['email']],
                array_merge($data['patient'], ['user_id' => $user->id])
            );
        }

        // Patient tanpa akun user
        Patient::create([
            'nama_lengkap' => 'Sari Cantik',
            'tanggal_lahir' => '1995-08-20',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Melati No. 456, Jakarta Pusat',
            'nomor_telepon' => '08987654321',
            'email' => 'sari.cantik@email.com',
        ]);

        Patient::create([
            'nama_lengkap' => 'El Putra',
            'tanggal_lahir' => '2003-12-31',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Anggrek No. 789, Jakarta Barat',
            'nomor_telepon' => '08555666777',
            'email' => null,
        ]);

        Patient::create([
            'nama_lengkap' => 'Mayang Sari',
            'tanggal_lahir' => '1992-03-25',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Dahlia No. 321, Jakarta Timur',
            'nomor_telepon' => '08444555666',
            'email' => 'mayang.sari@gmail.com',
        ]);

        Patient::create([
            'nama_lengkap' => 'Ade Pratama',
            'tanggal_lahir' => '1985-07-18',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Kenanga No. 654, Jakarta Utara',
            'nomor_telepon' => '08777888999',
            'email' => 'ade.pratama@gmail.com',
        ]);

        $this->command->info('Patients and patient users created successfully!');
    }
}
