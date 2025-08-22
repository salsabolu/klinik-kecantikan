<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $attachments = Attachment::with('medicalRecord')->get();
        return response()->json($attachments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $medicalRecordId): JsonResponse
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240', // max 10MB
            'type' => 'required|in:foto_sebelum,foto_sesudah,hasil_lab,lainnya'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah medical record ada
        $medicalRecord = MedicalRecord::find($medicalRecordId);
        if (!$medicalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found'
            ], 404);
        }

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Buat nama file unik
            $filename = time() . '_' . str_replace(' ', '_', $originalName);
            
            // Simpan file ke storage/app/public/attachments
            $filePath = $file->storeAs('attachments', $filename, 'public');

            // Simpan data ke database
            $attachment = Attachment::create([
                'medical_record_id' => $medicalRecordId,
                'type' => $request->type,
                'filename' => $originalName,
                'path' => $filePath,
                'mime_type' => $mimeType,
                'size' => $fileSize
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $attachment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attachment $attachment): JsonResponse
    {
        return response()->json($attachment->load('medicalRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attachment $attachment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:foto_sebelum,foto_sesudah,hasil_lab,lainnya'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $attachment->update($request->only(['type']));

        return response()->json([
            'success' => true,
            'message' => 'Attachment updated successfully',
            'data' => $attachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        try {
            // Check authorization - only admin or the doctor who created the medical record can delete
            if (auth()->user()->role === 'dokter' && $attachment->medicalRecord->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this attachment.'
                ], 403);
            }

            // Hapus file dari storage
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }

            // Hapus record dari database
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attachments by medical record ID
     */
    public function getByMedicalRecord($medicalRecordId): JsonResponse
    {
        $medicalRecord = MedicalRecord::find($medicalRecordId);
        if (!$medicalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found'
            ], 404);
        }

        $attachments = $medicalRecord->attachments()->get();

        return response()->json([
            'success' => true,
            'data' => $attachments
        ]);
    }
}
