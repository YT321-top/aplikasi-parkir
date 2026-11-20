# Parkirku — Aplikasi Manajemen Operasional Parkir

Aplikasi manajemen operasional parkir berbasis **Laravel 10**, **PostgreSQL**, **Blade**, **Tailwind CSS**, dan **Chart.js** dengan pembagian hak akses ketat untuk **Admin**, **Petugas**, dan **Owner**.

## Prasyarat Lingkungan
- PHP `8.1+` dengan ekstensi `pdo_pgsql` dan `pgsql` aktif di `php.ini`
- Composer
- Node.js & NPM
- PostgreSQL (port default `5432`)

## Langkah Instalasi Setelah `git clone`

1. **Clone repositori dan masuk ke direktori proyek:**
   ```bash
   git clone <url-repositori>
   cd parkir-app
   ```

2. **Pastikan ekstensi PostgreSQL PHP aktif:**
   Buka `php.ini`, lalu pastikan baris berikut tidak diberi tanda komentar (`;`):
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```

3. **Install dependensi PHP & JavaScript, lalu build aset:**
   ```bash
   composer install
   npm install && npm run build
   ```

4. **Salin file konfigurasi `.env` dan generate application key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Buat database PostgreSQL dan sesuaikan `.env`:**
   Buat database baru di PostgreSQL (misalnya `parkir`), lalu pastikan konfigurasi `.env` sesuai:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=parkir
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```

6. **Jalankan migrasi dan seeder data awal:**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan server lokal:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di `http://127.0.0.1:8000`.

## Akun Default (Seeder)

Semua akun default menggunakan password: `password`

| Role | Username | Nama Lengkap |
| :--- | :--- | :--- |
| **Admin** | `admin` | Administrator |
| **Petugas** | `petugas` | Budi Santoso |
| **Owner** | `owner` | Siti Rahma |
