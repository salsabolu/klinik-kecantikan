<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'type',
        'message',
        'sent',
        'scheduled_at',
        'sent_at',
        'is_read',
    ];

    protected $casts = [
        'sent' => 'boolean',
        'is_read' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the reservation that owns the reminder.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
