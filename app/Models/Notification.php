<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'type',
        'message',
        'sent',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'sent' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the reservation that owns the notification.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
