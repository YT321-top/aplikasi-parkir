# DESIGN.md — Sistem Desain UI/UX Monokrom (Sesuai Referensi)

Tampilan antarmuka wajib mengikuti gaya **Clean Monochrome Admin Dashboard** dari referensi visual: sidebar putih tetap di kiri, navigasi pil hitam dengan lingkaran ikon putih, canvas abu-abu lembut, card bersudut sangat membulat, grafik batang tipis monokrom, dan baris tabel berbentuk strip lembut.

## 1. Palet Warna (Monokrom Ketat)
| Elemen / Token | Kode Hex | Penggunaan |
| :--- | :--- | :--- |
| Canvas Utama | `#F3F4F6` | Latar area konten utama dan halaman `/login` (boleh diberi radial cahaya putih halus di sudut kiri atas) |
| Surface / Card / Sidebar | `#FFFFFF` | Sidebar kiri, card Quick Stats, card grafik, card list samping, card tabel, modal |
| Aksen Primer | `#0F0F10` | Pil menu aktif sidebar, 1 lingkaran ikon stat unggulan, tombol aksi utama, batang grafik seri 1, tooltip grafik |
| Teks Utama | `#111827` | Judul halaman, angka statistik, isi kolom tabel utama |
| Teks Sekunder | `#6B7280` | Label form, header tabel, sub-info waktu/kapasitas, teks menu non-aktif |
| Batang Grafik Seri 2 | `#D1D5DB` | Seri pembanding pada grafik batang Chart.js |
| Surface Strip & Badge | `#FAFAFA` / `#F3F4F6` | Baris tabel strip (`#FAFAFA`), lingkaran ikon non-aktif dan badge pil (`#F3F4F6`) |
| Border Halus | `#E5E7EB` / `#D1D5DB` | Input form, tombol filter pil outline, garis grid horizontal tipis |
| Destructive Minimal | `#DC2626` | Hanya teks/ikon hapus saat hover atau pesan error validasi form |

## 2. Tipografi & Bentuk Komponen
- **Font:** `Plus Jakarta Sans` (fallback `Inter, sans-serif`).
- **Border Radius:**
  - Card utama, kontainer grafik, kontainer tabel, card login: `rounded-[24px]` hingga `rounded-[28px]`.
  - Card Quick Stats: `rounded-[24px]`.
  - Strip baris tabel: `rounded-2xl`.
  - Pil menu aktif sidebar, tombol utama, tombol filter outline, badge status: `rounded-full`.
  - Input teks, select, date picker: `rounded-xl`.
- **Shadow:** Sangat halus (`shadow-[0_2px_14px_rgba(0,0,0,0.03)]`).

## 3. Struktur Layout Utama (`resources/views/layouts/app.blade.php`)
- **Sidebar Kiri (Fixed `w-[240px]`, `bg-white`, `min-h-screen`, `flex flex-col justify-between p-5`):**
  - **Atas:** Logo geometris monokrom (SVG) + nama aplikasi singkat (contoh: `Parkirku`).
  - **Tengah (Menu Vertikal Dinamis per Role):**
    - **Item Aktif:** Pil hitam penuh `bg-[#0F0F10] text-white rounded-full p-1.5 pr-4 flex items-center gap-3`, ikon berada dalam lingkaran putih `w-8 h-8 rounded-full bg-white text-[#0F0F10] flex items-center justify-center`.
    - **Item Non-Aktif:** `text-[#6B7280] hover:text-[#111827] p-1.5 pr-4 flex items-center gap-3`, ikon berada dalam lingkaran abu-abu `w-8 h-8 rounded-full bg-[#F3F4F6] text-[#4B5563]`.
    - Gunakan ikon SVG garis minimalis (Heroicons/Lucide), **tanpa emoji**.
  - **Bawah (Profil User):**
    - Avatar lingkaran inisial di tengah (`w-12 h-12 rounded-full bg-[#F3F4F6] text-[#111827] font-semibold`) dengan badge pil hitam kecil di pojok kanan atas bertuliskan role (`Admin` / `Petugas` / `Owner`).
    - `nama_lengkap` (`text-sm font-semibold text-[#111827]`) dan `@username` (`text-xs text-[#6B7280]`).
    - Tombol `Logout` monokrom (`rounded-full border border-[#E5E7EB] text-xs py-1.5 px-4 mt-3 hover:bg-[#0F0F10] hover:text-white`).
- **Responsif Mobile:** Pada layar kecil, sidebar dapat dibuka/tutup melalui tombol menu minimalis di bar atas.

## 4. Struktur Dashboard 3 Baris (Mengikuti Referensi)
Setiap halaman `/dashboard` untuk ketiga role wajib mengikuti komposisi 3 baris berikut tanpa teks basa-basi:

1. **Baris 1 — Quick Stats:**
   - Sisi kiri (di atas canvas `#F3F4F6`, bukan di dalam card): Judul singkat `Ringkasan` (`text-lg font-semibold text-[#111827]`) + sub-info 1 baris (`Periode hari ini` / `Periode bulan ini`, `text-xs text-[#6B7280]`).
   - Sisi kanan: Deretan 4–5 card vertikal putih (`bg-white rounded-[24px] py-5 px-4 text-center`). Isi card berurutan dari atas ke bawah: lingkaran ikon, angka tebal (`text-xl font-bold`), label singkat 2–3 kata (`text-xs text-[#6B7280]`).
   - **Sorotan:** Tepat 1 card metrik utama memakai lingkaran ikon hitam pekat (`bg-[#0F0F10] text-white`), sedangkan card lainnya memakai lingkaran abu-abu (`bg-[#F3F4F6] text-[#111827]`).
2. **Baris 2 — Grid 2 Kolom (`grid-cols-1 lg:grid-cols-12 gap-6`):**
   - **Kolom Kiri (`lg:col-span-8`):** Card `Statistik` (`bg-white rounded-[24px] p-6`). Header menampilkan judul di kiri dan legenda 2 titik (`#0F0F10` dan `#D1D5DB`) di kanan. Grafik memakai Chart.js batang vertikal tipis berujung bulat (`borderRadius: 999`, `barThickness: 8`, grid horizontal tipis, tooltip pil hitam `#0F0F10` teks putih).
   - **Kolom Kanan (`lg:col-span-4`):** Card daftar ringkas (`bg-white rounded-[24px] p-6`) berisi 3–5 item (Status Area Parkir atau Transaksi Terbaru). Tiap baris menampilkan lingkaran ikon di kiri, judul + sub-info di tengah, dan badge pil abu-abu (`rounded-full bg-[#F3F4F6] px-3 py-1 text-xs`) di kanan.
3. **Baris 3 — Tabel Data (`bg-white rounded-[24px] p-6`):**
   - Header bar: Judul tabel di kiri; filter pil outline (`border border-[#D1D5DB] rounded-full px-4 py-2 text-xs`) dan tombol aksi pil hitam (`bg-[#0F0F10] text-white rounded-full px-5 py-2 text-xs`) di kanan.
   - Tabel memakai `border-separate border-spacing-y-2`, header kolom `text-xs font-medium text-[#6B7280]`, tiap baris menggunakan `bg-[#FAFAFA] hover:bg-[#F3F4F6]` dengan sudut `rounded-l-2xl` dan `rounded-r-2xl`, tanpa garis vertikal.

## 5. Halaman Login (`/login`)
- Canvas penuh `#F3F4F6`, satu card putih terpusat (`max-w-[400px] w-full bg-white rounded-[28px] p-8`).
- Isi minimal: Logo + judul `Masuk`, input `Username`, input `Password`, blok error validasi jika gagal, tombol submit pil hitam penuh `Masuk`.
- Tanpa teks sambutan panjang, tanpa ilustrasi besar, dan tanpa tombol registrasi.

## 6. Format Cetak Struk Parkir (`/transaksi/{id}/struk`) & Cetak Rekap
- **Tampilan Layar:** Card struk putih bersih di tengah halaman dengan tombol pil hitam `Cetak Struk` (`window.print()`) dan tombol outline `Kembali`.
- **CSS `@media print` (Struk 80mm):** Sembunyikan sidebar, navbar, dan semua tombol aksi. Atur lebar area cetak `80mm`, font monospace bersih, garis pemisah putus-putus (`border-dashed`), menampilkan:
  - Judul `STRUK PARKIR`
  - No. Parkir (`#ID`), Plat Nomor, Jenis Kendaraan, Area Parkir
  - Waktu Masuk, Waktu Keluar, Durasi (Jam), Tarif/Jam
  - **Total Biaya** (tebal)
  - Nama Petugas & waktu cetak
- **CSS `@media print` (Rekap Owner):** Sembunyikan sidebar, form filter, dan tombol export; tampilkan judul periode rekap, ringkasan total, dan tabel rincian dengan rapi di kertas A4.