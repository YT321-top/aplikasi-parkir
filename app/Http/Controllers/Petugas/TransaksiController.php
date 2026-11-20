<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransaksiMasukRequest;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(): View
    {
        $activeTransactions = Transaksi::with(['kendaraan', 'area', 'tarif'])
            ->where('status', 'masuk')
            ->orderByDesc('waktu_masuk')
            ->get();

        $activeKendaraanIds = $activeTransactions->pluck('id_kendaraan')->all();

        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        $tarifs = Tarif::orderBy('id_tarif')->get();

        return view('petugas.transaksi.index', compact(
            'activeTransactions',
            'activeKendaraanIds',
            'kendaraans',
            'areas',
            'tarifs'
        ));
    }

    public function storeMasuk(StoreTransaksiMasukRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $transaksi = DB::transaction(function () use ($validated) {
                if ($validated['mode_kendaraan'] === 'baru') {
                    $kendaraan = Kendaraan::where('plat_nomor', $validated['plat_nomor'])->first();

                    if (!$kendaraan) {
                        $kendaraan = Kendaraan::create([
                            'plat_nomor' => $validated['plat_nomor'],
                            'jenis_kendaraan' => $validated['jenis_kendaraan'],
                            'warna' => $validated['warna'],
                            'pemilik' => $validated['pemilik'],
                            'id_user' => Auth::id(),
                        ]);
                    }
                } else {
                    $kendaraan = Kendaraan::findOrFail($validated['id_kendaraan']);
                }

                $sedangParkir = Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                    ->where('status', 'masuk')
                    ->lockForUpdate()
                    ->exists();

                if ($sedangParkir) {
                    throw ValidationException::withMessages([
                        'id_kendaraan' => 'Kendaraan masih berstatus parkir.',
                    ]);
                }

                $area = AreaParkir::lockForUpdate()->findOrFail($validated['id_area']);

                if ((int) $area->terisi >= (int) $area->kapasitas) {
                    throw ValidationException::withMessages([
                        'id_area' => 'Area parkir sudah penuh.',
                    ]);
                }

                $trx = Transaksi::create([
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'waktu_masuk' => now(),
                    'waktu_keluar' => null,
                    'id_tarif' => (int) $validated['id_tarif'],
                    'durasi_jam' => null,
                    'biaya_total' => null,
                    'status' => 'masuk',
                    'id_user' => Auth::id(),
                    'id_area' => $area->id_area,
                ]);

                $area->increment('terisi');

                LogAktivitasService::record(
                    Auth::id(),
                    "Kendaraan masuk: {$kendaraan->plat_nomor} ({$area->nama_area})"
                );

                return $trx;
            });
        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('transaksi.index')
            ->with('success', "Kendaraan masuk tercatat (#{$transaksi->id_parkir}).");
    }

    public function prosesKeluar(Transaksi $transaksi): RedirectResponse
    {
        try {
            $result = DB::transaction(function () use ($transaksi) {
                $trx = Transaksi::with(['kendaraan', 'tarif'])
                    ->lockForUpdate()
                    ->findOrFail($transaksi->id_parkir);

                if ($trx->status !== 'masuk') {
                    return null;
                }

                $waktuKeluar = now();
                $menit = $trx->waktu_masuk->diffInMinutes($waktuKeluar);
                $durasiJam = max(1, (int) ceil($menit / 60));
                $biayaTotal = $durasiJam * (int) $trx->tarif->tarif_per_jam;

                $trx->update([
                    'waktu_keluar' => $waktuKeluar,
                    'durasi_jam' => $durasiJam,
                    'biaya_total' => $biayaTotal,
                    'status' => 'keluar',
                ]);

                $area = AreaParkir::lockForUpdate()->findOrFail($trx->id_area);
                $area->update([
                    'terisi' => max(0, (int) $area->terisi - 1),
                ]);

                $plat = $trx->kendaraan?->plat_nomor ?? '-';
                LogAktivitasService::record(
                    Auth::id(),
                    "Kendaraan keluar: {$plat}, total Rp " . number_format($biayaTotal, 0, ',', '.')
                );

                return $trx;
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('transaksi.index')
                ->with('error', 'Gagal memproses kendaraan keluar.');
        }

        if (!$result) {
            return redirect()
                ->route('transaksi.index')
                ->with('error', 'Transaksi ini sudah berstatus keluar.');
        }

        return redirect()
            ->route('transaksi.struk', $result->id_parkir)
            ->with('success', 'Kendaraan keluar berhasil diproses.');
    }

    public function riwayat(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $transaksis = Transaksi::with(['kendaraan', 'area', 'tarif', 'user'])
            ->when(in_array($status, ['masuk', 'keluar'], true), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($sub) use ($search) {
                    $sub->where('plat_nomor', 'ILIKE', "%{$search}%")
                        ->orWhere('pemilik', 'ILIKE', "%{$search}%");
                });
            })
            ->orderByDesc('waktu_masuk')
            ->orderByDesc('id_parkir')
            ->paginate(15)
            ->withQueryString();

        return view('petugas.transaksi.riwayat', compact('transaksis', 'search', 'status'));
    }

    public function struk(Transaksi $transaksi): View
    {
        $transaksi->load(['kendaraan', 'area', 'tarif', 'user']);

        return view('petugas.transaksi.struk', compact('transaksi'));
    }
}
