<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Search functionality
        if ($request->has('search')) {
            $query->where('nama_layanan', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'LIKE', '%' . $request->search . '%');
        }

        $services = $query->orderBy('nama_layanan')
                         ->paginate(10)
                         ->withQueryString();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Services/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255|unique:services',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
                         ->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return Inertia::render('Services/Show', [
            'service' => $service,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return Inertia::render('Services/Edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255|unique:services,nama_layanan,' . $service->id,
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
                         ->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Catalog layanan untuk pasien
     */
    public function catalog()
    {
        $services = Service::orderBy('nama_layanan')->get();

        return Inertia::render('Services/Catalog', [
            'services' => $services,
        ]);
    }
}
