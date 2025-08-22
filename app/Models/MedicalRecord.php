<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'reservation_id',
        'tanggal_pemeriksaan',
        'diagnosa',
        'tindakan',
        'catatan',
        'allergies',
        'medications',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'allergies' => 'array',
        'medications' => 'array',
    ];

    // Relasi
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // dokter/terapis
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
