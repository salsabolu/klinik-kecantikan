<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $services = \App\Models\Service::orderBy('nama_layanan')->limit(3)->get();
    $products = \App\Models\Product::where('stok', '>', 0)->orderBy('nama_produk')->limit(3)->get();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'services' => $services,
        'products' => $products,
    ]);
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Forgot Password Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    // Reset Password Routes
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings - All authenticated users can access settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.change-password');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::put('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::put('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User Management
    Route::resource('users', UserController::class);

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Patient Management Routes - accessible by admin, resepsionis, and dokter
Route::middleware(['auth'])->group(function () {
    // All roles can view patients list and details
    Route::get('/patients', [PatientController::class, 'index'])->middleware('role:admin,resepsionis,dokter')->name('patients.index');

    // Only admin and resepsionis can create, edit, delete patients
    Route::get('/patients/create', [PatientController::class, 'create'])->middleware('role:admin,resepsionis')->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->middleware('role:admin,resepsionis')->name('patients.store');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->middleware('role:admin,resepsionis')->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->middleware('role:admin,resepsionis')->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->middleware('role:admin,resepsionis')->name('patients.destroy');

    // Show route should come after create route to avoid conflicts
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->middleware('role:admin,resepsionis,dokter')->name('patients.show');
});

// Resepsionis & Admin Routes
Route::middleware(['auth', 'role:resepsionis,admin'])->group(function () {
    Route::resource('reservations', ReservationController::class);
    Route::get('/api/available-slots', [ReservationController::class, 'getAvailableSlots'])->name('reservations.available-slots');

    // Transaction Management
    Route::resource('transactions', TransactionController::class);
    Route::patch('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');

    // All Doctors Schedule for Resepsionis
    Route::get('/all-schedule', [ReservationController::class, 'allDoctorsSchedule'])->name('resepsionis.schedule');
    Route::get('/all-schedule/upcoming', [ReservationController::class, 'allDoctorsUpcomingSchedule'])->name('all-doctors.upcoming');
});

// Dokter & Admin Routes
Route::middleware(['auth', 'role:dokter,admin'])->group(function () {
    // Medical Records Management
    Route::resource('medical-records', MedicalRecordController::class);
    Route::get('/api/patients/{patient}/records', [MedicalRecordController::class, 'getPatientRecords'])->name('api.patients.records');
    
    // Attachment routes for medical records
    Route::get('/attachments/{attachment}/view', function($id) {
        $attachment = \App\Models\Attachment::findOrFail($id);
        
        // Check authorization - only admin or the doctor who created the medical record can view
        if (auth()->user()->role === 'dokter' && $attachment->medicalRecord->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to view this attachment.');
        }
        
        $path = storage_path('app/public/' . $attachment->path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        return response()->file($path, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="' . $attachment->filename . '"'
        ]);
    })->name('attachments.view');

    Route::get('/attachments/{attachment}/download', function($id) {
        $attachment = \App\Models\Attachment::findOrFail($id);
        
        // Check authorization - only admin or the doctor who created the medical record can download
        if (auth()->user()->role === 'dokter' && $attachment->medicalRecord->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to download this attachment.');
        }
        
        $path = storage_path('app/public/' . $attachment->path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        return response()->download($path, $attachment->filename, [
            'Content-Type' => $attachment->mime_type,
        ]);
    })->name('attachments.download');

    // Read-only access to reservations for doctors
    Route::get('/reservations-view/{reservation}', [ReservationController::class, 'show'])->name('reservations.show.doctor');
    
    // Debug route to test auth
    Route::get('/debug-auth', function() {
        return response()->json([
            'user' => auth()->user(),
            'role' => auth()->user()->role ?? 'no role',
            'is_authenticated' => auth()->check(),
            'can_access_medical_records' => auth()->check() && in_array(auth()->user()->role, ['dokter', 'admin']),
        ]);
    })->name('debug.auth');
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->group(function () {
    // Doctor's schedule and history
    Route::get('/reservation-schedule', [ReservationController::class, 'todaySchedule'])->name('doctor.schedule');
    Route::get('/reservation-upcoming', [ReservationController::class, 'upcomingSchedule'])->name('doctor.upcoming');
    Route::get('/reservation-history', [ReservationController::class, 'reservationHistory'])->name('doctor.history');
});

// Manajer Stok Routes (MUST come before Pasien routes to avoid conflicts)
Route::middleware(['auth', 'role:manajer_stok,admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('services', ServiceController::class);
    
    Route::put('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');

    // Stock Reports (limited access)
    Route::get('/reports/stock', [ReportController::class, 'stockReports'])->name('reports.stock');
});

// Pasien Routes - View Only Access
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('my-reservations.index');
    Route::get('/my-reservations/{reservation}', [ReservationController::class, 'showPatientReservation'])->name('my-reservations.show');
    Route::get('/services-catalog', [ServiceController::class, 'catalog'])->name('services.catalog');
    Route::get('/products-catalog', [ProductController::class, 'catalog'])->name('products.catalog');

    // View specific products and services (read-only access for patients)
    Route::get('/view-product/{product}', [ProductController::class, 'show'])->name('products.show.pasien');
    Route::get('/view-service/{service}', [ServiceController::class, 'show'])->name('services.show.pasien');
});
