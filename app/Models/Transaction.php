<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'reservation_id',
        'nomor_transaksi',
        'tanggal_transaksi',
        'total_amount',
        'discount',
        'final_amount',
        'payment_method',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    // Relasi
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by'); // user yang membuat transaksi
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
