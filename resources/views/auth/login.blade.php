<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Parkirku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen canvas-glow flex items-center justify-center p-4 sm:p-6 font-sans antialiased text-[#111827]">
    @php
        $initialRole = in_array(old('role'), ['admin', 'petugas', 'owner'], true) ? old('role') : 'admin';
    @endphp

    <div class="pk-card max-w-[920px] w-full bg-white rounded-[28px] overflow-hidden shadow-card grid grid-cols-1 md:grid-cols-12 min-h-[480px]">
        {{-- Panel Kiri: Ilustrasi Mobil & Latar --}}
        <div class="relative md:col-span-7 bg-[#D9F1F4] overflow-hidden min-h-[220px] md:min-h-[480px]">
            <img
                src="{{ asset('images/login-hero.jpg') }}"
                alt="Ilustrasi Parkirku"
                class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 hover:scale-[1.02]"
            >

            <div class="relative z-10 flex items-center gap-2.5 px-6 pt-5">
                <svg class="w-7 h-7 text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.18)]" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 10L14 5L23 10L14 15L5 10Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M5 15.5L14 20.5L23 15.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.5 20.5L14 23.5L19.5 20.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-base font-bold tracking-wide text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.18)]">Parkirku</span>
            </div>
        </div>

        {{-- Panel Kanan: Role Switcher + Form Login --}}
        <div class="md:col-span-5 bg-white px-6 sm:px-8 py-7 flex flex-col justify-between">
            {{-- Sliding Pill Segmented Role Switcher (Dual-Layer Clip-Path) --}}
            <div class="relative grid grid-cols-3 rounded-full bg-[#F3F4F6] p-1 select-none" role="tablist" aria-label="Pilih Role">
                {{-- Bottom Layer: Inactive Text --}}
                <button type="button" data-role="admin" data-index="0"
                        class="role-tab relative z-10 rounded-full py-1.5 text-xs font-medium text-[#6B7280] hover:text-[#111827] focus:outline-none transition-colors">
                    Admin
                </button>
                <button type="button" data-role="petugas" data-index="1"
                        class="role-tab relative z-10 rounded-full py-1.5 text-xs font-medium text-[#6B7280] hover:text-[#111827] focus:outline-none transition-colors">
                    Petugas
                </button>
                <button type="button" data-role="owner" data-index="2"
                        class="role-tab relative z-10 rounded-full py-1.5 text-xs font-medium text-[#6B7280] hover:text-[#111827] focus:outline-none transition-colors">
                    Owner
                </button>

                {{-- Top Layer: Sliding Black Pill + White Text --}}
                <div id="pillOverlay" aria-hidden="true"
                     class="pointer-events-none absolute inset-1 z-20 grid grid-cols-3 rounded-full bg-[#0F0F10] text-white transition-[clip-path] duration-200 ease-out"
                     style="clip-path: inset(0 66.6667% 0 0 round 9999px);">
                    <span class="flex items-center justify-center py-1.5 text-xs font-semibold">Admin</span>
                    <span class="flex items-center justify-center py-1.5 text-xs font-semibold">Petugas</span>
                    <span class="flex items-center justify-center py-1.5 text-xs font-semibold">Owner</span>
                </div>
            </div>

            <div class="my-auto py-6">
                <div class="mb-5 flex items-center justify-between gap-2">
                    <div>
                        <h1 class="text-xl font-bold text-[#111827] tracking-tight">Masuk</h1>
                        <p id="roleSubtitle" class="text-xs text-[#6B7280] mt-0.5">Portal Admin</p>
                    </div>
                    <span id="roleBadge" class="rounded-full bg-[#F3F4F6] px-3 py-1 text-[11px] font-medium text-[#111827]">
                        Admin
                    </span>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] px-4 py-3 text-xs font-medium text-[#DC2626]">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="role" id="roleInput" value="{{ $initialRole }}">

                    <div class="group">
                        <label for="username" class="block text-[11px] font-medium text-[#6B7280] mb-1 transition-colors group-focus-within:text-[#0F0F10]">
                            Username
                        </label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin"
                            class="w-full border-b-2 border-[#E5E7EB] bg-transparent pb-2 text-sm font-medium text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none transition-colors"
                        >
                    </div>

                    <div class="group">
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-[11px] font-medium text-[#6B7280] transition-colors group-focus-within:text-[#0F0F10]">
                                Password
                            </label>
                            <span id="capsWarning" class="hidden text-[10px] font-medium text-[#DC2626]">
                                Caps Lock aktif
                            </span>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full border-b-2 border-[#E5E7EB] bg-transparent pb-2 pr-20 text-sm font-medium text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none transition-colors"
                            >
                            {{-- Toggle Password Pil Mikro (Opsi 3) --}}
                            <button type="button" id="togglePw" aria-label="Lihat atau sembunyikan password"
                                    class="absolute right-0 bottom-1.5 rounded-full bg-[#F3F4F6] text-[#111827] px-2.5 py-1 text-[11px] font-medium inline-flex items-center gap-1 transition-all duration-150 hover:bg-[#E5E7EB]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <span id="togglePwLabel">Lihat</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" id="submitBtn"
                                class="w-full rounded-full bg-[#0F0F10] text-white py-3 text-xs font-semibold hover:bg-[#262626] active:scale-[0.99] transition-all shadow-pill inline-flex items-center justify-center gap-2">
                            <svg id="btnSpinner" class="hidden w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span id="btnLabel">Masuk sebagai Admin</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center text-[11px] text-[#9CA3AF]">
                Sistem Manajemen Parkir
            </div>
        </div>
    </div>

    <script>
        (function () {
            const roleMeta = {
                admin: { label: 'Admin', placeholder: 'admin', clip: 'inset(0 66.6667% 0 0 round 9999px)' },
                petugas: { label: 'Petugas', placeholder: 'petugas', clip: 'inset(0 33.3333% 0 33.3333% round 9999px)' },
                owner: { label: 'Owner', placeholder: 'owner', clip: 'inset(0 0 0 66.6667% round 9999px)' },
            };

            const pillOverlay = document.getElementById('pillOverlay');
            const tabs = document.querySelectorAll('.role-tab');
            const roleInput = document.getElementById('roleInput');
            const subtitle = document.getElementById('roleSubtitle');
            const roleBadge = document.getElementById('roleBadge');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const togglePw = document.getElementById('togglePw');
            const togglePwLabel = document.getElementById('togglePwLabel');
            const capsWarning = document.getElementById('capsWarning');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnLabel = document.getElementById('btnLabel');

            function selectRole(role) {
                const meta = roleMeta[role] || roleMeta.admin;
                roleInput.value = role;
                pillOverlay.style.clipPath = meta.clip;

                tabs.forEach((t) => {
                    t.setAttribute('aria-selected', t.dataset.role === role ? 'true' : 'false');
                });

                subtitle.textContent = 'Portal ' + meta.label;
                roleBadge.textContent = meta.label;
                usernameInput.placeholder = meta.placeholder;
                btnLabel.textContent = 'Masuk sebagai ' + meta.label;
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => selectRole(tab.dataset.role));
            });

            selectRole(roleInput.value || 'admin');

            togglePw.addEventListener('click', () => {
                const show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                togglePwLabel.textContent = show ? 'Tutup' : 'Lihat';
                togglePw.classList.toggle('bg-[#0F0F10]', show);
                togglePw.classList.toggle('text-white', show);
                togglePw.classList.toggle('hover:bg-[#262626]', show);
                togglePw.classList.toggle('bg-[#F3F4F6]', !show);
                togglePw.classList.toggle('text-[#111827]', !show);
                togglePw.classList.toggle('hover:bg-[#E5E7EB]', !show);
            });

            ['keyup', 'keydown'].forEach((evt) => {
                passwordInput.addEventListener(evt, (e) => {
                    if (typeof e.getModifierState === 'function') {
                        capsWarning.classList.toggle('hidden', !e.getModifierState('CapsLock'));
                    }
                });
            });

            loginForm.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnLabel.textContent = 'Memproses...';
            });
        })();
    </script>
</body>
</html>
