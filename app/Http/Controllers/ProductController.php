<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('nama_produk', 'LIKE', '%' . $request->search . '%')
                ->orWhere('deskripsi', 'LIKE', '%' . $request->search . '%');
        }

        // Stock status filter
        if ($request->has('stok_status') && $request->stok_status) {
            switch ($request->stok_status) {
                case 'empty':
                    $query->where('stok', 0);
                    break;
                case 'low':
                    $query->where('stok', '>', 0)->where('stok', '<=', 10);
                    break;
                case 'available':
                    $query->where('stok', '>', 0);
                    break;
            }
        }

        $products = $query->orderBy('nama_produk')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'stok_status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255|unique:products',
            'deskripsi' => 'nullable|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $stockHistory = $product->stockHistories()
            ->with('user')
            ->take(10)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'stockHistory' => $stockHistory,
            'auth' => [
                'user' => auth()->user()
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255|unique:products,nama_produk,' . $product->id,
            'deskripsi' => 'nullable|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Adjust product stock manually.
     */
    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'new_stock' => 'required|integer|min:0',
        ]);

        $oldStock = $product->stok;
        $newStock = $validated['new_stock'];

        // Update product stock
        $product->update(['stok' => $newStock]);

        // Record stock history
        ProductStockHistory::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reason' => 'Manual adjustment',
        ]);

        $message = "Stok produk '{$product->nama_produk}' berhasil diubah dari {$oldStock} menjadi {$newStock}.";

        return back()->with('success', $message);
    }

    /**
     * Display products catalog for patients
     */
    public function catalog(Request $request)
    {
        $products = Product::where('stok', '>', 0) // Only show products with stock
            ->orderBy('nama_produk')
            ->get();

        return Inertia::render('Products/Catalog', [
            'products' => $products,
        ]);
    }
}
