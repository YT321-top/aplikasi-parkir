<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const BULAN_SINGKAT = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if ((int) $user->status_aktif !== 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['username' => 'Akun Anda sedang dinonaktifkan.']);
        }

        $now = Carbon::now('Asia/Jakarta');

        $data = match ($user->role) {
            'admin' => $this->buildAdminData($now),
            'petugas' => $this->buildPetugasData($now),
            'owner' => $this->buildOwnerData($now),
            default => abort(403, 'Role tidak dikenali.'),
        };

        return view('dashboard.index', array_merge([
            'user' => $user,
            'role' => $user->role,
        ], $data));
    }

    private function buildAdminData(Carbon $now): array
    {
        $today = $now->toDateString();
        $areas = AreaParkir::orderBy('nama_area')->get();
        $slotTersedia = $areas->sum(fn (AreaParkir $area) => $area->sisa_slot);

        $stats = [
            [
                'label' => 'Total User',
                'value' => number_format(User::count(), 0, ',', '.'),
                'icon' => 'users',
                'highlight' => false,
            ],
            [
                'label' => 'Total Kendaraan',
                'value' => number_format(Kendaraan::count(), 0, ',', '.'),
                'icon' => 'car',
                'highlight' => false,
            ],
            [
                'label' => 'Sedang Parkir',
                'value' => number_format(Transaksi::where('status', 'masuk')->count(), 0, ',', '.'),
                'icon' => 'parking',
                'highlight' => true,
            ],
            [
                'label' => 'Slot Tersedia',
                'value' => number_format($slotTersedia, 0, ',', '.'),
                'icon' => 'area',
                'highlight' => false,
            ],
            [
                'label' => 'Transaksi Hari Ini',
                'value' => number_format(Transaksi::whereDate('waktu_masuk', $today)->count(), 0, ',', '.'),
                'icon' => 'clock',
                'highlight' => false,
            ],
        ];

        // Grafik 6 bulan terakhir: Motor vs Mobil vs Lainnya
        $labels = [];
        $motorSeries = [];
        $mobilSeries = [];
        $lainnyaSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonthsNoOverflow($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $labels[] = self::BULAN_SINGKAT[$month->month];

            $motorCount = Transaksi::whereBetween('waktu_masuk', [$start, $end])
                ->whereHas('kendaraan', fn ($q) => $q->where('jenis_kendaraan', 'motor'))
                ->count();

            $mobilCount = Transaksi::whereBetween('waktu_masuk', [$start, $end])
                ->whereHas('kendaraan', fn ($q) => $q->where('jenis_kendaraan', 'mobil'))
                ->count();

            $lainnyaCount = Transaksi::whereBetween('waktu_masuk', [$start, $end])
                ->whereHas('kendaraan', fn ($q) => $q->where('jenis_kendaraan', 'lainnya'))
                ->count();

            $motorSeries[] = $motorCount;
            $mobilSeries[] = $mobilCount;
            $lainnyaSeries[] = $lainnyaCount;
        }

        $logs = LogAktivitas::with('user')
            ->orderByDesc('waktu_aktivitas')
            ->orderByDesc('id_log')
            ->limit(10)
            ->get();

        return [
            'periodLabel' => 'Periode hari ini',
            'stats' => $stats,
            'chart' => [
                'title' => 'Statistik',
                'labels' => $labels,
                'series1' => [
                    'label' => 'Motor',
                    'data' => $motorSeries,
                    'unit' => 'transaksi',
                ],
                'series2' => [
                    'label' => 'Mobil',
                    'data' => $mobilSeries,
                    'unit' => 'transaksi',
                ],
                'series3' => [
                    'label' => 'Lainnya',
                    'data' => $lainnyaSeries,
                    'unit' => 'transaksi',
                ],
                'dualAxis' => false,
            ],
            'areas' => $areas,
            'logs' => $logs,
        ];
    }

    private function buildPetugasData(Carbon $now): array
    {
        $today = $now->toDateString();
        $areas = AreaParkir::orderBy('nama_area')->get();
        $slotTersedia = $areas->sum(fn (AreaParkir $area) => $area->sisa_slot);

        $stats = [
            [
                'label' => 'Sedang Parkir',
                'value' => number_format(Transaksi::where('status', 'masuk')->count(), 0, ',', '.'),
                'icon' => 'parking',
                'highlight' => true,
            ],
            [
                'label' => 'Masuk Hari Ini',
                'value' => number_format(Transaksi::whereDate('waktu_masuk', $today)->count(), 0, ',', '.'),
                'icon' => 'arrow-down',
                'highlight' => false,
            ],
            [
                'label' => 'Keluar Hari Ini',
                'value' => number_format(
                    Transaksi::where('status', 'keluar')->whereDate('waktu_keluar', $today)->count(),
                    0,
                    ',',
                    '.'
                ),
                'icon' => 'arrow-up',
                'highlight' => false,
            ],
            [
                'label' => 'Slot Tersedia',
                'value' => number_format($slotTersedia, 0, ',', '.'),
                'icon' => 'area',
                'highlight' => false,
            ],
        ];

        // Grafik 7 hari terakhir: Masuk vs Keluar
        $labels = [];
        $masukSeries = [];
        $keluarSeries = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dateStr = $day->toDateString();

            $labels[] = $day->day . ' ' . self::BULAN_SINGKAT[$day->month];
            $masukSeries[] = Transaksi::whereDate('waktu_masuk', $dateStr)->count();
            $keluarSeries[] = Transaksi::where('status', 'keluar')
                ->whereDate('waktu_keluar', $dateStr)
                ->count();
        }

        $activeTransactions = Transaksi::with(['kendaraan', 'area', 'tarif'])
            ->where('status', 'masuk')
            ->orderByDesc('waktu_masuk')
            ->get();

        return [
            'periodLabel' => 'Periode hari ini',
            'stats' => $stats,
            'chart' => [
                'title' => 'Statistik',
                'labels' => $labels,
                'series1' => [
                    'label' => 'Masuk',
                    'data' => $masukSeries,
                    'unit' => 'kendaraan',
                ],
                'series2' => [
                    'label' => 'Keluar',
                    'data' => $keluarSeries,
                    'unit' => 'kendaraan',
                ],
                'dualAxis' => false,
            ],
            'areas' => $areas,
            'activeTransactions' => $activeTransactions,
        ];
    }

    private function buildOwnerData(Carbon $now): array
    {
        $today = $now->toDateString();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $pendapatanBulanIni = (int) Transaksi::where('status', 'keluar')
            ->whereBetween('waktu_keluar', [$monthStart, $monthEnd])
            ->sum('biaya_total');

        $pendapatanHariIni = (int) Transaksi::where('status', 'keluar')
            ->whereDate('waktu_keluar', $today)
            ->sum('biaya_total');

        $transaksiSelesaiBulanIni = Transaksi::where('status', 'keluar')
            ->whereBetween('waktu_keluar', [$monthStart, $monthEnd])
            ->count();

        $kendaraanAktif = Transaksi::where('status', 'masuk')->count();

        $stats = [
            [
                'label' => 'Pendapatan Bulan Ini',
                'value' => 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'),
                'icon' => 'wallet',
                'highlight' => true,
            ],
            [
                'label' => 'Pendapatan Hari Ini',
                'value' => 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'),
                'icon' => 'cash',
                'highlight' => false,
            ],
            [
                'label' => 'Selesai Bulan Ini',
                'value' => number_format($transaksiSelesaiBulanIni, 0, ',', '.'),
                'icon' => 'check',
                'highlight' => false,
            ],
            [
                'label' => 'Kendaraan Aktif',
                'value' => number_format($kendaraanAktif, 0, ',', '.'),
                'icon' => 'parking',
                'highlight' => false,
            ],
        ];

        // Grafik 6 bulan terakhir: Pendapatan (Rp) & Total Transaksi
        $labels = [];
        $pendapatanSeries = [];
        $transaksiSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonthsNoOverflow($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $labels[] = self::BULAN_SINGKAT[$month->month];

            $pendapatanSeries[] = (int) Transaksi::where('status', 'keluar')
                ->whereBetween('waktu_keluar', [$start, $end])
                ->sum('biaya_total');

            $transaksiSeries[] = Transaksi::whereBetween('waktu_masuk', [$start, $end])->count();
        }

        $recentCompleted = Transaksi::with(['kendaraan', 'area'])
            ->where('status', 'keluar')
            ->orderByDesc('waktu_keluar')
            ->limit(5)
            ->get();

        $recentTransactions = Transaksi::with(['kendaraan', 'area', 'tarif', 'user'])
            ->orderByDesc('waktu_masuk')
            ->limit(10)
            ->get();

        return [
            'periodLabel' => 'Periode bulan ini',
            'stats' => $stats,
            'chart' => [
                'title' => 'Statistik',
                'labels' => $labels,
                'series1' => [
                    'label' => 'Pendapatan',
                    'data' => $pendapatanSeries,
                    'unit' => 'rupiah',
                ],
                'series2' => [
                    'label' => 'Total Transaksi',
                    'data' => $transaksiSeries,
                    'unit' => 'transaksi',
                ],
                'dualAxis' => true,
            ],
            'recentCompleted' => $recentCompleted,
            'recentTransactions' => $recentTransactions,
        ];
    }
}
