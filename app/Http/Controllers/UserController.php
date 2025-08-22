<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get regular users (not patients without accounts)
        $userQuery = User::query();

        // Search users
        if ($request->filled('search')) {
            $userQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role (exclude patient filter for now since we'll handle it separately)
        if ($request->filled('role') && $request->role !== 'pasien') {
            $userQuery->where('role', $request->role);
        }

        $users = collect();

        // Add regular users (excluding patients without accounts logic for now)
        if (!$request->filled('role') || $request->role !== 'pasien') {
            $regularUsers = $userQuery->orderBy('role')->orderBy('id')->get();
            $users = $users->merge($regularUsers);
        }

        // Add patients without accounts as "fake users" when showing all or when filtering by 'pasien'
        if (!$request->filled('role') || $request->role === 'pasien') {
            $patientsWithoutAccounts = Patient::whereNull('user_id')
                ->when($request->filled('search'), function ($q) use ($request) {
                    $q->where(function ($subQuery) use ($request) {
                        $subQuery->where('nama_lengkap', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
                })
                ->orderBy('nama_lengkap')
                ->get()
                ->map(function ($patient) {
                    // Convert patient to user-like object
                    return (object) [
                        'id' => 'patient_' . $patient->id, // Prefix to distinguish from real users
                        'name' => $patient->nama_lengkap,
                        'email' => $patient->email ?: 'Tidak ada email',
                        'role' => 'pasien',
                        'created_at' => $patient->created_at,
                        'updated_at' => $patient->updated_at,
                        'patient' => $patient,
                        'is_patient_without_account' => true
                    ];
                });

            $users = $users->merge($patientsWithoutAccounts);
        }

        // Sort the combined collection
        $users = $users->sortBy([
            ['role', 'asc'],
            ['name', 'asc']
        ]);

        // Manual pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $total = $users->count();
        $items = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query()
            ]
        );

        // Calculate statistics
        $statistics = [
            'admin' => User::where('role', 'admin')->count(),
            'resepsionis' => User::where('role', 'resepsionis')->count(),
            'dokter' => User::where('role', 'dokter')->count(),
            'manajer_stok' => User::where('role', 'manajer_stok')->count(),
            'pasien' => User::where('role', 'pasien')->count() + Patient::whereNull('user_id')->count(),
        ];

        return Inertia::render('Users/Index', [
            'users' => $paginatedUsers,
            'filters' => $request->only(['search', 'role']),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,resepsionis,dokter,manajer_stok,pasien',
        ];

        // Add patient-specific validation rules
        if ($request->role === 'pasien') {
            $validationRules = array_merge($validationRules, [
                'tanggal_lahir' => 'required|date|before:today',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'nomor_telepon' => 'required|string|max:15',
                'alamat' => 'required|string|max:500',
            ]);
        }

        $request->validate($validationRules);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create patient record if role is pasien
        if ($request->role === 'pasien') {
            Patient::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->name,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'alamat' => $request->alamat,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('users.index')
                        ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Check if this is a patient without account (prefixed with 'patient_')
        if (str_starts_with($id, 'patient_')) {
            $patientId = str_replace('patient_', '', $id);
            $patient = Patient::findOrFail($patientId);
            
            // Create a user-like object for patients without accounts
            $fakeUser = (object) [
                'id' => $id,
                'name' => $patient->nama_lengkap,
                'email' => $patient->email ?: 'Tidak ada email',
                'role' => 'pasien',
                'created_at' => $patient->created_at,
                'updated_at' => $patient->updated_at,
                'patient' => $patient,
                'is_patient_without_account' => true
            ];

            return Inertia::render('Users/Show', [
                'user' => $fakeUser,
            ]);
        }

        // Regular user
        $user = User::findOrFail($id);
        return Inertia::render('Users/Show', [
            'user' => $user->load(['patient']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if this is a patient without account (prefixed with 'patient_')
        if (str_starts_with($id, 'patient_')) {
            return redirect()->route('users.index')
                ->withErrors(['error' => 'Pasien tanpa akun tidak dapat diedit melalui halaman Users. Silakan edit melalui halaman Patients.']);
        }

        $user = User::findOrFail($id);
        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,resepsionis,dokter,manajer_stok,pasien',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
                        ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        // Check if user has related data
        $hasRelatedData = false;
        $errorMessage = 'Tidak dapat menghapus pengguna yang memiliki data terkait: ';
        $relatedData = [];

        // Check reservations
        if ($user->reservations()->count() > 0) {
            $hasRelatedData = true;
            $relatedData[] = 'Reservasi';
        }

        // Check medical records
        if ($user->medicalRecords()->count() > 0) {
            $hasRelatedData = true;
            $relatedData[] = 'Rekam Medis';
        }

        // Check transactions differently based on role
        if ($user->role === 'pasien') {
            // For patients, check through patient relationship
            if ($user->patient && $user->patient->transactions()->count() > 0) {
                $hasRelatedData = true;
                $relatedData[] = 'Transaksi';
            }
        } else {
            // For staff, check transactions they created
            if (Transaction::where('created_by', $user->id)->count() > 0) {
                $hasRelatedData = true;
                $relatedData[] = 'Transaksi yang dibuat';
            }
        }

        if ($hasRelatedData) {
            return back()->withErrors(['error' => $errorMessage . implode(', ', $relatedData) . '.']);
        }

        // Delete related patient record if user is a patient
        if ($user->role === 'pasien' && $user->patient) {
            $user->patient->delete();
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Generate unique user ID based on role
     */
    public function generateUniqueId($role)
    {
        $prefixes = [
            'admin' => 'ADM',
            'resepsionis' => 'RSP',
            'dokter' => 'DOK', 
            'manajer_stok' => 'STK',
            'pasien' => 'PSN'
        ];

        $prefix = $prefixes[$role] ?? 'USR';
        $lastUser = User::where('role', $role)->orderBy('id', 'desc')->first();
        $number = $lastUser ? ($lastUser->id + 1) : 1;

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
