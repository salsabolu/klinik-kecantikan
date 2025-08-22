<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $validation_rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,resepsionis,dokter,manajer_stok,pasien',
        ];

        // Add patient-specific validation if role is pasien
        if ($request->role === 'pasien') {
            $validation_rules = array_merge($validation_rules, [
                'tanggal_lahir' => 'required|date|before:today',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'alamat' => 'required|string|max:500',
                'nomor_telepon' => 'required|string|max:15|unique:patients,nomor_telepon',
            ]);
        }

        $validated = $request->validate($validation_rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Create patient record if role is pasien
        if ($validated['role'] === 'pasien') {
            Patient::create([
                'nama_lengkap' => $validated['name'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'alamat' => $validated['alamat'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'email' => $validated['email'],
                'user_id' => $user->id,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
