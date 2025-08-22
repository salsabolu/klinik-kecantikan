<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['patient', 'user', 'service']);

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', "%{$request->search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('user_id', $request->doctor_id);
        }

        // Single date filter (for compatibility with frontend)
        if ($request->filled('date')) {
            $query->whereDate('tanggal_reservasi', $request->date);
        }

        // Date range filters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_reservasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_reservasi', '<=', $request->end_date);
        }

        // Doctor filter (alternative parameter name from frontend)
        if ($request->filled('doctor')) {
            $query->where('user_id', $request->doctor);
        }

        $reservations = $query->orderBy('tanggal_reservasi', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10)
            ->withQueryString();

        $doctors = User::where('role', 'dokter')->get();

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
            'doctors' => $doctors,
            'filters' => $request->only(['search', 'status', 'doctor_id', 'doctor', 'date', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::orderBy('nama_lengkap')->get();
        $services = Service::orderBy('nama_layanan')->get();
        $doctors = User::where('role', 'dokter')->orderBy('name')->get();

        return Inertia::render('Reservations/Create', [
            'patients' => $patients,
            'services' => $services,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'user_id' => 'required|exists:users,id',
            'tanggal_reservasi' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'catatan' => 'nullable|string',
        ]);

        // Check for time conflicts
        $conflict = Reservation::where('user_id', $validated['user_id'])
            ->where('tanggal_reservasi', $validated['tanggal_reservasi'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('waktu_mulai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                    ->orWhereBetween('waktu_selesai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('waktu_mulai', '<=', $validated['waktu_mulai'])
                          ->where('waktu_selesai', '>=', $validated['waktu_selesai']);
                    });
            })
            ->first();

        if ($conflict) {
            return back()->withErrors([
                'waktu_mulai' => 'Waktu yang dipilih bertabrakan dengan reservasi yang sudah ada.'
            ]);
        }

        $validated['status'] = 'pending';

        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservasi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        // For doctors, verify they can access this reservation
        if (auth()->user()->role === 'dokter') {
            // Check if the doctor is assigned to this reservation
            if ($reservation->user_id !== auth()->id()) {
                abort(403, 'Anda tidak memiliki akses untuk melihat reservasi ini.');
            }
        }

        $reservation->load(['patient', 'user', 'service', 'medicalRecords.user', 'medicalRecords.attachments']);

        return Inertia::render('Reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $patients = Patient::orderBy('nama_lengkap')->get();
        $services = Service::orderBy('nama_layanan')->get();
        $doctors = User::where('role', 'dokter')->orderBy('name')->get();

        return Inertia::render('Reservations/Edit', [
            'reservation' => $reservation,
            'patients' => $patients,
            'services' => $services,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        // Handle simple status updates
        if ($request->only('status') && count($request->all()) === 1) {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled',
            ]);

            $reservation->update($validated);

            return redirect()->back()
                ->with('success', 'Status reservasi berhasil diperbarui.');
        }

        // Handle full reservation updates
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'user_id' => 'required|exists:users,id',
            'tanggal_reservasi' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        // Check for time conflicts (excluding current reservation)
        $conflict = Reservation::where('id', '!=', $reservation->id)
            ->where('user_id', $validated['user_id'])
            ->where('tanggal_reservasi', $validated['tanggal_reservasi'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('waktu_mulai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                    ->orWhereBetween('waktu_selesai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('waktu_mulai', '<=', $validated['waktu_mulai'])
                          ->where('waktu_selesai', '>=', $validated['waktu_selesai']);
                    });
            })
            ->first();

        if ($conflict) {
            return back()->withErrors([
                'waktu_mulai' => 'Waktu yang dipilih bertabrakan dengan reservasi yang sudah ada.'
            ]);
        }

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservasi berhasil dihapus.');
    }

    /**
     * Get available time slots for a specific doctor and date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'doctor_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'exclude_reservation_id' => 'sometimes|exists:reservations,id',
        ]);

        $date = $request->date;
        $doctorId = $request->doctor_id;
        $serviceId = $request->service_id;
        $excludeReservationId = $request->exclude_reservation_id;

        // Get service duration
        $service = Service::find($serviceId);
        $durationMinutes = $service->durasi_menit;

        // Get existing reservations for the doctor on the date
        $existingReservations = Reservation::where('user_id', $doctorId)
            ->where('tanggal_reservasi', $date)
            ->where('status', '!=', 'cancelled')
            ->when($excludeReservationId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->get(['waktu_mulai', 'waktu_selesai']);

        // Generate all possible time slots (8:00 - 17:00, 30-minute intervals)
        $availableSlots = [];
        $startTime = Carbon::createFromFormat('H:i', '08:00');
        $endTime = Carbon::createFromFormat('H:i', '17:00');
        $currentDateTime = Carbon::now();
        $isToday = Carbon::parse($date)->isToday();

        while ($startTime->format('H:i') < $endTime->format('H:i')) {
            $slotEnd = $startTime->copy()->addMinutes($durationMinutes);
            
            // Check if slot end time exceeds working hours
            if ($slotEnd->format('H:i') > $endTime->format('H:i')) {
                break;
            }

            // Skip lunch break (12:00 - 13:00)
            if ($startTime->format('H:i') >= '12:00' && $startTime->format('H:i') < '13:00') {
                $startTime->addMinutes(30);
                continue;
            }

            // Skip past time slots for today
            if ($isToday) {
                $slotDateTime = Carbon::parse($date . ' ' . $startTime->format('H:i:s'));
                if ($slotDateTime->isPast()) {
                    $startTime->addMinutes(30);
                    continue;
                }
            }

            $slotStart = $startTime->format('H:i:s');
            $slotEndFormatted = $slotEnd->format('H:i:s');

            // Check for conflicts with existing reservations
            $hasConflict = $existingReservations->some(function ($reservation) use ($slotStart, $slotEndFormatted) {
                return ($slotStart >= $reservation->waktu_mulai && $slotStart < $reservation->waktu_selesai) ||
                       ($slotEndFormatted > $reservation->waktu_mulai && $slotEndFormatted <= $reservation->waktu_selesai) ||
                       ($slotStart <= $reservation->waktu_mulai && $slotEndFormatted >= $reservation->waktu_selesai);
            });

            if (!$hasConflict) {
                $availableSlots[] = [
                    'waktu_mulai' => $slotStart,
                    'waktu_selesai' => $slotEndFormatted,
                ];
            }

            // Move to next 30-minute slot
            $startTime->addMinutes(30);
        }

        return response()->json([
            'slots' => $availableSlots,
            'existing_reservations' => $existingReservations->map(function($reservation) {
                return [
                    'waktu_mulai' => $reservation->waktu_mulai,
                    'waktu_selesai' => $reservation->waktu_selesai,
                ];
            }),
            'date' => $date,
            'doctor_id' => $doctorId,
            'service_duration' => $durationMinutes,
        ]);
    }

    /**
     * Reservasi untuk pasien (self-booking)
     */
    public function myReservations(Request $request)
    {
        // Cari patient berdasarkan user yang login
        $patient = Patient::where('user_id', auth()->id())->first();
        
        if (!$patient) {
            return Inertia::render('Patients/Register', [
                'message' => 'Silakan lengkapi data diri Anda terlebih dahulu.'
            ]);
        }

        $query = Reservation::with(['service', 'user'])
            ->where('patient_id', $patient->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_reservasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_reservasi', '<=', $request->end_date);
        }

        $reservations = $query->orderBy('tanggal_reservasi', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10);

        $services = Service::orderBy('nama_layanan')->get();
        $doctors = User::where('role', 'dokter')->orderBy('name')->get();

        return Inertia::render('Reservations/MyReservations', [
            'reservations' => $reservations,
            'services' => $services,
            'doctors' => $doctors,
            'patient' => $patient,
            'filters' => [
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ]);
    }

    public function showPatientReservation(Reservation $reservation)
    {
        // Ensure patient can only view their own reservations
        if ($reservation->patient_id !== auth()->user()->patient->id) {
            abort(403);
        }

        $reservation->load(['patient', 'user', 'service']);

        return Inertia::render('Reservations/ShowPatient', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Jadwal reservasi hari ini untuk dokter/terapis
     */
    public function todaySchedule()
    {
        $today = Carbon::today();
        $reservations = Reservation::with(['patient', 'service'])
            ->where('user_id', auth()->id())
            ->whereDate('tanggal_reservasi', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('waktu_mulai')
            ->get();

        return Inertia::render('Reservations/DoctorSchedule', [
            'reservations' => $reservations,
            'date' => $today->format('Y-m-d'),
        ]);
    }

    /**
     * Riwayat reservasi untuk dokter/terapis
     */
    public function reservationHistory(Request $request)
    {
        $query = Reservation::with(['patient', 'service'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled');

        // Apply filters
        if ($request->filled('patient_search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->patient_search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_reservasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_reservasi', '<=', $request->end_date);
        }

        // Only past reservations
        $query->where(function($q) {
            $q->whereDate('tanggal_reservasi', '<', Carbon::today())
              ->orWhere(function($subQ) {
                  $subQ->whereDate('tanggal_reservasi', '=', Carbon::today())
                       ->whereTime('waktu_selesai', '<', now()->format('H:i:s'));
              });
        });

        $reservations = $query->orderBy('tanggal_reservasi', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(15);

        return Inertia::render('Reservations/DoctorHistory', [
            'reservations' => $reservations,
            'filters' => [
                'patient_search' => $request->patient_search,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ]);
    }

    /**
     * Show all doctors' schedule for today (for resepsionis)
     */
    public function allDoctorsSchedule()
    {
        $date = Carbon::today();
        
        $reservations = Reservation::with(['patient', 'user', 'service'])
            ->whereDate('tanggal_reservasi', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereHas('user', function($query) {
                $query->where('role', 'dokter');
            })
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy('user.name'); // Group by doctor name

        return Inertia::render('Reservations/AllDoctorsSchedule', [
            'reservationsByDoctor' => $reservations,
            'date' => $date->format('Y-m-d'),
        ]);
    }

    /**
     * Show upcoming reservations for the authenticated doctor (next 7 days)
     */
    public function upcomingSchedule()
    {
        $doctor = auth()->user();
        $startDate = now()->addDay(); // Tomorrow
        $endDate = now()->addDays(7); // Next 7 days

        $reservations = Reservation::with(['patient', 'service'])
            ->where('user_id', $doctor->id)
            ->whereBetween('tanggal_reservasi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('tanggal_reservasi', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return Inertia::render('Reservations/DoctorUpcoming', [
            'reservations' => $reservations,
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
        ]);
    }

    /**
     * Show upcoming reservations for all doctors (for admin)
     */
    public function allDoctorsUpcomingSchedule()
    {
        $startDate = now()->addDay(); // Tomorrow
        $endDate = now()->addDays(7); // Next 7 days

        $reservations = Reservation::with(['patient', 'user', 'service'])
            ->whereBetween('tanggal_reservasi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('user', function($query) {
                $query->where('role', 'dokter');
            })
            ->orderBy('tanggal_reservasi', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get()
            ->groupBy('user.name'); // Group by doctor name

        return Inertia::render('Reservations/AllDoctorsUpcoming', [
            'reservationsByDoctor' => $reservations,
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
        ]);
    }
}
