<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Mail\PatientAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::with('user');

        // For doctors, only show patients they have treated
        if (auth()->user()->role === 'dokter') {
            $query->whereHas('reservations', function ($q) {
                $q->where('user_id', auth()->id())
                  ->whereIn('status', ['confirmed', 'completed']);
            });
        }

        $patients = $query->when($request->search, function ($query, $search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nomor_telepon', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->jenis_kelamin, function ($query, $jenis_kelamin) {
                $query->where('jenis_kelamin', $jenis_kelamin);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search', 'jenis_kelamin']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Patients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:500',
            'nomor_telepon' => 'required|string|max:15|unique:patients,nomor_telepon',
            'email' => 'nullable|required_if:create_user_account,true|email|max:100|unique:patients,email',
            'create_user_account' => 'boolean',
            'user_password' => 'required_if:create_user_account,true|min:8|max:20|regex:/^[a-zA-Z0-9@$!%*?&_-]+$/',
        ], [
            'user_password.regex' => 'Password hanya boleh mengandung huruf, angka, dan karakter khusus (@$!%*?&_-)',
        ]);

        $patient = Patient::create($validated);
        $tempPassword = null;

        // Create user account if requested
        if ($request->create_user_account && $request->user_password && $validated['email']) {
            $tempPassword = $request->user_password;
            
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => bcrypt($tempPassword),
                'role' => 'pasien',
            ]);

            $patient->update(['user_id' => $user->id]);

            // Send email notification
            try {
                Mail::to($validated['email'])->send(new PatientAccountCreated($patient, $user, $tempPassword));
            } catch (\Exception $e) {
                // Log error but don't fail the patient creation
                Log::error('Failed to send patient account email: ' . $e->getMessage());
            }
        }

        return redirect()->route('patients.index')
            ->with('message', 'Data pasien berhasil ditambahkan.' . 
                ($tempPassword ? ' Email notifikasi akun telah dikirim.' : ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient, Request $request)
    {
        // For doctors, verify they can access this patient
        if (auth()->user()->role === 'dokter') {
            $hasAccess = $patient->reservations()
                ->where('user_id', auth()->id())
                ->whereIn('status', ['confirmed', 'completed'])
                ->exists();
                
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data pasien ini.');
            }
        }

        $patient->load([
            'user'
        ]);

        // Get paginated reservations (latest first, limit 3 for preview)
        $reservations = $patient->reservations()
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'reservations_page');

        // Get paginated medical records (latest first, limit 3 for preview)
        $medicalRecords = $patient->medicalRecords()
            ->with(['user', 'reservation.service'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->paginate(3, ['*'], 'medical_records_page');

        // Get paginated transactions (latest first)
        $transactions = $patient->transactions()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'transactions_page');

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'reservations' => $reservations,
            'medicalRecords' => $medicalRecords,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return Inertia::render('Patients/Edit', [
            'patient' => [
                'id' => $patient->id,
                'nama_lengkap' => $patient->nama_lengkap,
                'tanggal_lahir' => $patient->tanggal_lahir ? $patient->tanggal_lahir->format('Y-m-d') : null,
                'jenis_kelamin' => $patient->jenis_kelamin,
                'alamat' => $patient->alamat,
                'nomor_telepon' => $patient->nomor_telepon,
                'email' => $patient->email,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:500',
            'nomor_telepon' => [
                'required',
                'string',
                'max:15',
                Rule::unique('patients')->ignore($patient->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('patients')->ignore($patient->id),
            ],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('message', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Check if patient has any reservations or medical records
        if ($patient->reservations()->count() > 0 || $patient->medicalRecords()->count() > 0) {
            return redirect()->route('patients.index')
                ->with('error', 'Tidak dapat menghapus pasien yang memiliki riwayat reservasi atau rekam medis.');
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('message', 'Data pasien berhasil dihapus.');
    }

    /**
     * Get medical records for a specific patient (API endpoint)
     */
    public function getMedicalRecords(Patient $patient)
    {
        // For doctors, only show medical records they created or patients they have treated
        if (auth()->user()->role === 'dokter') {
            // Check if doctor has access to this patient
            $hasAccess = $patient->reservations()
                ->where('user_id', auth()->id())
                ->whereIn('status', ['confirmed', 'completed'])
                ->exists();
                
            if (!$hasAccess) {
                return response()->json(['error' => 'Unauthorized access to patient records'], 403);
            }
        }

        $medicalRecords = $patient->medicalRecords()
            ->with(['user', 'reservation.service'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->limit(10) // Limit to last 10 records
            ->get();

        return response()->json([
            'medicalRecords' => $medicalRecords
        ]);
    }
}
