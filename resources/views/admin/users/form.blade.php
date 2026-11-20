@extends('layouts.app')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
    @php
        $currentRole = old('role', $user->role ?: 'petugas');
        $currentStatus = (string) old('status_aktif', $user->status_aktif ?? 1);
        $roleLabels = ['admin' => 'Admin', 'petugas' => 'Petugas', 'owner' => 'Owner'];
        $canDeactivate = !$user->exists || $user->id_user !== auth()->id();

        $initNama = old('nama_lengkap', $user->nama_lengkap ?? '');
        $parts = preg_split('/\s+/', trim($initNama ?: 'U'));
        $initInitials = strtoupper(substr($parts[0] ?? 'U', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
        $passCode = $user->exists
            ? 'PKR-USR-' . str_pad((string) $user->id_user, 3, '0', STR_PAD_LEFT)
            : 'PKR-USR-BARU';
    @endphp

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full bg-[#0F0F10] text-white text-xs font-bold flex items-center justify-center shrink-0">
                    P
                </span>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-[#111827] tracking-tight">
                            {{ $user->exists ? 'Edit User' : 'Tambah User' }}
                        </h1>
                        @if ($user->exists)
                            <span id="dirtyBadge" class="hidden rounded-full bg-[#0F0F10] text-white px-2.5 py-0.5 text-[11px] font-medium">
                                Perubahan belum disimpan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Pengaturan akun dan kartu akses operasional parkir
                    </p>
                </div>
            </div>

            <a href="{{ route('users.index') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Kembali
            </a>
        </div>

        {{-- Grid 2 Kolom --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            {{-- Kolom Kiri: Form Informasi Akun --}}
            <section class="pk-card lg:col-span-8 bg-white rounded-[24px] shadow-card flex flex-col z-20">
                <div class="pk-barrier-stripe w-full rounded-t-[24px] overflow-hidden"></div>

                <form method="POST"
                      action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}"
                      class="p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-5">
                    @csrf
                    @if ($user->exists)
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_lengkap" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Nama Lengkap
                                </label>
                                <input
                                    id="nama_lengkap"
                                    name="nama_lengkap"
                                    type="text"
                                    maxlength="50"
                                    value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                @error('nama_lengkap')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="username" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Username
                                </label>
                                <input
                                    id="username"
                                    name="username"
                                    type="text"
                                    maxlength="50"
                                    value="{{ old('username', $user->username) }}"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                @error('username')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label for="password" class="block text-xs font-medium text-[#6B7280]">
                                    Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}
                                </label>
                                @if ($user->exists)
                                    <span id="passHint" class="text-[11px] text-[#6B7280]">Kosongkan jika tetap</span>
                                @endif
                            </div>
                            <div class="relative flex items-center">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    @if (!$user->exists) required @endif
                                    placeholder="{{ $user->exists ? 'Masukkan password baru...' : 'Minimal 6 karakter' }}"
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 pr-24 text-sm text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                <button
                                    type="button"
                                    id="togglePasswordBtn"
                                    aria-label="Tampilkan atau sembunyikan password"
                                    class="absolute right-2 rounded-full bg-[#F3F4F6] hover:bg-[#0F0F10] text-[#111827] hover:text-white px-3 py-1.5 text-[11px] font-medium flex items-center gap-1.5 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6.5 9.5-6.5S21.5 12 21.5 12s-3.5 6.5-9.5 6.5S2.5 12 2.5 12z"/>
                                        <circle cx="12" cy="12" r="2.6"/>
                                        <path class="pk-eye-slash" stroke-linecap="round" d="M4 20L20 4"/>
                                    </svg>
                                    <span id="togglePasswordLabel">Lihat</span>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Custom Dropdown Role --}}
                            <div class="relative" data-pk-dropdown="role">
                                <label class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Role
                                </label>
                                <input type="hidden" name="role" id="roleInput" value="{{ $currentRole }}">
                                <button
                                    type="button"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    class="dd-trigger w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] flex items-center justify-between transition-colors"
                                >
                                    <span class="dd-label font-medium">{{ $roleLabels[$currentRole] ?? 'Petugas' }}</span>
                                    <svg class="dd-chevron w-4 h-4 text-[#6B7280] transition-transform duration-150" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                    </svg>
                                </button>
                                <div class="pk-dropdown-menu is-closed absolute left-0 right-0 z-40 mt-1.5 rounded-2xl bg-white border border-[#E5E7EB] p-1.5 shadow-[0_10px_28px_rgba(0,0,0,0.08)]" role="listbox">
                                    @foreach ($roleLabels as $val => $label)
                                        @php $selected = $currentRole === $val; @endphp
                                        <button
                                            type="button"
                                            data-value="{{ $val }}"
                                            class="dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors {{ $selected ? 'bg-[#F3F4F6] font-semibold text-[#111827]' : 'text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]' }}"
                                        >
                                            <span>{{ $label }}</span>
                                            <svg class="dd-check w-4 h-4 text-[#111827] {{ $selected ? '' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                                @error('role')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Custom Dropdown Status Akun --}}
                            <div class="relative" data-pk-dropdown="status_aktif">
                                <label class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Status Akun
                                </label>
                                <input type="hidden" name="status_aktif" id="statusAktifInput" value="{{ $currentStatus }}">
                                <button
                                    type="button"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    class="dd-trigger w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] flex items-center justify-between transition-colors"
                                >
                                    <span class="dd-label font-medium">{{ $currentStatus === '1' ? 'Aktif' : 'Nonaktif' }}</span>
                                    <svg class="dd-chevron w-4 h-4 text-[#6B7280] transition-transform duration-150" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                    </svg>
                                </button>
                                <div class="pk-dropdown-menu is-closed absolute left-0 right-0 z-40 mt-1.5 rounded-2xl bg-white border border-[#E5E7EB] p-1.5 shadow-[0_10px_28px_rgba(0,0,0,0.08)]" role="listbox">
                                    @php
                                        $statusOptions = $canDeactivate
                                            ? ['1' => 'Aktif', '0' => 'Nonaktif']
                                            : ['1' => 'Aktif'];
                                    @endphp
                                    @foreach ($statusOptions as $val => $label)
                                        @php $selected = $currentStatus === (string) $val; @endphp
                                        <button
                                            type="button"
                                            data-value="{{ $val }}"
                                            class="dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors {{ $selected ? 'bg-[#F3F4F6] font-semibold text-[#111827]' : 'text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]' }}"
                                        >
                                            <span>{{ $label }}</span>
                                            <svg class="dd-check w-4 h-4 text-[#111827] {{ $selected ? '' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                                @error('status_aktif')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-[#F3F4F6]">
                        <a href="{{ route('users.index') }}"
                           class="rounded-full border border-[#D1D5DB] px-5 py-2.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                                class="rounded-full bg-[#0F0F10] text-white px-6 py-2.5 text-xs font-semibold hover:bg-[#262626] transition-colors shadow-pill">
                            Simpan
                        </button>
                    </div>
                </form>
            </section>

            {{-- Kolom Kanan: Kartu Akses Parkir --}}
            <aside class="pk-card lg:col-span-4 bg-white rounded-[24px] shadow-card overflow-hidden flex flex-col justify-between">
                <div class="bg-[#0F0F10] text-white px-5 py-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 28 28" fill="none">
                            <path d="M5 10L14 5L23 10L14 15L5 10Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                            <path d="M5 15.5L14 20.5L23 15.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-xs font-bold tracking-wider uppercase">Kartu Akses</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-0.5 text-[11px] font-medium">
                        <span id="pvDot" class="w-1.5 h-1.5 rounded-full {{ $currentStatus === '1' ? 'bg-white' : 'bg-[#9CA3AF]' }}"></span>
                        <span id="pvStatusText">{{ $currentStatus === '1' ? 'Aktif' : 'Nonaktif' }}</span>
                    </span>
                </div>

                <div class="px-6 py-6 text-center flex-1 flex flex-col items-center justify-center">
                    <div id="pvAvatar" class="w-14 h-14 rounded-full bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] text-base font-bold flex items-center justify-center">
                        {{ $initInitials }}
                    </div>
                    <p id="pvNama" class="mt-3 text-base font-bold text-[#111827] truncate max-w-full">
                        {{ $initNama !== '' ? $initNama : 'Nama Lengkap' }}
                    </p>
                    <p id="pvUser" class="text-xs text-[#6B7280] mt-0.5">
                        {{ '@' . (old('username', $user->username) ?: 'username') }}
                    </p>

                    <span id="pvRoleBadge" class="mt-3 inline-block rounded-full bg-[#F3F4F6] px-3.5 py-1 text-xs font-semibold text-[#111827]">
                        {{ $roleLabels[$currentRole] ?? 'Petugas' }}
                    </span>
                    <p id="pvScope" class="mt-2.5 text-xs text-[#6B7280] leading-relaxed max-w-[240px]">
                        @if ($currentRole === 'admin')
                            Kelola data master dan pantau log aktivitas.
                        @elseif ($currentRole === 'owner')
                            Pantau ringkasan pendapatan dan rekap transaksi.
                        @else
                            Operasional transaksi masuk/keluar dan cetak struk.
                        @endif
                    </p>
                </div>

                <div class="relative border-t border-dashed border-[#E5E7EB] px-5 py-3.5 bg-[#FAFAFA]">
                    <span class="absolute -top-2.5 -left-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <span class="absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <div class="pk-barcode" aria-hidden="true">
                        <span class="w-[2px]"></span><span class="w-[1px]"></span><span class="w-[3px]"></span>
                        <span class="w-[1px]"></span><span class="w-[2px]"></span><span class="w-[4px]"></span>
                        <span class="w-[1px]"></span><span class="w-[2px]"></span><span class="w-[1px]"></span>
                        <span class="w-[3px]"></span><span class="w-[2px]"></span><span class="w-[1px]"></span>
                        <span class="w-[2px]"></span><span class="w-[3px]"></span><span class="w-[1px]"></span>
                        <span class="w-[2px]"></span><span class="w-[1px]"></span><span class="w-[2px]"></span>
                    </div>
                    <p class="mt-1.5 text-center font-mono text-[11px] tracking-widest text-[#6B7280]">{{ $passCode }}</p>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inNama = document.getElementById('nama_lengkap');
        const inUser = document.getElementById('username');
        const inPass = document.getElementById('password');
        const inRole = document.getElementById('roleInput');
        const inStatus = document.getElementById('statusAktifInput');

        const dirtyBadge = document.getElementById('dirtyBadge');
        const passHint = document.getElementById('passHint');
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const toggleLabel = document.getElementById('togglePasswordLabel');

        const pvAvatar = document.getElementById('pvAvatar');
        const pvNama = document.getElementById('pvNama');
        const pvUser = document.getElementById('pvUser');
        const pvRoleBadge = document.getElementById('pvRoleBadge');
        const pvScope = document.getElementById('pvScope');
        const pvStatusText = document.getElementById('pvStatusText');
        const pvDot = document.getElementById('pvDot');

        const initial = {
            nama: inNama.value,
            user: inUser.value,
            role: inRole.value,
            status: inStatus.value,
        };

        const scopes = {
            admin: 'Kelola data master dan pantau log aktivitas.',
            petugas: 'Operasional transaksi masuk/keluar dan cetak struk.',
            owner: 'Pantau ringkasan pendapatan dan rekap transaksi.',
        };
        const roleLabels = { admin: 'Admin', petugas: 'Petugas', owner: 'Owner' };

        function syncPreview() {
            const nama = inNama.value.trim() || 'Nama Lengkap';
            const parts = nama.split(/\s+/);
            pvAvatar.textContent = ((parts[0]?.[0] || '') + (parts[1]?.[0] || '')).toUpperCase() || 'U';
            pvNama.textContent = nama;
            pvUser.textContent = '@' + (inUser.value.trim() || 'username');
            pvRoleBadge.textContent = roleLabels[inRole.value] || 'Petugas';
            pvScope.textContent = scopes[inRole.value] || scopes.petugas;

            const active = inStatus.value === '1';
            pvStatusText.textContent = active ? 'Aktif' : 'Nonaktif';
            pvDot.className = active ? 'w-1.5 h-1.5 rounded-full bg-white' : 'w-1.5 h-1.5 rounded-full bg-[#9CA3AF]';

            if (dirtyBadge) {
                const changed =
                    inNama.value !== initial.nama ||
                    inUser.value !== initial.user ||
                    inRole.value !== initial.role ||
                    inStatus.value !== initial.status ||
                    inPass.value.length > 0;
                dirtyBadge.classList.toggle('hidden', !changed);
            }

            if (passHint) {
                passHint.textContent = inPass.value.length > 0 ? 'Password baru siap disimpan' : 'Kosongkan jika tetap';
            }
        }

        // Opsi 1: Pil Ikon + Label Password Toggle
        toggleBtn.addEventListener('click', () => {
            const show = inPass.type === 'password';
            inPass.type = show ? 'text' : 'password';
            toggleBtn.classList.toggle('is-visible', show);
            toggleLabel.textContent = show ? 'Tutup' : 'Lihat';
            toggleBtn.classList.toggle('bg-[#0F0F10]', show);
            toggleBtn.classList.toggle('text-white', show);
            toggleBtn.classList.toggle('bg-[#F3F4F6]', !show);
            toggleBtn.classList.toggle('text-[#111827]', !show);
        });

        // Custom Monochrome Dropdowns
        const dropdowns = document.querySelectorAll('[data-pk-dropdown]');
        function closeAllDropdowns() {
            dropdowns.forEach((dd) => {
                dd.querySelector('.pk-dropdown-menu').classList.add('is-closed');
                const trig = dd.querySelector('.dd-trigger');
                trig.setAttribute('aria-expanded', 'false');
                trig.classList.remove('border-[#0F0F10]');
                trig.classList.add('border-[#E5E7EB]');
                dd.querySelector('.dd-chevron').classList.remove('rotate-180');
            });
        }

        dropdowns.forEach((dd) => {
            const trigger = dd.querySelector('.dd-trigger');
            const menu = dd.querySelector('.pk-dropdown-menu');
            const chevron = dd.querySelector('.dd-chevron');
            const hiddenInput = dd.querySelector('input[type="hidden"]');
            const labelEl = dd.querySelector('.dd-label');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isClosed = menu.classList.contains('is-closed');
                closeAllDropdowns();
                if (isClosed) {
                    menu.classList.remove('is-closed');
                    trigger.setAttribute('aria-expanded', 'true');
                    trigger.classList.remove('border-[#E5E7EB]');
                    trigger.classList.add('border-[#0F0F10]');
                    chevron.classList.add('rotate-180');
                }
            });

            dd.querySelectorAll('.dd-option').forEach((opt) => {
                opt.addEventListener('click', () => {
                    const val = opt.getAttribute('data-value');
                    hiddenInput.value = val;
                    dd.querySelectorAll('.dd-option').forEach((o) => {
                        const selected = o === opt;
                        o.className = selected
                            ? 'dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors bg-[#F3F4F6] font-semibold text-[#111827]'
                            : 'dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]';
                        o.querySelector('.dd-check').classList.toggle('hidden', !selected);
                    });
                    labelEl.textContent = opt.querySelector('span').textContent;
                    closeAllDropdowns();
                    syncPreview();
                });
            });
        });

        document.addEventListener('click', closeAllDropdowns);
        [inNama, inUser, inPass].forEach((el) => el.addEventListener('input', syncPreview));
    });
</script>
@endpush
