<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTarifRequest;
use App\Models\Tarif;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TarifController extends Controller
{
    public function index(): View
    {
        $tarifs = Tarif::withCount('transaksi')
            ->orderBy('id_tarif')
            ->paginate(10);

        return view('admin.tarif.index', compact('tarifs'));
    }

    public function create(): View
    {
        return view('admin.tarif.form', ['tarif' => new Tarif(['jenis_kendaraan' => 'motor'])]);
    }

    public function store(StoreTarifRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $tarif = Tarif::create([
            'jenis_kendaraan' => $validated['jenis_kendaraan'],
            'tarif_per_jam' => (int) $validated['tarif_per_jam'],
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Menambah tarif {$tarif->jenis_kendaraan}: Rp " . number_format($tarif->tarif_per_jam, 0, ',', '.')
        );

        return redirect()
            ->route('tarif.index')
            ->with('success', 'Tarif parkir berhasil ditambahkan.');
    }

    public function edit(Tarif $tarif): View
    {
        return view('admin.tarif.form', compact('tarif'));
    }

    public function update(StoreTarifRequest $request, Tarif $tarif): RedirectResponse
    {
        $validated = $request->validated();

        $tarif->update([
            'jenis_kendaraan' => $validated['jenis_kendaraan'],
            'tarif_per_jam' => (int) $validated['tarif_per_jam'],
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Mengubah tarif {$tarif->jenis_kendaraan}: Rp " . number_format($tarif->tarif_per_jam, 0, ',', '.')
        );

        return redirect()
            ->route('tarif.index')
            ->with('success', 'Tarif parkir berhasil diperbarui.');
    }

    public function destroy(Tarif $tarif): RedirectResponse
    {
        if ($tarif->transaksi()->exists()) {
            return redirect()
                ->route('tarif.index')
                ->with('error', 'Tarif tidak dapat dihapus karena sudah digunakan pada transaksi.');
        }

        $jenis = $tarif->jenis_kendaraan;
        $nominal = number_format((int) $tarif->tarif_per_jam, 0, ',', '.');
        $tarif->delete();

        LogAktivitasService::record(
            Auth::id(),
            "Menghapus tarif {$jenis}: Rp {$nominal}"
        );

        return redirect()
            ->route('tarif.index')
            ->with('success', 'Tarif parkir berhasil dihapus.');
    }
}
