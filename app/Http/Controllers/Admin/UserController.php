<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = trim((string) $request->query('role', ''));

        $users = User::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_lengkap', 'ILIKE', "%{$search}%")
                        ->orWhere('username', 'ILIKE', "%{$search}%");
                });
            })
            ->when(in_array($role, ['admin', 'petugas', 'owner'], true), function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->orderBy('id_user')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['status_aktif' => 1, 'role' => 'petugas'])]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status_aktif' => (int) $validated['status_aktif'],
        ]);

        LogAktivitasService::record(
            Auth::id(),
            "Menambah user: {$user->username} ({$user->role})"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->id_user === Auth::id() && (int) $validated['status_aktif'] !== 1) {
            return back()
                ->withInput()
                ->withErrors(['status_aktif' => 'Tidak dapat menonaktifkan akun sendiri.']);
        }

        $data = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'status_aktif' => (int) $validated['status_aktif'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        LogAktivitasService::record(
            Auth::id(),
            "Mengubah user: {$user->username} ({$user->role})"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id_user === Auth::id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->kendaraan()->exists() || $user->transaksi()->exists()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki data kendaraan atau transaksi.');
        }

        $username = $user->username;
        $user->delete();

        LogAktivitasService::record(
            Auth::id(),
            "Menghapus user: {$username}"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil dihapus.');
    }
}
