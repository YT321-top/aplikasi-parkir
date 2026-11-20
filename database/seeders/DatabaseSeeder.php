<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        // 1. 3 Akun Default
        $admin = User::create([
            'nama_lengkap' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_aktif' => 1,
        ]);

        $petugas = User::create([
            'nama_lengkap' => 'Budi Santoso',
            'username' => 'petugas',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'status_aktif' => 1,
        ]);

        $owner = User::create([
            'nama_lengkap' => 'Siti Rahma',
            'username' => 'owner',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status_aktif' => 1,
        ]);

        // 2. 3 Data Tarif
        $tarifMotor = Tarif::create([
            'jenis_kendaraan' => 'motor',
            'tarif_per_jam' => 2000,
        ]);

        $tarifMobil = Tarif::create([
            'jenis_kendaraan' => 'mobil',
            'tarif_per_jam' => 5000,
        ]);

        $tarifLainnya = Tarif::create([
            'jenis_kendaraan' => 'lainnya',
            'tarif_per_jam' => 10000,
        ]);

        // 3. 3 Data Area Parkir
        $areaMotor = AreaParkir::create([
            'nama_area' => 'Area A - Motor',
            'kapasitas' => 50,
            'terisi' => 0,
        ]);

        $areaMobil = AreaParkir::create([
            'nama_area' => 'Area B - Mobil',
            'kapasitas' => 30,
            'terisi' => 0,
        ]);

        $areaUmum = AreaParkir::create([
            'nama_area' => 'Area C - Umum',
            'kapasitas' => 20,
            'terisi' => 0,
        ]);

        // 4. 8 Data Kendaraan
        $kendaraanData = [
            [
                'plat_nomor' => 'B 1234 ABC',
                'jenis_kendaraan' => 'motor',
                'warna' => 'Hitam',
                'pemilik' => 'Andi Pratama',
                'id_user' => $admin->id_user,
            ],
            [
                'plat_nomor' => 'B 5678 DEF',
                'jenis_kendaraan' => 'motor',
                'warna' => 'Merah',
                'pemilik' => 'Dewi Lestari',
                'id_user' => $admin->id_user,
            ],
            [
                'plat_nomor' => 'D 9012 GHI',
                'jenis_kendaraan' => 'motor',
                'warna' => 'Putih',
                'pemilik' => 'Rizky Fauzan',
                'id_user' => $petugas->id_user,
            ],
            [
                'plat_nomor' => 'B 2345 JKL',
                'jenis_kendaraan' => 'mobil',
                'warna' => 'Silver',
                'pemilik' => 'Hendra Wijaya',
                'id_user' => $admin->id_user,
            ],
            [
                'plat_nomor' => 'B 6789 MNO',
                'jenis_kendaraan' => 'mobil',
                'warna' => 'Hitam',
                'pemilik' => 'Nadia Putri',
                'id_user' => $admin->id_user,
            ],
            [
                'plat_nomor' => 'F 3456 PQR',
                'jenis_kendaraan' => 'mobil',
                'warna' => 'Putih',
                'pemilik' => 'Fajar Nugroho',
                'id_user' => $petugas->id_user,
            ],
            [
                'plat_nomor' => 'B 7890 STU',
                'jenis_kendaraan' => 'lainnya',
                'warna' => 'Kuning',
                'pemilik' => 'PT Logistik Jaya',
                'id_user' => $admin->id_user,
            ],
            [
                'plat_nomor' => 'D 4567 VWX',
                'jenis_kendaraan' => 'lainnya',
                'warna' => 'Biru',
                'pemilik' => 'CV Mandiri Trans',
                'id_user' => $petugas->id_user,
            ],
        ];

        $kendaraanList = [];
        foreach ($kendaraanData as $item) {
            $kendaraanList[] = Kendaraan::create($item);
        }

        // Helper pemetaan tarif & area berdasarkan jenis kendaraan
        $tarifByJenis = [
            'motor' => $tarifMotor,
            'mobil' => $tarifMobil,
            'lainnya' => $tarifLainnya,
        ];

        $areaByJenis = [
            'motor' => $areaMotor,
            'mobil' => $areaMobil,
            'lainnya' => $areaUmum,
        ];

        // 5. 18 Data Transaksi Dummy (15 keluar dalam 6 bulan terakhir + 3 masuk hari ini)
        $bulanDate = function (int $monthsAgo, int $day, int $hour, int $minute) use ($now): Carbon {
            return $now->copy()
                ->subMonthsNoOverflow($monthsAgo)
                ->startOfMonth()
                ->addDays($day - 1)
                ->setTime($hour, $minute, 0);
        };

        $todayStart = $now->copy()->startOfDay();
        $todayCheckoutMasuk = $now->copy()->subHours(3)->lt($todayStart)
            ? $todayStart->copy()->addMinutes(5)
            : $now->copy()->subHours(3);
        $todayCheckoutKeluar = $now->copy()->subMinutes(20)->lte($todayCheckoutMasuk)
            ? $todayCheckoutMasuk->copy()->addMinutes(45)
            : $now->copy()->subMinutes(20);
        if ($todayCheckoutKeluar->gt($now)) {
            $todayCheckoutKeluar = $now->copy();
        }
        $todayCheckoutDurasi = max(1, (int) ceil($todayCheckoutMasuk->diffInMinutes($todayCheckoutKeluar) / 60));

        // 15 transaksi selesai (keluar)
        $transaksiKeluarSeed = [
            // 5 bulan lalu (2 transaksi)
            ['kendaraan' => $kendaraanList[0], 'masuk' => $bulanDate(5, 6, 8, 15), 'durasi' => 2],
            ['kendaraan' => $kendaraanList[3], 'masuk' => $bulanDate(5, 18, 10, 30), 'durasi' => 3],

            // 4 bulan lalu (2 transaksi)
            ['kendaraan' => $kendaraanList[1], 'masuk' => $bulanDate(4, 9, 9, 0), 'durasi' => 4],
            ['kendaraan' => $kendaraanList[4], 'masuk' => $bulanDate(4, 21, 13, 15), 'durasi' => 2],

            // 3 bulan lalu (2 transaksi)
            ['kendaraan' => $kendaraanList[2], 'masuk' => $bulanDate(3, 5, 7, 45), 'durasi' => 3],
            ['kendaraan' => $kendaraanList[6], 'masuk' => $bulanDate(3, 19, 11, 20), 'durasi' => 2],

            // 2 bulan lalu (3 transaksi)
            ['kendaraan' => $kendaraanList[0], 'masuk' => $bulanDate(2, 4, 8, 10), 'durasi' => 2],
            ['kendaraan' => $kendaraanList[5], 'masuk' => $bulanDate(2, 14, 14, 0), 'durasi' => 4],
            ['kendaraan' => $kendaraanList[1], 'masuk' => $bulanDate(2, 23, 16, 30), 'durasi' => 1],

            // 1 bulan lalu (2 transaksi)
            ['kendaraan' => $kendaraanList[2], 'masuk' => $bulanDate(1, 8, 9, 15), 'durasi' => 3],
            ['kendaraan' => $kendaraanList[3], 'masuk' => $bulanDate(1, 20, 12, 0), 'durasi' => 5],

            // 7 hari terakhir (3 transaksi hari sebelumnya + 1 transaksi hari ini)
            ['kendaraan' => $kendaraanList[1], 'masuk' => $now->copy()->subDays(5)->setTime(9, 20, 0), 'durasi' => 2],
            ['kendaraan' => $kendaraanList[4], 'masuk' => $now->copy()->subDays(3)->setTime(11, 10, 0), 'durasi' => 3],
            ['kendaraan' => $kendaraanList[7], 'masuk' => $now->copy()->subDays(1)->setTime(10, 0, 0), 'durasi' => 2],
        ];

        foreach ($transaksiKeluarSeed as $row) {
            $kendaraan = $row['kendaraan'];
            $tarif = $tarifByJenis[$kendaraan->jenis_kendaraan];
            $area = $areaByJenis[$kendaraan->jenis_kendaraan];
            $waktuMasuk = $row['masuk'];
            $durasiJam = $row['durasi'];
            $waktuKeluar = $waktuMasuk->copy()->addMinutes(($durasiJam * 60) - 10);

            Transaksi::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk' => $waktuMasuk,
                'waktu_keluar' => $waktuKeluar,
                'id_tarif' => $tarif->id_tarif,
                'durasi_jam' => $durasiJam,
                'biaya_total' => $durasiJam * $tarif->tarif_per_jam,
                'status' => 'keluar',
                'id_user' => $petugas->id_user,
                'id_area' => $area->id_area,
            ]);
        }

        // Transaksi keluar ke-15 (hari ini)
        Transaksi::create([
            'id_kendaraan' => $kendaraanList[5]->id_kendaraan,
            'waktu_masuk' => $todayCheckoutMasuk,
            'waktu_keluar' => $todayCheckoutKeluar,
            'id_tarif' => $tarifMobil->id_tarif,
            'durasi_jam' => $todayCheckoutDurasi,
            'biaya_total' => $todayCheckoutDurasi * $tarifMobil->tarif_per_jam,
            'status' => 'keluar',
            'id_user' => $petugas->id_user,
            'id_area' => $areaMobil->id_area,
        ]);

        // 3 Transaksi aktif (status = 'masuk') hari ini
        $transaksiMasukSeed = [
            [
                'kendaraan' => $kendaraanList[0], // B 1234 ABC (motor)
                'masuk' => $now->copy()->subMinutes(55)->lt($todayStart) ? $todayStart->copy()->addMinutes(1) : $now->copy()->subMinutes(55),
            ],
            [
                'kendaraan' => $kendaraanList[3], // B 2345 JKL (mobil)
                'masuk' => $now->copy()->subMinutes(35)->lt($todayStart) ? $todayStart->copy()->addMinutes(2) : $now->copy()->subMinutes(35),
            ],
            [
                'kendaraan' => $kendaraanList[6], // B 7890 STU (lainnya)
                'masuk' => $now->copy()->subMinutes(15)->lt($todayStart) ? $todayStart->copy()->addMinutes(3) : $now->copy()->subMinutes(15),
            ],
        ];

        foreach ($transaksiMasukSeed as $row) {
            $kendaraan = $row['kendaraan'];
            $tarif = $tarifByJenis[$kendaraan->jenis_kendaraan];
            $area = $areaByJenis[$kendaraan->jenis_kendaraan];

            Transaksi::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk' => $row['masuk'],
                'waktu_keluar' => null,
                'id_tarif' => $tarif->id_tarif,
                'durasi_jam' => null,
                'biaya_total' => null,
                'status' => 'masuk',
                'id_user' => $petugas->id_user,
                'id_area' => $area->id_area,
            ]);
        }

        // Sinkronisasi kolom terisi pada setiap tb_area_parkir sesuai jumlah transaksi aktif
        foreach (AreaParkir::all() as $area) {
            $jumlahAktif = Transaksi::where('id_area', $area->id_area)
                ->where('status', 'masuk')
                ->count();

            $area->update(['terisi' => $jumlahAktif]);
        }

        // 6. 6 Data Log Aktivitas Awal
        $logs = [
            [
                'id_user' => $admin->id_user,
                'aktivitas' => 'Login ke sistem',
                'waktu_aktivitas' => $now->copy()->subHours(4),
            ],
            [
                'id_user' => $admin->id_user,
                'aktivitas' => 'Menambahkan tarif parkir motor, mobil, dan lainnya',
                'waktu_aktivitas' => $now->copy()->subHours(3)->subMinutes(45),
            ],
            [
                'id_user' => $admin->id_user,
                'aktivitas' => 'Menambahkan data area parkir awal',
                'waktu_aktivitas' => $now->copy()->subHours(3)->subMinutes(30),
            ],
            [
                'id_user' => $admin->id_user,
                'aktivitas' => 'Menambahkan data kendaraan terdaftar',
                'waktu_aktivitas' => $now->copy()->subHours(3)->subMinutes(15),
            ],
            [
                'id_user' => $petugas->id_user,
                'aktivitas' => 'Login ke sistem',
                'waktu_aktivitas' => $now->copy()->subHours(2),
            ],
            [
                'id_user' => $owner->id_user,
                'aktivitas' => 'Login ke sistem',
                'waktu_aktivitas' => $now->copy()->subHour(),
            ],
        ];

        foreach ($logs as $log) {
            LogAktivitas::create($log);
        }
    }
}
