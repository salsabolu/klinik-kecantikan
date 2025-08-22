<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Resepsionis
        User::create([
            'name' => 'Sari Resepsionis',
            'email' => 'resepsionis@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'resepsionis',
        ]);

        // Manajer Stok
        User::create([
            'name' => 'Budi Manajer Stok',
            'email' => 'stok@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'manajer_stok',
        ]);
        $this->command->info('Basic users created successfully!');
    }
}
