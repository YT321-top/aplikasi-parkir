# PRD.md — Product Requirements Document Aplikasi Parkir

## 1. Ringkasan Produk
Aplikasi Parkir adalah sistem manajemen operasional parkir berbasis web (Laravel 10 + PostgreSQL) yang membagi hak akses secara ketat untuk **Admin**, **Petugas**, dan **Owner**, mencakup pengelolaan data master, transaksi keluar-masuk kendaraan beserta cetak struk, pencatatan log aktivitas otomatis, serta rekap transaksi berdasarkan rentang waktu.

## 2. Daftar Role Pengguna
Role disimpan pada kolom `tb_user.role` dengan 3 nilai eksklusif:
1. **Admin:** Bertanggung jawab mengelola seluruh data master (User, Tarif Parkir, Area Parkir, Kendaraan) dan memantau riwayat Log Aktivitas.
2. **Petugas:** Bertanggung jawab menjalankan operasional harian transaksi kendaraan masuk & keluar serta mencetak struk parkir.
3. **Owner:** Bertanggung jawab memantau ringkasan operasional dan mengakses rekap transaksi sesuai rentang waktu yang diminta.

## 3. Matriks Fitur & Hak Akses
| Fitur | Admin | Petugas | Owner |
| :--- | :---: | :---: | :---: |
| Login | V | V | V |
| Logout | V | V | V |
| CRUD User | V | - | - |
| CRUD Tarif Parkir | V | - | - |
| CRUD Area Parkir | V | - | - |
| CRUD Kendaraan | V | - | - |
| Akses Log Aktivitas | V | - | - |
| Transaksi | - | V | - |
| Cetak Struk Parkir | - | V | - |
| Rekap Transaksi Sesuai Waktu yang Diminta | - | - | V |

## 4. Navigasi Sidebar per Role
Menu pada sidebar kiri tampil dinamis hanya untuk fitur yang diizinkan oleh role yang sedang login:
- **Admin:** `Dashboard`, `User`, `Tarif Parkir`, `Area Parkir`, `Kendaraan`, `Log Aktivitas`
- **Petugas:** `Dashboard`, `Transaksi`, `Riwayat & Struk`
- **Owner:** `Dashboard`, `Rekap Transaksi`

## 5. Kriteria Keberhasilan & Portabilitas
- Setiap role tidak dapat mengakses URL milik role lain (mengembalikan HTTP `403` atau redirect yang aman).
- Akun dengan `status_aktif = 0` tidak dapat login.
- Kapasitas area parkir (`terisi`) selalu sinkron dengan jumlah kendaraan yang berstatus `masuk`.
- Proyek dapat di-clone ke laptop lain (Windows/macOS/Linux dengan PHP 8.1+ & PostgreSQL) dan langsung berjalan penuh hanya lewat `composer install`, `npm install && npm run build`, konfigurasi `.env`, dan `php artisan migrate --seed`.