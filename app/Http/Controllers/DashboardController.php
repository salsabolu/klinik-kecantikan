<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $data = [
            'user' => $user,
        ];

        // Stats berdasarkan role
        if ($user->isAdmin() || $user->isResepsionis()) {
            $data['stats'] = [
                'total_patients' => Patient::count(),
                'total_reservations_today' => Reservation::whereDate('tanggal_reservasi', today())->count(),
                'total_transactions_today' => Transaction::whereDate('tanggal_transaksi', today())->count(),
                'total_revenue_today' => Transaction::whereDate('tanggal_transaksi', today())
                    ->where('status_pembayaran', 'paid')
                    ->sum('total_harga'),
            ];
        }

        if ($user->isManajerStok()) {
            $data['stats'] = [
                'total_products' => Product::count(),
                'total_services' => Service::count(),
                'low_stock_products' => Product::where('stok', '<=', 10)->count(),
                'out_of_stock_products' => Product::where('stok', 0)->count(),
            ];
        }

        if ($user->isDokter()) {
            $data['stats'] = [
                'reservations_today' => Reservation::where('user_id', $user->id)
                    ->whereDate('tanggal_reservasi', today())
                    ->count(),
                'upcoming_reservations' => Reservation::where('user_id', $user->id)
                    ->whereDate('tanggal_reservasi', '>', today())
                    ->whereDate('tanggal_reservasi', '<=', today()->addDays(7))
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
                'completed_reservations' => Reservation::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
            ];
        }

        // Untuk pasien, tampilkan katalog produk dan layanan
        if ($user->isPasien()) {
            $data['products'] = Product::where('stok', '>', 0)->take(6)->get();
            $data['services'] = Service::take(6)->get();
        }

        return Inertia::render('Dashboard/Index', $data);
    }
}
