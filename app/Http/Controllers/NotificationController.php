<?php

namespace App\Http\Controllers;

use App\Models\ReservationReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user based on their role
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = collect();

        if ($user->role === 'dokter') {
            // Doctors see notifications for their own reservations
            $notifications = ReservationReminder::with(['reservation.patient', 'reservation.user', 'reservation.service'])
                ->whereHas('reservation', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('sent', true)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
        } elseif ($user->role === 'pasien') {
            // Patients see notifications for their own reservations
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $notifications = ReservationReminder::with(['reservation.patient', 'reservation.user', 'reservation.service'])
                    ->whereHas('reservation', function ($query) use ($patient) {
                        $query->where('patient_id', $patient->id);
                    })
                    ->where('sent', true)
                    ->orderBy('created_at', 'desc')
                    ->take(20)
                    ->get();
            }
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count()
        ]);
    }

    /**
     * Get unread notification count for badge
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = 0;

        if ($user->role === 'dokter') {
            $count = ReservationReminder::whereHas('reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('sent', true)
            ->where('is_read', false)
            ->count();
        } elseif ($user->role === 'pasien') {
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $count = ReservationReminder::whereHas('reservation', function ($query) use ($patient) {
                    $query->where('patient_id', $patient->id);
                })
                ->where('sent', true)
                ->where('is_read', false)
                ->count();
            }
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = ReservationReminder::findOrFail($id);

        // Check if user has permission to mark this notification as read
        $canRead = false;
        
        if ($user->role === 'dokter') {
            $canRead = $notification->reservation->user_id === $user->id;
        } elseif ($user->role === 'pasien') {
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            $canRead = $patient && $notification->reservation->patient_id === $patient->id;
        }

        if (!$canRead) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        if ($user->role === 'dokter') {
            ReservationReminder::whereHas('reservation', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('sent', true)->update(['is_read' => true]);
        } elseif ($user->role === 'pasien') {
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            if ($patient) {
                ReservationReminder::whereHas('reservation', function ($query) use ($patient) {
                    $query->where('patient_id', $patient->id);
                })->where('sent', true)->update(['is_read' => true]);
            }
        }

        return response()->json(['success' => true]);
    }
}
