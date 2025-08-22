<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['stockReports']);
        $this->middleware(['auth', 'role:manajer_stok,admin'])->only(['stockReports']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set default date range based on period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'daily':
                    $startDate = Carbon::today()->format('Y-m-d');
                    $endDate = Carbon::today()->format('Y-m-d');
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'yearly':
                    $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                    break;
                default: // monthly
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                    break;
            }
        }

        // Revenue Report
        $revenueData = $this->getRevenueReport($startDate, $endDate);
        
        // Transaction Statistics
        $transactionStats = $this->getTransactionStats($startDate, $endDate);
        
        // Top Services and Products
        $topServices = $this->getTopServices($startDate, $endDate);
        $topProducts = $this->getTopProducts($startDate, $endDate);
        
        // Patient Statistics
        $patientStats = $this->getPatientStats($startDate, $endDate);

        // Patient Visits Report
        $patientVisits = $this->getPatientVisitsReport($startDate, $endDate);

        // Doctor Performance Report
        $doctorPerformance = $this->getDoctorPerformanceReport($startDate, $endDate);

        return Inertia::render('Reports/Index', [
            'filters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'revenue' => $revenueData,
            'transactions' => $transactionStats,
            'topServices' => $topServices,
            'topProducts' => $topProducts,
            'patients' => $patientStats,
            'patientVisits' => $patientVisits,
            'doctorPerformance' => $doctorPerformance,
        ]);
    }

    private function getRevenueReport($startDate, $endDate)
    {
        $totalRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('final_amount');

        $dailyRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $serviceRevenue = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('services as s', 's.id', '=', 'td.service_id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.payment_status', 'paid')
            ->whereNotNull('td.service_id')
            ->select('s.nama_layanan', DB::raw('SUM(td.total_price) as revenue'))
            ->groupBy('s.id', 's.nama_layanan')
            ->orderByDesc('revenue')
            ->get();

        $productRevenue = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('products as p', 'p.id', '=', 'td.product_id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.payment_status', 'paid')
            ->whereNotNull('td.product_id')
            ->select('p.nama_produk', DB::raw('SUM(td.total_price) as revenue'))
            ->groupBy('p.id', 'p.nama_produk')
            ->orderByDesc('revenue')
            ->get();

        return [
            'total' => $totalRevenue,
            'daily' => $dailyRevenue,
            'by_service' => $serviceRevenue,
            'by_product' => $productRevenue,
        ];
    }

    private function getTransactionStats($startDate, $endDate)
    {
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $paidTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')->count();
            
        $unpaidTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'pending')->count();
            
        $partialTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'partially_paid')->count();

        $paymentMethods = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->select('payment_method', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(final_amount) as total_amount'))
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        return [
            'total' => $totalTransactions,
            'paid' => $paidTransactions,
            'unpaid' => $unpaidTransactions,
            'partial' => $partialTransactions,
            'payment_methods' => $paymentMethods,
        ];
    }

    private function getTopServices($startDate, $endDate)
    {
        return DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('services as s', 's.id', '=', 'td.service_id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.payment_status', 'paid')
            ->whereNotNull('td.service_id')
            ->select(
                's.nama_layanan',
                DB::raw('SUM(td.quantity) as total_quantity'),
                DB::raw('SUM(td.total_price) as total_revenue')
            )
            ->groupBy('s.id', 's.nama_layanan')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get();
    }

    private function getTopProducts($startDate, $endDate)
    {
        return DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('products as p', 'p.id', '=', 'td.product_id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.payment_status', 'paid')
            ->whereNotNull('td.product_id')
            ->select(
                'p.nama_produk',
                DB::raw('SUM(td.quantity) as total_quantity'),
                DB::raw('SUM(td.total_price) as total_revenue')
            )
            ->groupBy('p.id', 'p.nama_produk')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get();
    }

    private function getPatientStats($startDate, $endDate)
    {
        $newPatients = Patient::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $activePatients = DB::table('patients as p')
            ->join('transactions as t', 't.patient_id', '=', 'p.id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->distinct('p.id')
            ->count('p.id');

        $medicalRecords = MedicalRecord::whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])->count();

        return [
            'new' => $newPatients,
            'active' => $activePatients,
            'medical_records' => $medicalRecords,
        ];
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'revenue');

        // Generate CSV export
        $filename = "laporan_{$type}_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'revenue':
                    $this->exportRevenue($file, $startDate, $endDate);
                    break;
                case 'transactions':
                    $this->exportTransactions($file, $startDate, $endDate);
                    break;
                case 'services':
                    $this->exportServices($file, $startDate, $endDate);
                    break;
                case 'products':
                    $this->exportProducts($file, $startDate, $endDate);
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRevenue($file, $startDate, $endDate)
    {
        fputcsv($file, ['Tanggal', 'Total Pendapatan']);
        
        $dailyRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($dailyRevenue as $revenue) {
            fputcsv($file, [
                $revenue->date,
                'Rp ' . number_format($revenue->revenue, 0, ',', '.')
            ]);
        }
    }

    private function exportTransactions($file, $startDate, $endDate)
    {
        fputcsv($file, ['ID Transaksi', 'Tanggal', 'Pasien', 'Total', 'Status', 'Metode Pembayaran']);
        
        $transactions = Transaction::with('patient')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($transactions as $transaction) {
            fputcsv($file, [
                $transaction->id,
                $transaction->tanggal_transaksi,
                $transaction->patient->nama_lengkap,
                'Rp ' . number_format($transaction->final_amount, 0, ',', '.'),
                ucfirst($transaction->payment_status),
                ucfirst($transaction->payment_method)
            ]);
        }
    }

    private function exportServices($file, $startDate, $endDate)
    {
        fputcsv($file, ['Nama Layanan', 'Jumlah Terjual', 'Total Pendapatan']);
        
        $services = $this->getTopServices($startDate, $endDate);

        foreach ($services as $service) {
            fputcsv($file, [
                $service->nama_layanan,
                $service->total_quantity,
                'Rp ' . number_format($service->total_revenue, 0, ',', '.')
            ]);
        }
    }

    private function exportProducts($file, $startDate, $endDate)
    {
        fputcsv($file, ['Nama Produk', 'Jumlah Terjual', 'Total Pendapatan']);
        
        $products = $this->getTopProducts($startDate, $endDate);

        foreach ($products as $product) {
            fputcsv($file, [
                $product->nama_produk,
                $product->total_quantity,
                'Rp ' . number_format($product->total_revenue, 0, ',', '.')
            ]);
        }
    }

    public function stockReports(Request $request)
    {
        // Stock Reports untuk Manajer Stok
        $lowStockThreshold = $request->get('threshold', 10);
        
        // Get date filters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get separate date filters for product movement
        $movementStartDate = $request->get('movement_start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $movementEndDate = $request->get('movement_end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Low stock products (no pagination needed as handled by frontend)
        $lowStockProducts = Product::where('stok', '<', $lowStockThreshold)
            ->orderBy('stok', 'asc')
            ->get();
        
        // Product movement with date filter (no pagination needed as handled by frontend)
        $productMovement = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('products as p', 'p.id', '=', 'td.product_id')
            ->whereNotNull('td.product_id')
            ->whereBetween('t.created_at', [$movementStartDate, $movementEndDate])
            ->select(
                'p.id',
                'p.nama_produk',
                'p.stok',
                DB::raw('SUM(td.quantity) as total_sold'),
                DB::raw('AVG(td.quantity) as avg_sold_per_transaction'),
                DB::raw('SUM(td.total_price) as total_revenue')
            )
            ->groupBy('p.id', 'p.nama_produk', 'p.stok')
            ->orderByDesc('total_sold')
            ->get();

        $stockValue = Product::selectRaw('SUM(stok * harga) as total_value')->first();

        return Inertia::render('Reports/Stock', [
            'lowStockProducts' => $lowStockProducts,
            'productMovement' => $productMovement,
            'stockValue' => $stockValue,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'movement_start_date' => $movementStartDate,
                'movement_end_date' => $movementEndDate,
                'threshold' => $lowStockThreshold,
            ],
        ]);
    }

    private function getPatientVisitsReport($startDate, $endDate)
    {
        // Total visits (reservations)
        $totalVisits = DB::table('reservations')
            ->whereBetween('tanggal_reservasi', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        // New vs returning patients
        $newPatients = DB::table('patients')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $returningPatients = DB::table('reservations as r1')
            ->join('patients as p', 'p.id', '=', 'r1.patient_id')
            ->whereBetween('r1.tanggal_reservasi', [$startDate, $endDate])
            ->where('r1.status', 'completed')
            ->where(function($query) use ($startDate) {
                $query->whereExists(function($subquery) use ($startDate) {
                    $subquery->select(DB::raw(1))
                        ->from('reservations as r2')
                        ->whereRaw('r2.patient_id = r1.patient_id')
                        ->where('r2.tanggal_reservasi', '<', $startDate);
                });
            })
            ->distinct('r1.patient_id')
            ->count('r1.patient_id');

        // Visits by service
        $visitsByService = DB::table('reservations as r')
            ->join('services as s', 's.id', '=', 'r.service_id')
            ->whereBetween('r.tanggal_reservasi', [$startDate, $endDate])
            ->where('r.status', 'completed')
            ->select('s.nama_layanan', DB::raw('COUNT(*) as total_visits'))
            ->groupBy('s.id', 's.nama_layanan')
            ->orderByDesc('total_visits')
            ->limit(10)
            ->get();

        // Daily visits trend
        $dailyVisits = DB::table('reservations')
            ->whereBetween('tanggal_reservasi', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(tanggal_reservasi) as date'),
                DB::raw('COUNT(*) as visits')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_visits' => $totalVisits,
            'new_patients' => $newPatients,
            'returning_patients' => $returningPatients,
            'by_service' => $visitsByService,
            'daily_trend' => $dailyVisits,
        ];
    }

    private function getDoctorPerformanceReport($startDate, $endDate)
    {
        // Doctor performance based on reservations
        $doctorStats = DB::table('reservations as r')
            ->join('users as u', 'u.id', '=', 'r.user_id') // Changed from doctor_id to user_id
            ->whereBetween('r.tanggal_reservasi', [$startDate, $endDate])
            ->where('r.status', 'completed')
            ->select(
                'u.name as doctor_name',
                DB::raw('COUNT(*) as total_patients'),
                DB::raw('COUNT(DISTINCT r.patient_id) as unique_patients')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_patients')
            ->get();

        // Doctor revenue contribution
        $doctorRevenue = DB::table('transactions as t')
            ->join('reservations as r', 'r.id', '=', 't.reservation_id')
            ->join('users as u', 'u.id', '=', 'r.user_id') // Changed from doctor_id to user_id
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.payment_status', 'paid')
            ->select(
                'u.name as doctor_name',
                DB::raw('SUM(t.final_amount) as total_revenue'),
                DB::raw('AVG(t.final_amount) as avg_revenue_per_transaction')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Most requested services by doctor
        $doctorServices = DB::table('reservations as r')
            ->join('users as u', 'u.id', '=', 'r.user_id') // Changed from doctor_id to user_id
            ->join('services as s', 's.id', '=', 'r.service_id')
            ->whereBetween('r.tanggal_reservasi', [$startDate, $endDate])
            ->where('r.status', 'completed')
            ->select(
                'u.name as doctor_name',
                's.nama_layanan',
                DB::raw('COUNT(*) as service_count')
            )
            ->groupBy('u.id', 'u.name', 's.id', 's.nama_layanan')
            ->orderByDesc('service_count')
            ->limit(20)
            ->get()
            ->groupBy('doctor_name')
            ->map(function ($services) {
                return $services->take(3); // Top 3 services per doctor
            });

        return [
            'statistics' => $doctorStats,
            'revenue' => $doctorRevenue,
            'top_services' => $doctorServices,
        ];
    }
}
