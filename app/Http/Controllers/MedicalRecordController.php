<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'user', 'reservation.service']);

        // Filter berdasarkan dokter untuk role dokter
        if (Auth::user()->role === 'dokter') {
            $query->where('user_id', Auth::id());
        }

        // Search by patient name
        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('tanggal_pemeriksaan', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('tanggal_pemeriksaan', '<=', $request->date_to);
        }

        // Filter by patient
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $medicalRecords = $query->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get patients - for doctors, only show patients they have treated
        $patientsQuery = Patient::orderBy('nama_lengkap');
        
        if (Auth::user()->role === 'dokter') {
            // Only show patients who have reservations with this doctor
            $patientsQuery->whereHas('reservations', function ($q) {
                $q->where('user_id', Auth::id())
                  ->whereIn('status', ['confirmed', 'completed']);
            });
        }
        
        $patients = $patientsQuery->get();

        return Inertia::render('MedicalRecords/Index', [
            'medicalRecords' => $medicalRecords,
            'patients' => $patients,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'patient_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Medical Record HARUS dibuat dari reservasi yang sudah confirmed/completed
        if (!$request->filled('reservation_id')) {
            return redirect()->route('doctor.schedule')
                ->with('error', 'Rekam medis harus dibuat dari reservasi yang sudah dikonfirmasi.');
        }

        $selectedReservation = \App\Models\Reservation::with(['patient', 'service', 'user'])
            ->where('id', $request->reservation_id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->first();

        if (!$selectedReservation) {
            return redirect()->route('doctor.schedule')
                ->with('error', 'Reservasi tidak ditemukan atau belum dikonfirmasi.');
        }

        // For doctors/therapists, only allow their own reservations
        if (auth()->user()->role === 'dokter' && $selectedReservation->user_id !== auth()->id()) {
            return redirect()->route('doctor.schedule')
                ->with('error', 'Anda hanya dapat membuat rekam medis untuk reservasi Anda sendiri.');
        }

        // Check if medical record already exists for this reservation
        $existingRecord = MedicalRecord::where('reservation_id', $selectedReservation->id)->first();
        if ($existingRecord) {
            // Allow creating multiple medical records per reservation
            // Show info message but continue to allow creation
            session()->flash('info', 'Sudah ada rekam medis untuk reservasi ini. Anda dapat membuat rekam medis tambahan.');
        }

        $selectedPatient = $selectedReservation->patient;
        
        // Get all patients for dropdown (but reservation is required)
        if (auth()->user()->role === 'dokter') {
            $patients = Patient::whereHas('reservations', function ($query) {
                $query->where('user_id', auth()->id())
                      ->whereIn('status', ['confirmed', 'completed']);
            })->orderBy('nama_lengkap')->get();
        } else {
            $patients = Patient::orderBy('nama_lengkap')->get();
        }

        return Inertia::render('MedicalRecords/Create', [
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'selectedReservation' => $selectedReservation,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tanggal_pemeriksaan' => 'required|date|before_or_equal:today',
            'diagnosa' => 'required|string|max:1000',
            'tindakan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:2000',
            'reservation_id' => 'nullable|exists:reservations,id',
            'allergies' => 'nullable|string', // Will be JSON decoded
            'medications' => 'nullable|string', // Will be JSON decoded
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max per file
            'attachment_types' => 'nullable|array',
            'attachment_types.*' => 'nullable|string|in:foto_sebelum,foto_sesudah,hasil_lab,lainnya'
        ]);

        // Decode JSON strings for allergies and medications
        if (isset($validated['allergies'])) {
            $validated['allergies'] = json_decode($validated['allergies'], true);
        }
        if (isset($validated['medications'])) {
            $validated['medications'] = json_decode($validated['medications'], true);
        }

        // Clean up empty allergies and medications
        if (isset($validated['allergies'])) {
            $validated['allergies'] = array_filter($validated['allergies'], function($allergy) {
                return !empty($allergy['name']);
            });
        }

        if (isset($validated['medications'])) {
            $validated['medications'] = array_filter($validated['medications'], function($medication) {
                return !empty($medication['name']);
            });
        }

        // For doctors/therapists, validate they can only create medical records for confirmed/completed reservations
        if (auth()->user()->role === 'dokter') {
            if (isset($validated['reservation_id'])) {
                $reservation = \App\Models\Reservation::where('id', $validated['reservation_id'])
                    ->where('user_id', auth()->id())
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->first();
                
                if (!$reservation) {
                    return back()->withErrors(['reservation_id' => 'Reservasi tidak valid atau bukan milik Anda.']);
                }
            }
        }

        $validated['user_id'] = Auth::id();

        $medicalRecord = MedicalRecord::create($validated);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachmentTypes = $request->input('attachment_types', []);
            
            foreach ($request->file('attachments') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Generate unique filename
                $filename = time() . '_' . str_replace(' ', '_', $originalName);
                
                // Store file
                $filePath = $file->storeAs('attachments', $filename, 'public');
                
                // Create attachment record
                $medicalRecord->attachments()->create([
                    'type' => $attachmentTypes[$index] ?? 'lainnya',
                    'filename' => $originalName,
                    'path' => $filePath,
                    'mime_type' => $mimeType,
                    'size' => $fileSize
                ]);
            }
        }

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Rekam medis berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        // Check authorization
        if (Auth::user()->role === 'dokter' && $medicalRecord->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to view this medical record.');
        }

        $medicalRecord->load(['patient', 'user', 'reservation.service', 'attachments']);

        // Get patient's medical history
        $patientHistory = MedicalRecord::where('patient_id', $medicalRecord->patient_id)
            ->where('id', '!=', $medicalRecord->id)
            ->with(['user', 'reservation.service'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('MedicalRecords/Show', [
            'medicalRecord' => $medicalRecord,
            'patientHistory' => $patientHistory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        // Check authorization - only the creating doctor can edit
        if (Auth::user()->role === 'dokter' && $medicalRecord->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this medical record.');
        }

        // Load relationships
        $medicalRecord->load(['patient', 'user', 'attachments']);
        
        // For doctors/therapists, only show patients they have treated
        if (auth()->user()->role === 'dokter') {
            $patients = Patient::whereHas('reservations', function ($query) {
                $query->where('user_id', auth()->id())
                      ->whereIn('status', ['confirmed', 'completed']);
            })->orderBy('nama_lengkap')->get();
        } else {
            $patients = Patient::orderBy('nama_lengkap')->get();
        }

        return Inertia::render('MedicalRecords/Edit', [
            'medicalRecord' => $medicalRecord,
            'patients' => $patients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // Check authorization - only the creating doctor can edit
        if (Auth::user()->role === 'dokter' && $medicalRecord->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this medical record.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tanggal_pemeriksaan' => 'required|date|before_or_equal:today',
            'diagnosa' => 'required|string|max:1000',
            'tindakan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:2000',
            'allergies' => 'nullable|string', // Will be JSON decoded
            'medications' => 'nullable|string', // Will be JSON decoded
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max per file
            'attachment_types' => 'nullable|array',
            'attachment_types.*' => 'nullable|string|in:foto_sebelum,foto_sesudah,hasil_lab,lainnya'
        ]);

        // Decode JSON strings for allergies and medications
        if (isset($validated['allergies'])) {
            $validated['allergies'] = json_decode($validated['allergies'], true);
        }
        if (isset($validated['medications'])) {
            $validated['medications'] = json_decode($validated['medications'], true);
        }

        // Clean up empty allergies and medications
        if (isset($validated['allergies'])) {
            $validated['allergies'] = array_filter($validated['allergies'], function($allergy) {
                return !empty($allergy['name']);
            });
        }

        if (isset($validated['medications'])) {
            $validated['medications'] = array_filter($validated['medications'], function($medication) {
                return !empty($medication['name']);
            });
        }

        $medicalRecord->update($validated);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachmentTypes = $request->input('attachment_types', []);
            
            foreach ($request->file('attachments') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Generate unique filename
                $filename = time() . '_' . str_replace(' ', '_', $originalName);
                
                // Store file
                $filePath = $file->storeAs('attachments', $filename, 'public');
                
                // Create attachment record
                $medicalRecord->attachments()->create([
                    'type' => $attachmentTypes[$index] ?? 'lainnya',
                    'filename' => $originalName,
                    'path' => $filePath,
                    'mime_type' => $mimeType,
                    'size' => $fileSize
                ]);
            }
        }

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        // Check authorization - only admin or the creating doctor can delete
        if (Auth::user()->role === 'dokter' && $medicalRecord->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this medical record.');
        }

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }

    /**
     * Get medical records for a specific patient
     */
    public function getPatientRecords(Patient $patient)
    {
        $medicalRecords = MedicalRecord::where('patient_id', $patient->id)
            ->with(['user', 'reservation.service'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'medicalRecords' => $medicalRecords,
        ]);
    }
}
