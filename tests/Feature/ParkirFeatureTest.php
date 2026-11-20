<?php

namespace Tests\Feature;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ParkirFeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_database_schema_matches_erd(): void
    {
        $expected = [
            'tb_user' => ['id_user', 'nama_lengkap', 'username', 'password', 'role', 'status_aktif'],
            'tb_tarif' => ['id_tarif', 'jenis_kendaraan', 'tarif_per_jam'],
            'tb_area_parkir' => ['id_area', 'nama_area', 'kapasitas', 'terisi'],
            'tb_kendaraan' => ['id_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'warna', 'pemilik', 'id_user'],
            'tb_transaksi' => ['id_parkir', 'id_kendaraan', 'waktu_masuk', 'waktu_keluar', 'id_tarif', 'durasi_jam', 'biaya_total', 'status', 'id_user', 'id_area'],
            'tb_log_aktivitas' => ['id_log', 'id_user', 'aktivitas', 'waktu_aktivitas'],
        ];

        foreach ($expected as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} tidak ditemukan.");
            foreach ($columns as $column) {
                $this->assertTrue(Schema::hasColumn($table, $column), "Kolom {$table}.{$column} tidak ditemukan.");
            }
        }
    }

    public function test_login_logout_and_inactive_user_blocked(): void
    {
        $this->get('/login')->assertStatus(200);

        $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        $inactive = User::create([
            'nama_lengkap' => 'User Nonaktif',
            'username' => 'nonaktif_test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'status_aktif' => 0,
        ]);

        $this->post('/login', [
            'username' => $inactive->username,
            'password' => 'password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_role_access_matrix_is_strictly_enforced(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $petugas = User::where('role', 'petugas')->firstOrFail();
        $owner = User::where('role', 'owner')->firstOrFail();

        // Admin
        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/users')->assertStatus(200);
        $this->actingAs($admin)->get('/tarif')->assertStatus(200);
        $this->actingAs($admin)->get('/area-parkir')->assertStatus(200);
        $this->actingAs($admin)->get('/kendaraan')->assertStatus(200);
        $this->actingAs($admin)->get('/log-aktivitas')->assertStatus(200);
        $this->actingAs($admin)->get('/transaksi')->assertStatus(403);
        $this->actingAs($admin)->get('/rekap')->assertStatus(403);

        // Petugas
        $this->actingAs($petugas)->get('/dashboard')->assertStatus(200);
        $this->actingAs($petugas)->get('/transaksi')->assertStatus(200);
        $this->actingAs($petugas)->get('/riwayat')->assertStatus(200);
        $this->actingAs($petugas)->get('/users')->assertStatus(403);
        $this->actingAs($petugas)->get('/tarif')->assertStatus(403);
        $this->actingAs($petugas)->get('/area-parkir')->assertStatus(403);
        $this->actingAs($petugas)->get('/kendaraan')->assertStatus(403);
        $this->actingAs($petugas)->get('/log-aktivitas')->assertStatus(403);
        $this->actingAs($petugas)->get('/rekap')->assertStatus(403);

        // Owner
        $this->actingAs($owner)->get('/dashboard')->assertStatus(200);
        $this->actingAs($owner)->get('/rekap')->assertStatus(200);
        $this->actingAs($owner)->get('/rekap/export-csv')->assertStatus(200);
        $this->actingAs($owner)->get('/users')->assertStatus(403);
        $this->actingAs($owner)->get('/transaksi')->assertStatus(403);
    }

    public function test_petugas_transaksi_masuk_keluar_struk_and_area_sync(): void
    {
        $petugas = User::where('role', 'petugas')->firstOrFail();
        $area = AreaParkir::whereColumn('terisi', '<', 'kapasitas')->firstOrFail();
        $tarif = Tarif::where('jenis_kendaraan', 'motor')->firstOrFail();
        $initialTerisi = (int) $area->terisi;

        $this->actingAs($petugas)->post('/transaksi/masuk', [
            'mode_kendaraan' => 'baru',
            'plat_nomor' => 'B 9999 TST',
            'jenis_kendaraan' => 'motor',
            'warna' => 'Hitam',
            'pemilik' => 'Tester Parkir',
            'id_area' => $area->id_area,
            'id_tarif' => $tarif->id_tarif,
        ])->assertRedirect('/transaksi');

        $area->refresh();
        $this->assertSame($initialTerisi + 1, (int) $area->terisi);

        $kendaraan = Kendaraan::where('plat_nomor', 'B 9999 TST')->firstOrFail();
        $transaksi = Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
            ->where('status', 'masuk')
            ->firstOrFail();

        // Cetak struk saat masuk
        $this->actingAs($petugas)->get("/transaksi/{$transaksi->id_parkir}/struk")->assertStatus(200);

        // Proses keluar
        $this->actingAs($petugas)
            ->post("/transaksi/{$transaksi->id_parkir}/keluar")
            ->assertRedirect("/transaksi/{$transaksi->id_parkir}/struk");

        $transaksi->refresh();
        $area->refresh();

        $this->assertSame('keluar', $transaksi->status);
        $this->assertGreaterThanOrEqual(1, (int) $transaksi->durasi_jam);
        $this->assertSame((int) $transaksi->durasi_jam * (int) $tarif->tarif_per_jam, (int) $transaksi->biaya_total);
        $this->assertSame($initialTerisi, (int) $area->terisi);
        $this->assertTrue(LogAktivitas::where('aktivitas', 'like', '%B 9999 TST%')->exists());
    }
}
