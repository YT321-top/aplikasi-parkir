# ARCHITECTURE.md — Arsitektur Aplikasi Laravel 10 & Standar Clone-Ready

## 1. Struktur Direktori Aplikasi
~~~text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── Admin/
│   │   │   ├── UserController.php
│   │   │   ├── TarifController.php
│   │   │   ├── AreaParkirController.php
│   │   │   ├── KendaraanController.php
│   │   │   └── LogAktivitasController.php
│   │   ├── Petugas/
│   │   │   └── TransaksiController.php
│   │   └── Owner/
│   │       └── RekapController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   ├── Requests/
│   │   ├── StoreUserRequest.php
│   │   ├── UpdateUserRequest.php
│   │   ├── StoreTarifRequest.php
│   │   ├── StoreAreaParkirRequest.php
│   │   ├── StoreKendaraanRequest.php
│   │   └── StoreTransaksiMasukRequest.php
│   └── Kernel.php
├── Models/
│   ├── User.php
│   ├── Tarif.php
│   ├── AreaParkir.php
│   ├── Kendaraan.php
│   ├── Transaksi.php
│   └── LogAktivitas.php
└── Services/
    └── LogAktivitasService.php
~~~

## 2. Autentikasi & Role Middleware (Laravel 10)
- Pastikan `config/auth.php` memakai model `App\Models\User::class` pada provider `users`.
- Login menerima `username` dan `password`. Jika kredensial valid tetapi `status_aktif != 1`, segera `Auth::logout()` dan kembalikan pesan error `"Akun Anda sedang dinonaktifkan."`.
- `RoleMiddleware::handle(Request $request, Closure $next, ...$roles)` wajib mengecek:
  1. `Auth::check()`
  2. `Auth::user()->status_aktif == 1` (jika tidak aktif, logout dan redirect `/login`)
  3. `in_array(Auth::user()->role, $roles)` (jika tidak cocok, `abort(403, 'Akses ditolak.')`)
- Daftarkan di `app/Http/Kernel.php` pada `$middlewareAliases`:

~~~php
'role' => \App\Http\Middleware\RoleMiddleware::class,
~~~

## 3. Service Pencatat Log (`app/Services/LogAktivitasService.php`)
Sediakan helper statis untuk mencegah string melebihi batas `varchar(100)` di PostgreSQL:

~~~php
public static function record(?int $idUser, string $aktivitas): void
{
    if (!$idUser) {
        return;
    }

    LogAktivitas::create([
        'id_user' => $idUser,
        'aktivitas' => Str::limit(trim($aktivitas), 100, ''),
        'waktu_aktivitas' => now(),
    ]);
}
~~~

## 4. Peta Route (`routes/web.php`)
- **Guest (`middleware('guest')`):**
  - `GET /login` -> `AuthController@showLogin`
  - `POST /login` -> `AuthController@login`
- **Semua Role Login (`middleware('auth')`):**
  - `GET /` -> redirect ke `/dashboard`
  - `GET /dashboard` -> `DashboardController@index`
  - `POST /logout` -> `AuthController@logout`
- **Grup Admin (`middleware(['auth', 'role:admin'])`):**
  - `Route::resource('users', Admin\UserController::class)->except(['show'])`
  - `Route::resource('tarif', Admin\TarifController::class)->except(['show'])`
  - `Route::resource('area-parkir', Admin\AreaParkirController::class)->except(['show'])`
  - `Route::resource('kendaraan', Admin\KendaraanController::class)->except(['show'])`
  - `GET /log-aktivitas` -> `Admin\LogAktivitasController@index`
- **Grup Petugas (`middleware(['auth', 'role:petugas'])`):**
  - `GET /transaksi` -> `Petugas\TransaksiController@index`
  - `POST /transaksi/masuk` -> `Petugas\TransaksiController@storeMasuk`
  - `POST /transaksi/{transaksi}/keluar` -> `Petugas\TransaksiController@prosesKeluar`
  - `GET /riwayat` -> `Petugas\TransaksiController@riwayat`
  - `GET /transaksi/{transaksi}/struk` -> `Petugas\TransaksiController@struk`
- **Grup Owner (`middleware(['auth', 'role:owner'])`):**
  - `GET /rekap` -> `Owner\RekapController@index`
  - `GET /rekap/export-csv` -> `Owner\RekapController@exportCsv`

## 5. Standar Clone-Ready (GitHub)
- **`.env.example`** langsung diarahkan ke PostgreSQL:
  ~~~env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=parkir
  DB_USERNAME=postgres
  DB_PASSWORD=
  ~~~
- **`.gitignore`** wajib mengecualikan `/vendor`, `/node_modules`, `.env`, dan `/public/build`.
- Hindari dependensi berat yang butuh ekstensi PHP non-standar (export memakai native CSV UTF-8 BOM dan `@media print`).