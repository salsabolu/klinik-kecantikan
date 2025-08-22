<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Api\AttachmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Reservation API routes
Route::middleware(['auth', 'role:dokter,admin'])->group(function () {
    Route::get('/available-slots', [ReservationController::class, 'getAvailableSlots']);
    Route::get('/patients/{patient}/medical-records', [PatientController::class, 'getMedicalRecords']);
    
    // Attachment routes for medical records
    Route::post('/medical-records/{medicalRecordId}/attachments', [AttachmentController::class, 'store']);
    Route::get('/medical-records/{medicalRecordId}/attachments', [AttachmentController::class, 'getByMedicalRecord']);
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
    Route::put('/attachments/{attachment}', [AttachmentController::class, 'update']);
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show']);
});
