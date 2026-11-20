<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Parkirku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F4F6] text-[#111827] font-sans antialiased">
    @php
        $authUser = auth()->user();
        $role = $authUser?->role ?? '';
        $roleLabel = match ($role) {
            'admin' => 'Admin',
            'petugas' => 'Petugas',
            'owner' => 'Owner',
            default => ucfirst($role),
        };

        $nameParts = preg_split('/\s+/', trim($authUser?->nama_lengkap ?? 'U'));
        $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

        $navItems = [
            [
                'label' => 'Dashboard',
                'url' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
                'icon' => 'home',
            ],
        ];

        if ($role === 'admin') {
            $navItems = array_merge($navItems, [
                [
                    'label' => 'User',
                    'url' => url('/users'),
                    'active' => request()->is('users*'),
                    'icon' => 'users',
                ],
                [
                    'label' => 'Tarif Parkir',
                    'url' => url('/tarif'),
                    'active' => request()->is('tarif*'),
                    'icon' => 'tag',
                ],
                [
                    'label' => 'Area Parkir',
                    'url' => url('/area-parkir'),
                    'active' => request()->is('area-parkir*'),
                    'icon' => 'map',
                ],
                [
                    'label' => 'Kendaraan',
                    'url' => url('/kendaraan'),
                    'active' => request()->is('kendaraan*'),
                    'icon' => 'car',
                ],
                [
                    'label' => 'Log Aktivitas',
                    'url' => url('/log-aktivitas'),
                    'active' => request()->is('log-aktivitas*'),
                    'icon' => 'clock',
                ],
            ]);
        } elseif ($role === 'petugas') {
            $navItems = array_merge($navItems, [
                [
                    'label' => 'Transaksi',
                    'url' => url('/transaksi'),
                    'active' => request()->is('transaksi*'),
                    'icon' => 'switch',
                ],
                [
                    'label' => 'Riwayat & Struk',
                    'url' => url('/riwayat'),
                    'active' => request()->is('riwayat*'),
                    'icon' => 'receipt',
                ],
            ]);
        } elseif ($role === 'owner') {
            $navItems = array_merge($navItems, [
                [
                    'label' => 'Rekap Transaksi',
                    'url' => url('/rekap'),
                    'active' => request()->is('rekap*'),
                    'icon' => 'chart',
                ],
            ]);
        }
    @endphp

    <div class="min-h-screen flex flex-col lg:flex-row">
        {{-- Mobile Top Bar --}}
        <header class="no-print lg:hidden sticky top-0 z-30 bg-white border-b border-[#E5E7EB] px-4 py-3 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <svg class="w-7 h-7 text-[#0F0F10]" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 10L14 5L23 10L14 15L5 10Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M5 15.5L14 20.5L23 15.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.5 20.5L14 23.5L19.5 20.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-base font-bold tracking-tight text-[#111827]">Parkirku</span>
            </a>
            <button type="button" id="mobileMenuOpen" aria-label="Buka menu"
                    class="w-9 h-9 rounded-full bg-[#F3F4F6] text-[#111827] flex items-center justify-center hover:bg-[#E5E7EB] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
        </header>

        {{-- Mobile Backdrop --}}
        <div id="sidebarBackdrop" class="no-print fixed inset-0 z-40 bg-black/25 hidden lg:hidden"></div>

        {{-- Fixed Left Sidebar --}}
        <aside id="appSidebar"
               class="no-print fixed inset-y-0 left-0 z-50 w-[240px] bg-white min-h-screen flex flex-col justify-between p-5 border-r border-[#F3F4F6] transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
            <div>
                {{-- Brand --}}
                <div class="flex items-center justify-between px-2 pt-1 pb-7">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <svg class="w-7 h-7 text-[#0F0F10]" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 10L14 5L23 10L14 15L5 10Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                            <path d="M5 15.5L14 20.5L23 15.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.5 20.5L14 23.5L19.5 20.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-lg font-bold tracking-tight text-[#111827]">Parkirku</span>
                    </a>
                    <button type="button" id="mobileMenuClose" aria-label="Tutup menu"
                            class="lg:hidden w-8 h-8 rounded-full bg-[#F3F4F6] text-[#4B5563] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Navigation --}}
                <nav class="space-y-1.5" aria-label="Menu utama">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['url'] }}"
                           class="{{ $item['active']
                               ? 'bg-[#0F0F10] text-white rounded-full p-1.5 pr-4 flex items-center gap-3 shadow-pill'
                               : 'text-[#6B7280] hover:text-[#111827] hover:bg-[#F9FAFB] rounded-full p-1.5 pr-4 flex items-center gap-3 transition-colors' }}">
                            <span class="{{ $item['active']
                                ? 'w-8 h-8 rounded-full bg-white text-[#0F0F10] flex items-center justify-center shrink-0'
                                : 'w-8 h-8 rounded-full bg-[#F3F4F6] text-[#4B5563] flex items-center justify-center shrink-0' }}">
                                @switch($item['icon'])
                                    @case('home')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6.5h-5V21H4a1 1 0 01-1-1v-9.5z"/>
                                        </svg>
                                        @break
                                    @case('users')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1"/>
                                            <circle cx="9.5" cy="7.5" r="3.2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 19v-1a3.5 3.5 0 00-2.6-3.4M16.5 4.4a3.2 3.2 0 010 6.2"/>
                                        </svg>
                                        @break
                                    @case('tag')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 11.2V5a2 2 0 012-2h6.2a2 2 0 011.41.59l7.8 7.8a2 2 0 010 2.82l-6.2 6.2a2 2 0 01-2.82 0l-7.8-7.8A2 2 0 013 11.2z"/>
                                        </svg>
                                        @break
                                    @case('map')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.3-6-10a6 6 0 1112 0c0 4.7-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2.2"/>
                                        </svg>
                                        @break
                                    @case('car')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-5A2 2 0 018.4 6.5h7.2A2 2 0 0117.5 8L19 13m-14 0h14m-14 0v4.5a1 1 0 001 1h1.5a1 1 0 001-1V16h7v1.5a1 1 0 001 1H18a1 1 0 001-1V13"/>
                                            <circle cx="7.5" cy="14" r="0.8" fill="currentColor"/>
                                            <circle cx="16.5" cy="14" r="0.8" fill="currentColor"/>
                                        </svg>
                                        @break
                                    @case('clock')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="8.5"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v4.8l3.2 1.9"/>
                                        </svg>
                                        @break
                                    @case('switch')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h12m0 0l-3.5-3.5M19 7l-3.5 3.5M17 17H5m0 0l3.5-3.5M5 17l3.5 3.5"/>
                                        </svg>
                                        @break
                                    @case('receipt')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8h6M9 12h6M9 16h4"/>
                                        </svg>
                                        @break
                                    @case('chart')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V10M12 16V6M17 16v-4"/>
                                        </svg>
                                        @break
                                @endswitch
                            </span>
                            <span class="text-[13px] font-medium truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- User Profile Footer --}}
            <div class="pt-6 mt-6 border-t border-[#F3F4F6] text-center">
                <div class="relative inline-flex items-center justify-center">
                    <div class="w-12 h-12 rounded-full bg-[#F3F4F6] text-[#111827] font-semibold text-sm flex items-center justify-center">
                        {{ $initials }}
                    </div>
                    <span class="absolute -top-1 -right-4 bg-[#0F0F10] text-white text-[10px] font-medium px-2 py-0.5 rounded-full leading-tight">
                        {{ $roleLabel }}
                    </span>
                </div>
                <p class="mt-2.5 text-sm font-semibold text-[#111827] truncate px-2">
                    {{ $authUser?->nama_lengkap }}
                </p>
                <p class="text-xs text-[#6B7280] truncate px-2">
                    {{ '@' . ($authUser?->username ?? '') }}
                </p>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="rounded-full border border-[#E5E7EB] text-xs font-medium text-[#111827] py-1.5 px-5 hover:bg-[#0F0F10] hover:text-white hover:border-[#0F0F10] transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Canvas --}}
        <main class="flex-1 lg:pl-[240px] min-h-screen canvas-glow">
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                @if (session('success'))
                    <div class="no-print mb-6 rounded-2xl bg-white border border-[#E5E7EB] px-4 py-3 text-xs font-medium text-[#111827] flex items-center justify-between shadow-card">
                        <span>{{ session('success') }}</span>
                        <span class="inline-flex items-center rounded-full bg-[#0F0F10] text-white px-2.5 py-0.5 text-[11px]">Berhasil</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="no-print mb-6 rounded-2xl bg-white border border-[#FECACA] px-4 py-3 text-xs font-medium text-[#DC2626] flex items-center justify-between shadow-card">
                        <span>{{ session('error') }}</span>
                        <span class="inline-flex items-center rounded-full bg-[#FEF2F2] text-[#DC2626] px-2.5 py-0.5 text-[11px]">Gagal</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const openBtn = document.getElementById('mobileMenuOpen');
            const closeBtn = document.getElementById('mobileMenuClose');

            function openSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }

            function closeSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        })();

        (function () {
            function initCustomDropdowns() {
                const selects = document.querySelectorAll('select:not([data-native]):not([data-dd-enhanced])');
                const instances = [];

                function closeAll(except) {
                    instances.forEach((inst) => {
                        if (inst === except) return;
                        inst.menu.classList.add('hidden');
                        inst.chevron.classList.remove('rotate-180');
                        inst.trigger.classList.remove('border-[#0F0F10]');
                        if (inst.card && (!except || except.card !== inst.card)) {
                            inst.card.classList.remove('z-30');
                        }
                    });
                }

                selects.forEach((select) => {
                    select.setAttribute('data-dd-enhanced', 'true');

                    const isFull = select.classList.contains('w-full');
                    const isSmall = select.classList.contains('text-xs');

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative ' + (isFull ? 'w-full' : 'inline-block min-w-[148px]');

                    select.parentNode.insertBefore(wrapper, select);
                    wrapper.appendChild(select);

                    select.tabIndex = -1;
                    select.classList.add('opacity-0', 'absolute', 'inset-0', 'w-full', 'h-full', 'pointer-events-none', '-z-10');

                    const trigger = document.createElement('button');
                    trigger.type = 'button';
                    trigger.className = [
                        'w-full flex items-center justify-between gap-2.5 rounded-xl border border-[#E5E7EB] bg-white text-left font-medium text-[#111827]',
                        'hover:border-[#D1D5DB] focus:border-[#0F0F10] focus:outline-none transition-colors',
                        isSmall ? 'px-3.5 py-2 text-xs' : 'px-4 py-2.5 text-sm',
                    ].join(' ');

                    const labelSpan = document.createElement('span');
                    labelSpan.className = 'truncate';

                    const chevron = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    chevron.setAttribute('class', 'w-3.5 h-3.5 text-[#6B7280] shrink-0 transition-transform duration-150');
                    chevron.setAttribute('fill', 'none');
                    chevron.setAttribute('stroke', 'currentColor');
                    chevron.setAttribute('stroke-width', '2');
                    chevron.setAttribute('viewBox', '0 0 24 24');
                    chevron.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>';

                    trigger.appendChild(labelSpan);
                    trigger.appendChild(chevron);

                    const menu = document.createElement('div');
                    menu.className = 'hidden absolute left-0 min-w-full w-max max-w-[320px] sm:max-w-[420px] mt-1.5 z-40 rounded-2xl bg-white border border-[#E5E7EB] p-1.5 shadow-[0_10px_28px_rgba(0,0,0,0.08)] max-h-60 overflow-y-auto';

                    const card = wrapper.closest('.pk-card');
                    const instance = { select, trigger, menu, chevron, card };
                    instances.push(instance);

                    function renderOptions() {
                        menu.innerHTML = '';
                        const selectedOpt = select.options[select.selectedIndex] || select.options[0];
                        labelSpan.textContent = selectedOpt ? selectedOpt.textContent.trim() : '';

                        Array.from(select.options).forEach((opt) => {
                            const isSelected = opt.value === select.value && opt.selected;
                            const isDisabled = opt.disabled;

                            const item = document.createElement('button');
                            item.type = 'button';
                            item.disabled = isDisabled;

                            if (isDisabled) {
                                item.className = 'w-full flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-xs font-normal text-[#9CA3AF] cursor-not-allowed';
                            } else if (isSelected) {
                                item.className = 'w-full flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-xs font-semibold bg-[#F3F4F6] text-[#111827]';
                            } else {
                                item.className = 'w-full flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-xs font-medium text-[#4B5563] hover:bg-[#FAFAFA] hover:text-[#111827] transition-colors';
                            }

                            const textSpan = document.createElement('span');
                            textSpan.className = 'truncate';
                            textSpan.textContent = opt.textContent.trim();
                            item.appendChild(textSpan);

                            if (isSelected && !isDisabled) {
                                const check = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                                check.setAttribute('class', 'w-3.5 h-3.5 text-[#0F0F10] shrink-0');
                                check.setAttribute('fill', 'none');
                                check.setAttribute('stroke', 'currentColor');
                                check.setAttribute('stroke-width', '2.2');
                                check.setAttribute('viewBox', '0 0 24 24');
                                check.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>';
                                item.appendChild(check);
                            }

                            if (!isDisabled) {
                                item.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    select.value = opt.value;
                                    select.dispatchEvent(new Event('change', { bubbles: true }));
                                    closeAll(null);
                                });
                            }

                            menu.appendChild(item);
                        });
                    }

                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = !menu.classList.contains('hidden');
                        closeAll(instance);
                        if (!isOpen) {
                            renderOptions();
                            menu.classList.remove('hidden');
                            chevron.classList.add('rotate-180');
                            trigger.classList.add('border-[#0F0F10]');
                            card?.classList.add('z-30');
                        } else {
                            menu.classList.add('hidden');
                            chevron.classList.remove('rotate-180');
                            trigger.classList.remove('border-[#0F0F10]');
                            card?.classList.remove('z-30');
                        }
                    });

                    select.addEventListener('change', renderOptions);

                    wrapper.appendChild(trigger);
                    wrapper.appendChild(menu);
                    renderOptions();
                });

                document.addEventListener('click', () => closeAll(null));
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeAll(null);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCustomDropdowns);
            } else {
                initCustomDropdowns();
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
