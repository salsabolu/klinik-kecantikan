<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function transactions()
    {
        // For patients: find transactions by patient_id through patient relationship
        if ($this->role === 'pasien' && $this->patient) {
            return $this->patient->transactions();
        }
        
        // For staff: find transactions created by this user
        return $this->hasMany(Transaction::class, 'created_by');
    }

    // Helper methods untuk role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isResepsionis()
    {
        return $this->role === 'resepsionis';
    }

    public function isDokter()
    {
        return $this->role === 'dokter';
    }

    public function isManajerStok()
    {
        return $this->role === 'manajer_stok';
    }

    public function isPasien()
    {
        return $this->role === 'pasien';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
