# EXECUTION_STEPS.md — Panduan Langkah Eksekusi Lengkap

Ikuti urutan langkah di bawah ini dari awal sampai proyek siap di-review dan dikirim via GitHub ke laptop temanmu.

---

## Persiapan Awal (Sebelum Buka Antigravity)
1. **Siapkan Database Kosong di PostgreSQL:**
   - Buka pgAdmin atau DBeaver, buat database baru bernama `parkir`.
2. **Siapkan File `.env` Proyek Laravel Kamu:**
   - Pastikan proyek Laravel `10.50.3` (PHP `8.1.10`) sudah ada.
   - Atur koneksi PostgreSQL di file `.env`:
     ~~~env
     DB_CONNECTION=pgsql
     DB_HOST=127.0.0.1
     DB_PORT=5432
     DB_DATABASE=parkir
     DB_USERNAME=postgres
     DB_PASSWORD=password_postgres_kamu
     ~~~
   - Pastikan ekstensi `pdo_pgsql` dan `pgsql` aktif di `php.ini`.
3. **Simpan Semua File `.md` ke Root Proyek:**
   Taruh 6 file dokumentasi ini langsung di folder root proyek Laravel kamu:
   - `GEMINI.md`
   - `PRD.md`
   - `DESIGN.md`
   - `DATABASE.md`
   - `ARCHITECTURE.md`
   - `FEATURES.md`

---

## Eksekusi Bertahap di Antigravity IDE
Kirim prompt berikut **satu per satu**. Pastikan setiap tahap selesai dan bebas error sebelum lanjut ke tahap berikutnya.

### TAHAP 1 — Paket, Database PostgreSQL & Seeder
~~~text
TAHAP 1 - Paket, Database & Seeder:
1. Install `tailwindcss@^3`, `postcss`, `autoprefixer`, dan `chart.js` melalui npm lalu jalankan `npx tailwindcss init -p`.
2. Baca `GEMINI.md`, `ARCHITECTURE.md`, dan `DATABASE.md`.
3. Perbarui `config/app.php` agar timezone menjadi `Asia/Jakarta`.
4. Hapus atau nonaktifkan migration bawaan Laravel yang bentrok, lalu buat 6 file migrations PostgreSQL sesuai urutan dependensi tabel (`tb_user`, `tb_tarif`, `tb_area_parkir`, `tb_kendaraan`, `tb_transaksi`, `tb_log_aktivitas`).
5. Buat 6 Eloquent models (`User`, `Tarif`, `AreaParkir`, `Kendaraan`, `Transaksi`, `LogAktivitas`) lengkap dengan `$timestamps = false`, `$table`, `$primaryKey`, `$casts`, dan seluruh relasinya, serta pastikan `config/auth.php` mengarah ke `App\Models\User`.
6. Buat `DatabaseSeeder.php` berisi 3 akun default (`admin`, `petugas`, `owner` dengan password `password`), data tarif, area parkir, kendaraan, 18 data transaksi dummy (sinkron dengan kolom `terisi`), dan log aktivitas awal.
7. Jalankan `php artisan migrate:fresh --seed` dan pastikan tidak ada error PostgreSQL.
~~~

### TAHAP 2 — Autentikasi, RBAC, Log Service & Dashboard Monokrom
~~~text
TAHAP 2 - Autentikasi, RBAC, Log Service & Dashboard Monokrom:
Baca `GEMINI.md`, `DESIGN.md`, `PRD.md`, `ARCHITECTURE.md`, dan `FEATURES.md`.
1. Konfigurasikan Tailwind CSS (dengan font Plus Jakarta Sans dan palet monokrom `#F3F4F6`, `#FFFFFF`, `#0F0F10`) serta buat layout `resources/views/layouts/app.blade.php` dengan sidebar kiri putih tetap, pil menu aktif hitam dengan lingkaran ikon putih, profil user + tombol logout di bawah, dan menu dinamis sesuai role.
2. Buat halaman `/login` dalam satu kartu putih `rounded-[28px]` di tengah layar `#F3F4F6` dengan tombol pil hitam `#0F0F10` (tanpa template Breeze & tanpa teks filler), serta `AuthController` untuk login menggunakan `username` + `password` dengan validasi `status_aktif == 1`.
3. Buat `RoleMiddleware`, daftarkan di `app/Http/Kernel.php` pada `$middlewareAliases` sebagai `'role'`, dan buat `App\Services\LogAktivitasService` untuk mencatat aktivitas login/logout dan aksi sistem ke `tb_log_aktivitas`.
4. Buat `DashboardController` dan view `/dashboard` mengikuti struktur 3 baris di `DESIGN.md` (Quick Stats dengan 1 card highlight ikon hitam, Grid Grafik Batang Chart.js + Card Status Area/Transaksi Terbaru, dan Tabel Data strip `#FAFAFA`) untuk role `admin`, `petugas`, dan `owner` tanpa AI slop atau teks tambahan yang tidak diperlukan.
~~~

### TAHAP 3 — Master Data CRUD & Log Aktivitas (Admin)
~~~text
TAHAP 3 - Master Data CRUD & Log Aktivitas (Admin):
Baca `DESIGN.md`, `ARCHITECTURE.md`, dan `FEATURES.md` bagian Modul Admin.
1. Buat Controller, Form Request Validation, Routes (`['auth', 'role:admin']`), dan Blade Views bergaya kartu putih `rounded-[24px]` dengan baris tabel strip `#FAFAFA` dan tombol pil hitam untuk:
   - CRUD User (`/users`) dengan proteksi tidak bisa hapus/nonaktifkan akun sendiri dan password opsional saat edit.
   - CRUD Tarif Parkir (`/tarif`) dengan format Rupiah dan proteksi hapus jika sudah dipakai transaksi.
   - CRUD Area Parkir (`/area-parkir`) dengan validasi `kapasitas >= terisi` dan proteksi hapus jika dipakai transaksi.
   - CRUD Kendaraan (`/kendaraan`) dengan auto-uppercase `plat_nomor` dan pencarian.
   - Akses Log Aktivitas (`/log-aktivitas`) read-only dengan filter user, filter tanggal, dan pagination.
2. Pastikan setiap aksi tambah, ubah, dan hapus otomatis mencatat log ke `tb_log_aktivitas` melalui `LogAktivitasService`.
~~~

### TAHAP 4 — Transaksi, Cetak Struk (Petugas) & Rekap Waktu (Owner)
~~~text
TAHAP 4 - Transaksi, Cetak Struk (Petugas) & Rekap Waktu (Owner):
Baca `DESIGN.md`, `ARCHITECTURE.md`, dan `FEATURES.md` bagian Modul Petugas dan Modul Owner.
1. Buat `Petugas\TransaksiController` dan Routes (`['auth', 'role:petugas']`):
   - `/transaksi` & `POST /transaksi/masuk`: pilih kendaraan terdaftar atau input cepat kendaraan baru, pilih area & tarif, validasi kendaraan tidak sedang berstatus `masuk` dan `terisi < kapasitas`, simpan transaksi + increment `terisi` di dalam `DB::transaction()` dengan `lockForUpdate()`.
   - `POST /transaksi/{transaksi}/keluar`: hitung `durasi_jam = max(1, (int) ceil($menit / 60))` dan `biaya_total = durasi_jam * tarif_per_jam`, update `status = 'keluar'`, decrement `terisi` secara aman di dalam `DB::transaction()`, lalu arahkan ke halaman struk.
   - `/riwayat` & `/transaksi/{transaksi}/struk`: daftar seluruh transaksi dengan filter status & pencarian plat nomor, serta halaman Cetak Struk Parkir siap cetak (`@media print` lebar 80mm).
   - Catat semua transaksi masuk dan keluar ke `tb_log_aktivitas`.
2. Buat `Owner\RekapController` dan Routes (`['auth', 'role:owner']`):
   - `/rekap`: filter rentang waktu (`tanggal_mulai` s/d `tanggal_selesai`) + filter opsional area/jenis kendaraan/status, kartu ringkasan total transaksi & total pendapatan, tabel rincian, tombol `Cetak Rekap` (`@media print`), dan tombol `Export CSV` (`/rekap/export-csv`) memakai native `streamDownload` UTF-8 BOM.
~~~

---

## Fase Review & Revisi Lokal (JANGAN Langsung TAHAP 5)
Setelah TAHAP 4 selesai, jalankan proyek secara lokal di dua terminal terpisah:
~~~bash
npm run dev
php artisan serve
~~~
Uji seluruh alur di browser `http://127.0.0.1:8000` dengan 3 akun:
- **Admin:** `admin` / `password` — cek dashboard, CRUD User/Tarif/Area/Kendaraan, dan Log Aktivitas.
- **Petugas:** `petugas` / `password` — cek kendaraan masuk (pastikan `terisi` naik), kendaraan keluar (pastikan biaya benar & `terisi` turun), dan preview cetak struk (`Ctrl+P`).
- **Owner:** `owner` / `password` — cek dashboard, filter tanggal rekap, cetak rekap, dan download CSV.
- **Keamanan Role:** Coba akses `/users` saat login sebagai `petugas` atau `/rekap` saat login sebagai `admin` (harus ditolak `403`).

Kalau ada tampilan atau perilaku yang kurang pas, minta Antigravity revisi di tahap ini sampai semuanya benar-benar rapi.

---

## TAHAP 5 — Finalisasi & Persiapan Clone GitHub (Setelah Revisi Beres)
Jalankan prompt ini hanya saat aplikasi sudah final dan siap dikirim ke laptop temanmu:
~~~text
TAHAP 5 - Finalisasi & Persiapan Clone:
1. Jalankan `npm run build` agar aset CSS/JS terkompilasi, periksa `php artisan route:list` untuk memastikan tidak ada syntax error, dan uji ulang `php artisan migrate:fresh --seed`.
2. Pastikan `.env.example` sudah dikonfigurasi untuk PostgreSQL (`DB_CONNECTION=pgsql`, `DB_PORT=5432`) dan `.gitignore` mengecualikan `/vendor`, `/node_modules`, `.env`, serta `/public/build`.
3. Buat file `README.md` singkat di root proyek berisi langkah instalasi setelah `git clone` di laptop lain (aktifkan ekstensi PHP `pdo_pgsql` & `pgsql`, `composer install`, `npm install && npm run build`, `cp .env.example .env`, `php artisan key:generate`, `php artisan migrate --seed`, `php artisan serve`, serta daftar 3 akun default).
4. JANGAN jalankan `git commit` atau `git push` otomatis.
~~~

---

## Langkah Push ke GitHub & Clone di Laptop Temanmu

### Di Laptop Kamu
~~~bash
git init
git add .
git commit -m "Aplikasi Parkir Laravel 10 PostgreSQL selesai"
git branch -M main
git remote add origin https://github.com/username-kamu/nama-repo.git
git push -u origin main
~~~

### Di Laptop Temanmu
1. Pastikan **PHP 8.1+ (ekstensi `pdo_pgsql` & `pgsql` aktif)**, **Composer**, **Node.js**, dan **PostgreSQL** sudah terpasang.
2. Buat database kosong `parkir` di pgAdmin/DBeaver.
3. Jalankan perintah berikut di terminal:
~~~bash
git clone https://github.com/username-kamu/nama-repo.git
cd nama-repo
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
~~~
4. Buka file `.env` lalu isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai PostgreSQL laptop temanmu.
5. Jalankan migrasi & server:
~~~bash
php artisan migrate --seed
php artisan serve
~~~