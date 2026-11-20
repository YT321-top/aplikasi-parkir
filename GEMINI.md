# GEMINI.md — Aturan Proyek Aplikasi Parkir

File ini adalah instruksi global wajib yang harus dipatuhi pada setiap sesi pengerjaan di Antigravity IDE.

## 1. Spesifikasi Lingkungan & Versi
- **Framework:** Laravel `10.50.3`
- **PHP:** `8.1.10`
- **Database:** PostgreSQL (`pgsql`, port default `5432`)
- **Timezone:** `Asia/Jakarta` (`config/app.php`)
- **Frontend:** Blade Template + Tailwind CSS v3 (`tailwindcss@^3`, Vite) + Chart.js
- **Autentikasi:** Custom session auth Laravel (`Auth::attempt`) terhadap tabel `tb_user` menggunakan `username` dan `password`. **Dilarang** menginstall Laravel Breeze, Jetstream, atau UI starter kit bawaan.

## 2. Aturan Teknis Wajib (Laravel 10 & PostgreSQL)
1. **Middleware Laravel 10:** Semua middleware kustom (termasuk `role`) **wajib** didaftarkan di `app/Http/Kernel.php` pada array `$middlewareAliases`. **Dilarang** menggunakan sintaks Laravel 11 di `bootstrap/app.php`.
2. **Skema Database Eksak:** Nama tabel, primary key, dan nama kolom **wajib** mengikuti `DATABASE.md` secara persis (`tb_user`, `tb_tarif`, `tb_area_parkir`, `tb_kendaraan`, `tb_transaksi`, `tb_log_aktivitas`).
3. **Tanpa Timestamps Default:** Seluruh 6 Model Eloquent **wajib** mendeklarasikan `public $timestamps = false;`, `protected $table`, dan `protected $primaryKey`.
4. **Kompatibilitas PostgreSQL:**
   - Gunakan tipe integer yang konsisten antara primary key (`increments`) dan foreign key (`unsignedInteger`).
   - Dilarang memakai sintaks khusus MySQL (`ENUM('')` kosong, `UNSIGNED` pada decimal, atau fungsi `DATE_FORMAT()`). Gunakan query standar PostgreSQL (`TO_CHAR`, `EXTRACT`) atau olah tanggal lewat Carbon.
5. **Konsistensi Transaksi:** Semua aksi transaksi masuk dan keluar yang mengubah `tb_transaksi` dan `tb_area_parkir.terisi` **wajib** dijalankan di dalam `DB::transaction()` dengan `lockForUpdate()`.
6. **Pencatatan Log Otomatis:** Setiap aksi `login`, `logout`, CRUD master data, dan transaksi parkir **wajib** dicatat ke `tb_log_aktivitas` melalui `App\Services\LogAktivitasService`.

## 3. Aturan Desain & Anti AI Slop
Baca `DESIGN.md` sebelum menulis kode Blade atau CSS apa pun.
- **Palet Monokrom Ketat:** Canvas `#F3F4F6`, Card & Sidebar `#FFFFFF`, Aksen utama hitam `#0F0F10`, teks sekunder `#6B7280`.
- **Larangan Keras:**
  - Dilarang memakai warna ungu, biru neon, gradien warna-warni, atau shadow tebal.
  - Dilarang memakai emoji di seluruh UI (menu, judul, tombol, badge, tabel, notifikasi).
  - Dilarang menambah paragraf sambutan, tagline promosi, subtitle panjang, atau widget fiktif yang datanya tidak berasal dari 6 tabel database.
- **Bahasa UI:** Bahasa Indonesia yang ringkas, langsung pada fungsi, dan bebas basa-basi.

## 4. Aturan Clone-Ready (GitHub)
- Seluruh struktur tabel dibuat via Laravel Migrations dan data awal via `DatabaseSeeder` agar proyek bisa langsung dijalankan di laptop lain hanya dengan `php artisan migrate --seed`.
- Jangan lakukan `git commit` atau `git push` otomatis. Biarkan pengguna meninjau proyek terlebih dahulu.

## 5. Daftar Rujukan File Proyek
- `PRD.md` — Tujuan produk, daftar role, dan matriks fitur.
- `DESIGN.md` — Spesifikasi visual monokrom sesuai referensi gambar dan format cetak struk.
- `DATABASE.md` — Skema 6 tabel PostgreSQL, urutan migration, model Eloquent, dan seeder.
- `ARCHITECTURE.md` — Struktur folder, routing, middleware, dan service layer.
- `FEATURES.md` — Spesifikasi logika bisnis detail untuk modul Admin, Petugas, dan Owner.
- `EXECUTION_STEPS.md` — Panduan langkah eksekusi dan prompt bertahap.