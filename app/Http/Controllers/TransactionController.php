<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'resepsionis'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['patient', 'user', 'transactionDetails.service']);

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->date_to);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('metode')) {
            $query->where('payment_method', $request->metode);
        }

        // Search berdasarkan nama pasien
        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_telepon', 'like', '%' . $request->search . '%');
            });
        }

        $transactions = $query->orderBy('tanggal_transaksi', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(10)
                             ->withQueryString();

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status', 'metode']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::select('id', 'nama_lengkap', 'nomor_telepon')->get();
        $services = Service::orderBy('nama_layanan')->get();
        $products = Product::where('stok', '>', 0)->orderBy('nama_produk')->get();
        
        // Get today's reservations that are confirmed or completed
        $todaysReservations = \App\Models\Reservation::with(['patient', 'service', 'user'])
            ->whereDate('tanggal_reservasi', today())
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy('waktu_mulai')
            ->get();

        return Inertia::render('Transactions/Create', [
            'patients' => $patients,
            'services' => $services,
            'products' => $products,
            'todaysReservations' => $todaysReservations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug log
        Log::info('Transaction store request:', $request->all());
        
        // Enhanced validation - matching Vue form structure
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,transfer,other',
            'payment_status' => 'required|string|in:pending,paid,partially_paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        Log::info('Transaction validated data:', $validated);

        DB::beginTransaction();
        try {
            // Create the transaction - using correct field names based on database schema
            $transaction = Transaction::create([
                'patient_id' => $validated['patient_id'],
                'reservation_id' => $validated['reservation_id'],
                'nomor_transaksi' => 'TRX-' . now()->format('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT),
                'tanggal_transaksi' => now(),
                'total_amount' => $validated['total'],
                'discount' => 0,
                'tax' => 0,
                'final_amount' => $validated['total'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'notes' => null,
                'created_by' => auth()->id(),
            ]);

            Log::info('Transaction created with ID:', ['id' => $transaction->id]);

            // Process each item
            foreach ($validated['items'] as $item) {
                // Validate that either service_id or product_id is set
                if (empty($item['service_id']) && empty($item['product_id'])) {
                    throw new \Exception('Setiap item harus memiliki layanan atau produk');
                }

                $itemData = [
                    'transaction_id' => $transaction->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['subtotal'],
                    'notes' => null,
                ];

                if (!empty($item['service_id'])) {
                    $service = Service::find($item['service_id']);
                    if (!$service) {
                        throw new \Exception('Layanan tidak ditemukan');
                    }
                    $itemData['service_id'] = $item['service_id'];
                    $itemData['product_id'] = null;
                    $itemData['item_name'] = $service->nama_layanan;
                    
                    Log::info('Processing service item:', $itemData);
                } elseif (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('Produk tidak ditemukan');
                    }
                    $itemData['product_id'] = $item['product_id'];
                    $itemData['service_id'] = null;
                    $itemData['item_name'] = $product->nama_produk;
                    
                    // Check stock availability
                    if ($product->stok < $item['quantity']) {
                        throw new \Exception("Stok {$product->nama_produk} tidak mencukupi. Tersedia: {$product->stok}");
                    }
                    
                    // Update product stock
                    $product->decrement('stok', $item['quantity']);
                    Log::info("Product stock updated - Product ID: {$product->id}, New Stock: {$product->stok}");
                }

                $transactionDetail = TransactionDetail::create($itemData);
                Log::info('Transaction Detail Created:', $transactionDetail->toArray());
            }

            DB::commit();
            Log::info('Transaction stored successfully with ID: ' . $transaction->id);

            return redirect()->route('transactions.index')
                           ->with('success', 'Transaksi berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transaction Store Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['patient', 'user', 'transactionDetails.service', 'transactionDetails.product']);

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        // Hanya admin atau resepsionis yang membuat transaksi yang bisa edit
        if (auth()->user()->role !== 'admin' && $transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $transaction->load(['patient', 'transactionDetails.service', 'transactionDetails.product']);
        $patients = Patient::select('id', 'nama_lengkap', 'nomor_telepon')->get();
        $services = Service::orderBy('nama_layanan')->get();
        $products = Product::orderBy('nama_produk')->get();

        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction,
            'patients' => $patients,
            'services' => $services,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Hanya admin atau resepsionis yang membuat transaksi yang bisa update
        if (auth()->user()->role !== 'admin' && $transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Debug log
        Log::info('Transaction update request:', $request->all());

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tanggal_transaksi' => 'required|date',
            'payment_method' => 'required|string|in:cash,debit_card,credit_card,transfer',
            'payment_status' => 'required|string|in:paid,pending,partially_paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        Log::info('Transaction update validated data:', $validated);

        DB::beginTransaction();
        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['subtotal'];
            }

            // Update transaction
            $transaction->update([
                'patient_id' => $validated['patient_id'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
            ]);

            Log::info('Transaction updated with ID:', ['id' => $transaction->id]);

            // Delete old transaction details
            $transaction->transactionDetails()->delete();

            // Process each updated item
            foreach ($validated['items'] as $item) {
                // Validate that either service_id or product_id is set
                if (empty($item['service_id']) && empty($item['product_id'])) {
                    throw new \Exception('Setiap item harus memiliki layanan atau produk');
                }

                $itemData = [
                    'transaction_id' => $transaction->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['subtotal'],
                    'notes' => null,
                ];

                if (!empty($item['service_id'])) {
                    $service = Service::find($item['service_id']);
                    if (!$service) {
                        throw new \Exception('Layanan tidak ditemukan');
                    }
                    $itemData['service_id'] = $item['service_id'];
                    $itemData['product_id'] = null;
                    $itemData['item_name'] = $service->nama_layanan;
                    
                    Log::info('Processing service item for update:', $itemData);
                } elseif (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('Produk tidak ditemukan');
                    }
                    $itemData['product_id'] = $item['product_id'];
                    $itemData['service_id'] = null;
                    $itemData['item_name'] = $product->nama_produk;
                    
                    // Note: For updates, we're not adjusting stock here
                    // Stock adjustments would require more complex logic to handle
                    // the difference between old and new quantities
                    
                    Log::info('Processing product item for update:', $itemData);
                }

                $transactionDetail = TransactionDetail::create($itemData);
                Log::info('Transaction Detail Updated:', $transactionDetail->toArray());
            }

            DB::commit();
            Log::info('Transaction updated successfully with ID: ' . $transaction->id);

            return redirect()->route('transactions.index')
                           ->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transaction Update Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Hanya admin yang bisa hapus transaksi
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        try {
            $transaction->delete();
            return back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => 'required|string|in:pending,paid,partially_paid,cancelled',
        ]);

        $transaction->update([
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
