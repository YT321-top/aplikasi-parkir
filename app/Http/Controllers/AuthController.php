<?php

namespace App\Http\Controllers;

use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'in:admin,petugas,owner'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        $attempt = Auth::attempt([
            'username' => trim($credentials['username']),
            'password' => $credentials['password'],
        ]);

        if (!$attempt) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username', 'role');
        }

        $user = Auth::user();

        if ((int) $user->status_aktif !== 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['username' => 'Akun Anda sedang dinonaktifkan.'])
                ->onlyInput('username', 'role');
        }

        $selectedRole = $credentials['role'] ?? null;
        if ($selectedRole && $user->role !== $selectedRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'username' => 'Akun ini tidak memiliki akses sebagai ' . ucfirst($selectedRole) . '.',
                ])
                ->onlyInput('username', 'role');
        }

        $request->session()->regenerate();
        LogAktivitasService::record($user->id_user, 'Login ke sistem');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            LogAktivitasService::record($userId, 'Logout dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
