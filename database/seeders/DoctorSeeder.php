<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Sarah Wijaya',
                'email' => 'sarah.wijaya@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Maria Sari',
                'email' => 'maria.sari@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Andi Pratama',
                'email' => 'andi.pratama@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Lisa Permata',
                'email' => 'lisa.permata@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Sarah Melati',
                'email' => 'sarah.melati@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Andi Wijaya',
                'email' => 'andi.wijaya@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Therapis Maya Sari',
                'email' => 'maya.sari@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
        ];

        foreach ($doctors as $doctor) {
            User::updateOrCreate(
                ['email' => $doctor['email']], // Check by email
                $doctor // Create or update with this data
            );
        }

        $this->command->info('Doctor users created successfully!');
    }
}
