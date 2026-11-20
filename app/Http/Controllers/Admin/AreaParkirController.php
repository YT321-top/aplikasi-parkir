<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaParkirRequest;
use App\Models\AreaParkir;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AreaParkirController extends Controller
{
    public function index(): View
    {
        $areas = AreaParkir::withCount('transaksi')
            ->orderBy('nama_area')
            ->paginate(10);

        return view('admin.area-parkir.index', compact('areas'));
    }

    public function create(): View
    {
        return view('admin.area-parkir.form', ['area' => new AreaParkir(['terisi' => 0])]);
    }

    public function store(StoreAreaParkirRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $area = AreaParkir::create([
            'nama_area' => $validated['nama_area'],
            'kapasitas' => (int) $validated['kapasitas'],
            'terisi' => 0,
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Menambah area parkir: {$area->nama_area} (kapasitas {$area->kapasitas})"
        );

        return redirect()
            ->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function edit(AreaParkir $area_parkir): View
    {
        return view('admin.area-parkir.form', ['area' => $area_parkir]);
    }

    public function update(StoreAreaParkirRequest $request, AreaParkir $area_parkir): RedirectResponse
    {
        $validated = $request->validated();

        $area_parkir->update([
            'nama_area' => $validated['nama_area'],
            'kapasitas' => (int) $validated['kapasitas'],
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Mengubah area parkir: {$area_parkir->nama_area} (kapasitas {$area_parkir->kapasitas})"
        );

        return redirect()
            ->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil diperbarui.');
    }

    public function destroy(AreaParkir $area_parkir): RedirectResponse
    {
        if ($area_parkir->transaksi()->exists() || $area_parkir->terisi > 0) {
            return redirect()
                ->route('area-parkir.index')
                ->with('error', 'Area parkir tidak dapat dihapus karena masih terhubung dengan transaksi.');
        }

        $namaArea = $area_parkir->nama_area;
        $area_parkir->delete();

        LogAktivitasService::record(
            Auth::id(),
            "Menghapus area parkir: {$namaArea}"
        );

        return redirect()
            ->route('area-parkir.index')
            ->with('success', 'Area parkir berhasil dihapus.');
    }
}
