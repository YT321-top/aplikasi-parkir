<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $idUser = $request->query('id_user');
        $tanggalMulai = trim((string) $request->query('tanggal_mulai', ''));
        $tanggalSelesai = trim((string) $request->query('tanggal_selesai', ''));

        $logs = LogAktivitas::with('user')
            ->when($idUser !== null && $idUser !== '', function ($q) use ($idUser) {
                $q->where('id_user', (int) $idUser);
            })
            ->when($tanggalMulai !== '', function ($q) use ($tanggalMulai) {
                $q->whereDate('waktu_aktivitas', '>=', $tanggalMulai);
            })
            ->when($tanggalSelesai !== '', function ($q) use ($tanggalSelesai) {
                $q->whereDate('waktu_aktivitas', '<=', $tanggalSelesai);
            })
            ->orderByDesc('waktu_aktivitas')
            ->orderByDesc('id_log')
            ->paginate(15)
            ->withQueryString();

        $users = User::orderBy('nama_lengkap')->get(['id_user', 'nama_lengkap', 'username']);

        return view('admin.log-aktivitas.index', compact(
            'logs',
            'users',
            'idUser',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
}
