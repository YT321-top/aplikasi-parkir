# DATABASE.md — Skema Database PostgreSQL, Model & Seeder

Seluruh migration wajib dibuat mengikuti urutan dependensi berikut agar relasi Foreign Key di PostgreSQL berjalan tanpa error. Hapus atau nonaktifkan file migration bawaan Laravel (`users`, `password_reset_tokens`, `failed_jobs`, `personal_access_tokens`) jika tidak digunakan agar tabel database bersih sesuai ERD.

## 1. Urutan Migration & Definisi Kolom

### Tabel 1: `tb_user`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_user` | `$table->increments('id_user');` | Primary Key |
| `nama_lengkap` | `$table->string('nama_lengkap', 50);` | Wajib |
| `username` | `$table->string('username', 50)->unique();` | Kredensial login |
| `password` | `$table->string('password', 100);` | Hash bcrypt |
| `role` | `$table->enum('role', ['admin', 'petugas', 'owner']);` | Role pengguna |
| `status_aktif` | `$table->smallInteger('status_aktif')->default(1);` | `1` aktif, `0` nonaktif |

### Tabel 2: `tb_tarif`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_tarif` | `$table->increments('id_tarif');` | Primary Key |
| `jenis_kendaraan` | `$table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);` | Kategori tarif |
| `tarif_per_jam` | `$table->decimal('tarif_per_jam', 10, 0);` | Nominal per jam |

### Tabel 3: `tb_area_parkir`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_area` | `$table->increments('id_area');` | Primary Key |
| `nama_area` | `$table->string('nama_area', 50);` | Nama zona parkir |
| `kapasitas` | `$table->integer('kapasitas');` | Jumlah slot maksimal |
| `terisi` | `$table->integer('terisi')->default(0);` | Slot sedang digunakan |

### Tabel 4: `tb_kendaraan`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_kendaraan` | `$table->increments('id_kendaraan');` | Primary Key |
| `plat_nomor` | `$table->string('plat_nomor', 15)->unique();` | Disimpan huruf kapital |
| `jenis_kendaraan` | `$table->string('jenis_kendaraan', 20);` | `motor`, `mobil`, `lainnya` |
| `warna` | `$table->string('warna', 20);` | Warna kendaraan |
| `pemilik` | `$table->string('pemilik', 100);` | Nama pemilik |
| `id_user` | `$table->unsignedInteger('id_user');` | FK -> `tb_user.id_user` (`onDelete('restrict')`) |

### Tabel 5: `tb_transaksi`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_parkir` | `$table->increments('id_parkir');` | Primary Key |
| `id_kendaraan` | `$table->unsignedInteger('id_kendaraan');` | FK -> `tb_kendaraan.id_kendaraan` (`onDelete('restrict')`) |
| `waktu_masuk` | `$table->timestamp('waktu_masuk');` | Waktu check-in |
| `waktu_keluar` | `$table->timestamp('waktu_keluar')->nullable();` | Diisi saat check-out |
| `id_tarif` | `$table->unsignedInteger('id_tarif');` | FK -> `tb_tarif.id_tarif` (`onDelete('restrict')`) |
| `durasi_jam` | `$table->integer('durasi_jam')->nullable();` | Minimal 1 jam saat keluar |
| `biaya_total` | `$table->decimal('biaya_total', 10, 0)->nullable();` | `durasi_jam * tarif_per_jam` |
| `status` | `$table->enum('status', ['masuk', 'keluar'])->default('masuk');` | Status parkir |
| `id_user` | `$table->unsignedInteger('id_user');` | FK -> `tb_user.id_user` (`onDelete('restrict')`) |
| `id_area` | `$table->unsignedInteger('id_area');` | FK -> `tb_area_parkir.id_area` (`onDelete('restrict')`) |

### Tabel 6: `tb_log_aktivitas`
| Kolom | Migration Laravel (PostgreSQL) | Keterangan |
| :--- | :--- | :--- |
| `id_log` | `$table->increments('id_log');` | Primary Key |
| `id_user` | `$table->unsignedInteger('id_user');` | FK -> `tb_user.id_user` (`onDelete('cascade')`) |
| `aktivitas` | `$table->string('aktivitas', 100);` | Deskripsi singkat aksi |
| `waktu_aktivitas` | `$table->timestamp('waktu_aktivitas');` | Waktu aksi |

*(Catatan PostgreSQL: Nilai enum kosong `''` yang muncul di gambar ERD diabaikan agar validasi enum PostgreSQL tetap bersih dan ketat).*

## 2. Konvensi Model Eloquent
Semua model wajib mendeklarasikan properti berikut secara eksplisit:

~~~php
public $timestamps = false;
protected $table = 'nama_tabel';
protected $primaryKey = 'nama_pk';
~~~

### Daftar Model & Relasi
1. **`App\Models\User`** (`extends Authenticatable`, tabel `tb_user`, PK `id_user`):
   - `$hidden = ['password']`
   - `kendaraan()` -> `hasMany(Kendaraan::class, 'id_user', 'id_user')`
   - `transaksi()` -> `hasMany(Transaksi::class, 'id_user', 'id_user')`
   - `logAktivitas()` -> `hasMany(LogAktivitas::class, 'id_user', 'id_user')`
2. **`App\Models\Tarif`** (tabel `tb_tarif`, PK `id_tarif`):
   - `transaksi()` -> `hasMany(Transaksi::class, 'id_tarif', 'id_tarif')`
3. **`App\Models\AreaParkir`** (tabel `tb_area_parkir`, PK `id_area`):
   - `transaksi()` -> `hasMany(Transaksi::class, 'id_area', 'id_area')`
4. **`App\Models\Kendaraan`** (tabel `tb_kendaraan`, PK `id_kendaraan`):
   - `user()` -> `belongsTo(User::class, 'id_user', 'id_user')`
   - `transaksi()` -> `hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan')`
5. **`App\Models\Transaksi`** (tabel `tb_transaksi`, PK `id_parkir`):
   - `$casts = ['waktu_masuk' => 'datetime', 'waktu_keluar' => 'datetime']`
   - `kendaraan()` -> `belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan')`
   - `tarif()` -> `belongsTo(Tarif::class, 'id_tarif', 'id_tarif')`
   - `user()` -> `belongsTo(User::class, 'id_user', 'id_user')`
   - `area()` -> `belongsTo(AreaParkir::class, 'id_area', 'id_area')`
6. **`App\Models\LogAktivitas`** (tabel `tb_log_aktivitas`, PK `id_log`):
   - `$casts = ['waktu_aktivitas' => 'datetime']`
   - `user()` -> `belongsTo(User::class, 'id_user', 'id_user')`

## 3. Data Seeder (`DatabaseSeeder.php`)
Seeder wajib menghasilkan data awal yang konsisten agar grafik dan tabel langsung terisi setelah `php artisan migrate --seed`:
1. **3 Akun Default** (password semua akun: `password`, di-hash `Hash::make('password')`, `status_aktif = 1`):
   - `username: admin` | `nama_lengkap: Administrator` | `role: admin`
   - `username: petugas` | `nama_lengkap: Budi Santoso` | `role: petugas`
   - `username: owner` | `nama_lengkap: Siti Rahma` | `role: owner`
2. **3 Data Tarif:**
   - `motor`: `2000`
   - `mobil`: `5000`
   - `lainnya`: `10000`
3. **3 Data Area Parkir:**
   - `Area A - Motor` (kapasitas `50`)
   - `Area B - Mobil` (kapasitas `30`)
   - `Area C - Umum` (kapasitas `20`)
4. **8 Data Kendaraan:** Kombinasi plat nomor motor, mobil, dan lainnya.
5. **18 Data Transaksi Dummy:**
   - 15 transaksi `status = 'keluar'` tersebar dalam 6 bulan terakhir agar grafik bulanan terisi alami.
   - 3 transaksi `status = 'masuk'` pada hari ini, lalu sinkronkan nilai `terisi` pada setiap `tb_area_parkir` agar persis sama dengan jumlah transaksi aktif di masing-masing area.
6. **6 Data Log Aktivitas Awal:** Riwayat login dan input data awal.