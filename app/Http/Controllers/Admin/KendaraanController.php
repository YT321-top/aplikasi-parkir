<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKendaraanRequest;
use App\Models\Kendaraan;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $jenis = trim((string) $request->query('jenis_kendaraan', ''));

        $kendaraans = Kendaraan::with('user')
            ->withCount('transaksi')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('plat_nomor', 'ILIKE', "%{$search}%")
                        ->orWhere('pemilik', 'ILIKE', "%{$search}%");
                });
            })
            ->when(in_array($jenis, ['motor', 'mobil', 'lainnya'], true), function ($q) use ($jenis) {
                $q->where('jenis_kendaraan', $jenis);
            })
            ->orderByDesc('id_kendaraan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kendaraan.index', compact('kendaraans', 'search', 'jenis'));
    }

    public function create(): View
    {
        return view('admin.kendaraan.form', ['kendaraan' => new Kendaraan(['jenis_kendaraan' => 'motor'])]);
    }

    public function store(StoreKendaraanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $kendaraan = Kendaraan::create([
            'plat_nomor' => $validated['plat_nomor'],
            'jenis_kendaraan' => $validated['jenis_kendaraan'],
            'warna' => $validated['warna'],
            'pemilik' => $validated['pemilik'],
            'id_user' => Auth::id(),
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Menambah kendaraan: {$kendaraan->plat_nomor} ({$kendaraan->jenis_kendaraan})"
        );

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan): View
    {
        return view('admin.kendaraan.form', compact('kendaraan'));
    }

    public function update(StoreKendaraanRequest $request, Kendaraan $kendaraan): RedirectResponse
    {
        $validated = $request->validated();

        $kendaraan->update([
            'plat_nomor' => $validated['plat_nomor'],
            'jenis_kendaraan' => $validated['jenis_kendaraan'],
            'warna' => $validated['warna'],
            'pemilik' => $validated['pemilik'],
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Mengubah kendaraan: {$kendaraan->plat_nomor}"
        );

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        if ($kendaraan->transaksi()->exists()) {
            return redirect()
                ->route('kendaraan.index')
                ->with('error', 'Kendaraan tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();

        LogAktivitasService::record(
            Auth::id(),
            "Menghapus kendaraan: {$plat}"
        );

        return redirect()
            ->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus.');
    }
}
