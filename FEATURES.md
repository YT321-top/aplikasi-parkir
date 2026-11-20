# FEATURES.md — Spesifikasi Logika Bisnis per Modul

## 1. Dashboard Dinamis (`/dashboard`)
`DashboardController@index` merender data sesuai `Auth::user()->role` dengan struktur 3 baris dari `DESIGN.md`:

### Role Admin
- **Quick Stats (5 card):** Total User, Total Kendaraan, **Sedang Parkir** *(card highlight ikon hitam)*, Slot Tersedia, Transaksi Hari Ini.
- **Grafik Batang (Kiri):** Perbandingan jumlah transaksi `motor` vs `mobil` dalam 6 bulan terakhir.
- **List Ringkas (Kanan):** `Status Area Parkir` menampilkan `nama_area`, `terisi / kapasitas`, dan badge `Tersedia` atau `Penuh`.
- **Tabel Bawah:** `Log Aktivitas Terbaru` (10 baris terakhir + tombol `Lihat Semua` ke `/log-aktivitas`).

### Role Petugas
- **Quick Stats (4 card):** **Sedang Parkir** *(card highlight ikon hitam)*, Masuk Hari Ini, Keluar Hari Ini, Slot Tersedia.
- **Grafik Batang (Kiri):** Jumlah kendaraan Masuk vs Keluar selama 7 hari terakhir.
- **List Ringkas (Kanan):** `Status Area Parkir` secara real-time.
- **Tabel Bawah:** `Kendaraan Sedang Parkir` lengkap dengan tombol cepat `Proses Keluar`, `Struk`, dan tombol header `+ Kendaraan Masuk`.

### Role Owner
- **Quick Stats (4 card):** **Pendapatan Bulan Ini** *(card highlight ikon hitam)*, Pendapatan Hari Ini, Transaksi Selesai Bulan Ini, Kendaraan Aktif.
- **Grafik Batang (Kiri):** Ringkasan total transaksi & pendapatan 6 bulan terakhir.
- **List Ringkas (Kanan):** `Transaksi Selesai Terbaru` (5 transaksi terakhir dengan plat nomor, waktu keluar, dan badge nominal `Rp`).
- **Tabel Bawah:** `Ringkasan Transaksi Terbaru` dengan tombol header `Buka Rekap` dan `Export CSV`.

## 2. Modul Admin (`role:admin`)
Seluruh operasi tambah, ubah, dan hapus wajib mencatat log ke `tb_log_aktivitas`.

- **CRUD User (`/users`):**
  - Pencarian berdasarkan `nama_lengkap`/`username` dan filter `role`.
  - Form tambah/edit (`nama_lengkap`, `username`, `password`, `role`, `status_aktif`). Saat edit, `password` bersifat opsional (jika kosong, jangan ubah password lama).
  - Cegah admin menghapus atau menonaktifkan akunnya sendiri yang sedang login.
- **CRUD Tarif Parkir (`/tarif`):**
  - Tambah, edit, hapus `jenis_kendaraan` dan `tarif_per_jam` (tampilkan format `Rp 2.000`).
  - Cegah hapus tarif jika sudah dipakai pada `tb_transaksi` (tampilkan pesan error yang jelas).
- **CRUD Area Parkir (`/area-parkir`):**
  - Tambah, edit, hapus `nama_area` dan `kapasitas`; tampilkan kolom `terisi` dan sisa slot.
  - Saat edit, validasi `kapasitas >= terisi`.
  - Cegah hapus area jika sudah terhubung dengan data `tb_transaksi`.
- **CRUD Kendaraan (`/kendaraan`):**
  - Tambah, edit, hapus `plat_nomor` (otomatis di-trim & uppercase, unik), `jenis_kendaraan`, `warna`, `pemilik`. `id_user` diisi otomatis dari `Auth::id()`.
  - Pencarian cepat berdasarkan `plat_nomor` atau `pemilik`.
  - Cegah hapus kendaraan jika sudah memiliki riwayat di `tb_transaksi`.
- **Log Aktivitas (`/log-aktivitas`):**
  - Read-only dengan filter `user` dan rentang tanggal (`tanggal_mulai` – `tanggal_selesai`), diurutkan terbaru (`waktu_aktivitas desc`), dengan pagination.

## 3. Modul Petugas (`role:petugas`)
- **Halaman Transaksi (`/transaksi`):**
  - Menampilkan form **Kendaraan Masuk** dan tabel **Kendaraan Sedang Parkir** (`status = 'masuk'`).
  - Pada form Kendaraan Masuk, petugas dapat:
    1. Memilih kendaraan yang sudah terdaftar di `tb_kendaraan`, **ATAU**
    2. Menginput cepat kendaraan baru (`plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`) yang otomatis disimpan ke `tb_kendaraan` jika plat nomor belum terdaftar.
  - Memilih `id_area` (hanya area yang masih punya slot kosong) dan `id_tarif` (otomatis menyesuaikan `jenis_kendaraan` namun tetap bisa dipilih).
- **Logika Simpan Masuk (`POST /transaksi/masuk`):**
  Dijalankan di dalam `DB::transaction()`:
  1. Cek apakah `id_kendaraan` masih memiliki transaksi berstatus `masuk`. Jika ya, batalkan (`Kendaraan masih berstatus parkir.`).
  2. Lock baris area (`AreaParkir::lockForUpdate()->findOrFail($idArea)`). Jika `$area->terisi >= $area->kapasitas`, batalkan (`Area parkir sudah penuh.`).
  3. Insert ke `tb_transaksi` (`waktu_masuk = now()`, `status = 'masuk'`, `id_user = Auth::id()`).
  4. Increment `$area->terisi`.
  5. Catat log: `"Kendaraan masuk: {plat_nomor} ({nama_area})"`.
- **Logika Proses Keluar (`POST /transaksi/{transaksi}/keluar`):**
  Dijalankan di dalam `DB::transaction()`:
  1. Pastikan transaksi masih berstatus `masuk`.
  2. Set `$waktuKeluar = now()`.
  3. Hitung selisih menit: `$menit = $transaksi->waktu_masuk->diffInMinutes($waktuKeluar)`.
  4. Hitung durasi: `$durasiJam = max(1, (int) ceil($menit / 60))` (pembulatan ke atas per jam, minimal 1 jam).
  5. Hitung total biaya: `$biayaTotal = $durasiJam * $transaksi->tarif->tarif_per_jam`.
  6. Update `tb_transaksi` (`waktu_keluar`, `durasi_jam`, `biaya_total`, `status = 'keluar'`).
  7. Lock area terkait dan kurangi `terisi` dengan aman (`max(0, $area->terisi - 1)`).
  8. Catat log: `"Kendaraan keluar: {plat_nomor}, total Rp {biaya_total}"`.
  9. Redirect ke `/transaksi/{id_parkir}/struk` dengan flash message sukses.
- **Riwayat & Cetak Struk (`/riwayat` & `/transaksi/{transaksi}/struk`):**
  - `/riwayat` memuat seluruh transaksi dengan filter status (`masuk` / `keluar`) dan pencarian `plat_nomor`, lengkap dengan tombol `Cetak Struk`.
  - `/transaksi/{transaksi}/struk` dapat dicetak untuk bukti masuk maupun struk pembayaran keluar menggunakan `window.print()`.

## 4. Modul Owner (`role:owner`)
- **Rekap Transaksi (`/rekap`):**
  - Filter utama: `tanggal_mulai` dan `tanggal_selesai` (default: tanggal 1 bulan ini s/d hari ini).
  - Filter opsional: `id_area`, `jenis_kendaraan`, dan `status`.
  - Menampilkan ringkasan di atas tabel: Total Transaksi, Transaksi Selesai, Total Pendapatan (`SUM(biaya_total)`), dan Rata-rata Durasi (jam).
  - Tabel rincian transaksi sesuai rentang waktu yang diminta.
  - Tombol `Cetak Rekap` (`window.print()`) dan tombol `Export CSV` (`GET /rekap/export-csv`) yang membawa parameter filter aktif dan men-download file `.csv` (UTF-8 BOM agar kolom terbuka rapi di Microsoft Excel).