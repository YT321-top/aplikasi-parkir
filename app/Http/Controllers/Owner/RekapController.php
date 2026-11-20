<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AreaParkir;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);
        $baseQuery = $this->buildFilteredQuery($filters);

        $totalTransaksi = (clone $baseQuery)->count();
        $transaksiSelesai = (clone $baseQuery)->where('status', 'keluar')->count();
        $totalPendapatan = (int) (clone $baseQuery)->where('status', 'keluar')->sum('biaya_total');
        $rataDurasi = (float) ((clone $baseQuery)->where('status', 'keluar')->avg('durasi_jam') ?? 0);

        $transaksis = (clone $baseQuery)
            ->with(['kendaraan', 'area', 'tarif', 'user'])
            ->orderByDesc('waktu_masuk')
            ->orderByDesc('id_parkir')
            ->paginate(20)
            ->withQueryString();

        $areas = AreaParkir::orderBy('nama_area')->get(['id_area', 'nama_area']);

        return view('owner.rekap.index', compact(
            'transaksis',
            'areas',
            'filters',
            'totalTransaksi',
            'transaksiSelesai',
            'totalPendapatan',
            'rataDurasi'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->extractFilters($request);
        $rows = $this->buildFilteredQuery($filters)
            ->with(['kendaraan', 'area', 'tarif', 'user'])
            ->orderByDesc('waktu_masuk')
            ->orderByDesc('id_parkir')
            ->get();

        $filename = sprintf(
            'rekap-parkir-%s-sd-%s.csv',
            $filters['tanggal_mulai'],
            $filters['tanggal_selesai']
        );

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar terbuka rapi di Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No Parkir',
                'Plat Nomor',
                'Jenis Kendaraan',
                'Pemilik',
                'Area Parkir',
                'Waktu Masuk',
                'Waktu Keluar',
                'Durasi (Jam)',
                'Tarif per Jam (Rp)',
                'Biaya Total (Rp)',
                'Status',
                'Petugas',
            ]);

            foreach ($rows as $trx) {
                fputcsv($handle, [
                    '#' . $trx->id_parkir,
                    $trx->kendaraan?->plat_nomor ?? '-',
                    ucfirst($trx->kendaraan?->jenis_kendaraan ?? '-'),
                    $trx->kendaraan?->pemilik ?? '-',
                    $trx->area?->nama_area ?? '-',
                    $trx->waktu_masuk ? $trx->waktu_masuk->format('Y-m-d H:i:s') : '-',
                    $trx->waktu_keluar ? $trx->waktu_keluar->format('Y-m-d H:i:s') : '-',
                    $trx->durasi_jam ?? '-',
                    $trx->tarif ? (int) $trx->tarif->tarif_per_jam : 0,
                    $trx->biaya_total !== null ? (int) $trx->biaya_total : 0,
                    ucfirst($trx->status),
                    $trx->user?->nama_lengkap ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function extractFilters(Request $request): array
    {
        $now = Carbon::now('Asia/Jakarta');

        $tanggalMulai = trim((string) $request->query('tanggal_mulai', ''));
        $tanggalSelesai = trim((string) $request->query('tanggal_selesai', ''));

        if ($tanggalMulai === '') {
            $tanggalMulai = $now->copy()->startOfMonth()->toDateString();
        }
        if ($tanggalSelesai === '') {
            $tanggalSelesai = $now->toDateString();
        }

        return [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'id_area' => trim((string) $request->query('id_area', '')),
            'jenis_kendaraan' => trim((string) $request->query('jenis_kendaraan', '')),
            'status' => trim((string) $request->query('status', '')),
        ];
    }

    private function buildFilteredQuery(array $filters): Builder
    {
        return Transaksi::query()
            ->when($filters['tanggal_mulai'] !== '', function (Builder $q) use ($filters) {
                $q->whereDate('waktu_masuk', '>=', $filters['tanggal_mulai']);
            })
            ->when($filters['tanggal_selesai'] !== '', function (Builder $q) use ($filters) {
                $q->whereDate('waktu_masuk', '<=', $filters['tanggal_selesai']);
            })
            ->when($filters['id_area'] !== '', function (Builder $q) use ($filters) {
                $q->where('id_area', (int) $filters['id_area']);
            })
            ->when(in_array($filters['jenis_kendaraan'], ['motor', 'mobil', 'lainnya'], true), function (Builder $q) use ($filters) {
                $q->whereHas('kendaraan', function (Builder $sub) use ($filters) {
                    $sub->where('jenis_kendaraan', $filters['jenis_kendaraan']);
                });
            })
            ->when(in_array($filters['status'], ['masuk', 'keluar'], true), function (Builder $q) use ($filters) {
                $q->where('status', $filters['status']);
            });
    }
}
