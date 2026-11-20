@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Baris 1: Quick Stats --}}
        <section class="flex flex-col xl:flex-row xl:items-center gap-5">
            <div class="xl:w-[190px] shrink-0">
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Ringkasan</h1>
                <p class="text-xs text-[#6B7280] mt-1">{{ $periodLabel }}</p>
            </div>

            <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 {{ count($stats) === 5 ? 'xl:grid-cols-5' : 'xl:grid-cols-4' }} gap-4">
                @foreach ($stats as $stat)
                    <div class="pk-card pk-clickable relative bg-white rounded-[24px] py-5 px-4 text-center shadow-card flex flex-col items-center justify-center">
                        <div class="{{ $stat['highlight']
                            ? 'pk-icon w-11 h-11 rounded-full bg-[#0F0F10] text-white flex items-center justify-center'
                            : 'pk-icon pk-icon-muted w-11 h-11 rounded-full bg-[#F3F4F6] text-[#111827] flex items-center justify-center' }}">
                            @switch($stat['icon'])
                                @case('users')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1"/>
                                        <circle cx="9.5" cy="7.5" r="3.2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 19v-1a3.5 3.5 0 00-2.6-3.4M16.5 4.4a3.2 3.2 0 010 6.2"/>
                                    </svg>
                                    @break
                                @case('car')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-5A2 2 0 018.4 6.5h7.2A2 2 0 0117.5 8L19 13m-14 0h14m-14 0v4.5a1 1 0 001 1h1.5a1 1 0 001-1V16h7v1.5a1 1 0 001 1H18a1 1 0 001-1V13"/>
                                    </svg>
                                    @break
                                @case('parking')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="4" y="4" width="16" height="16" rx="4"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 16V8h3a2.5 2.5 0 010 5h-3"/>
                                    </svg>
                                    @break
                                @case('area')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.3-6-10a6 6 0 1112 0c0 4.7-6 10-6 10z"/>
                                        <circle cx="12" cy="11" r="2.2"/>
                                    </svg>
                                    @break
                                @case('clock')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="8.5"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v4.8l3.2 1.9"/>
                                    </svg>
                                    @break
                                @case('arrow-down')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-5-5m5 5l5-5"/>
                                    </svg>
                                    @break
                                @case('arrow-up')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-5 5m5-5l5 5"/>
                                    </svg>
                                    @break
                                @case('wallet')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <rect x="3" y="6" width="18" height="13" rx="2.5"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12.5h5"/>
                                        <circle cx="16.5" cy="12.5" r="0.8" fill="currentColor"/>
                                    </svg>
                                    @break
                                @case('cash')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <rect x="3" y="7" width="18" height="10" rx="2"/>
                                        <circle cx="12" cy="12" r="2.3"/>
                                    </svg>
                                    @break
                                @case('check')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @break
                            @endswitch
                        </div>

                        <p class="mt-3.5 text-lg sm:text-xl font-bold text-[#111827] tabular-nums tracking-tight">
                            {{ $stat['value'] }}
                        </p>
                        <p class="mt-1 text-xs text-[#6B7280] leading-snug">
                            {{ $stat['label'] }}
                        </p>

                        @if ($stat['highlight'])
                            <span class="pk-arrow-badge absolute -bottom-3 left-1/2 -translate-x-1/2 w-6 h-6 rounded-full bg-white shadow-card border border-[#F3F4F6] text-[#111827] flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Baris 2: Grid Grafik Batang + Daftar Ringkas --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Kolom Kiri: Statistik Chart.js --}}
            <div class="pk-card lg:col-span-8 bg-white rounded-[24px] p-6 shadow-card">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <h2 class="text-base font-bold text-[#111827]">{{ $chart['title'] }}</h2>
                    <div class="flex items-center gap-4 text-xs text-[#6B7280]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#0F0F10]"></span>
                            {{ $chart['series1']['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D1D5DB]"></span>
                            {{ $chart['series2']['label'] }}
                        </span>
                        @if (!empty($chart['series3']))
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#6B7280]"></span>
                                {{ $chart['series3']['label'] }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="relative h-[240px] w-full">
                    <canvas id="dashboardChart"></canvas>
                </div>
            </div>

            {{-- Kolom Kanan: Status Area Parkir (Admin/Petugas) atau Transaksi Selesai Terbaru (Owner) --}}
            <div class="pk-card lg:col-span-4 bg-white rounded-[24px] p-6 shadow-card flex flex-col">
                @if ($role === 'owner')
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-[#111827]">Transaksi Selesai Terbaru</h2>
                        <a href="{{ url('/rekap') }}"
                           class="w-7 h-7 rounded-full border border-[#E5E7EB] text-[#6B7280] hover:text-[#111827] hover:border-[#0F0F10] flex items-center justify-center transition-colors"
                           title="Buka Rekap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-2 flex-1">
                        @forelse ($recentCompleted as $trx)
                            <div class="pk-list-item rounded-2xl p-2 -mx-2 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-9 h-9 rounded-full bg-[#F3F4F6] text-[#111827] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-5A2 2 0 018.4 6.5h7.2A2 2 0 0117.5 8L19 13m-14 0h14m-14 0v4.5a1 1 0 001 1h1.5a1 1 0 001-1V16h7v1.5a1 1 0 001 1H18a1 1 0 001-1V13"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#111827] truncate">
                                            {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                        </p>
                                        <p class="text-xs text-[#6B7280] truncate">
                                            {{ $trx->waktu_keluar ? $trx->waktu_keluar->format('d M H:i') : '-' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-semibold text-[#111827] tabular-nums">
                                    Rp {{ number_format((int) $trx->biaya_total, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-[#6B7280] py-6 text-center">Belum ada transaksi selesai.</p>
                        @endforelse
                    </div>
                @else
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-[#111827]">Status Area Parkir</h2>
                        @if ($role === 'admin')
                            <a href="{{ url('/area-parkir') }}"
                               class="w-7 h-7 rounded-full border border-[#E5E7EB] text-[#6B7280] hover:text-[#111827] hover:border-[#0F0F10] flex items-center justify-center transition-colors"
                               title="Kelola Area Parkir">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="space-y-2 flex-1">
                        @forelse ($areas as $area)
                            @php
                                $penuh = $area->terisi >= $area->kapasitas;
                            @endphp
                            <div class="pk-list-item rounded-2xl p-2 -mx-2 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-9 h-9 rounded-full bg-[#F3F4F6] text-[#111827] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.3-6-10a6 6 0 1112 0c0 4.7-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2.2"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#111827] truncate">
                                            {{ $area->nama_area }}
                                        </p>
                                        <p class="text-xs text-[#6B7280] tabular-nums">
                                            {{ $area->terisi }} / {{ $area->kapasitas }} terisi
                                        </p>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $penuh ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#111827]' }}">
                                    {{ $penuh ? 'Penuh' : 'Tersedia' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-[#6B7280] py-6 text-center">Belum ada data area parkir.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </section>

        {{-- Baris 3: Tabel Data --}}
        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            @if ($role === 'admin')
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-base font-bold text-[#111827]">Log Aktivitas Terbaru</h2>
                    <a href="{{ url('/log-aktivitas') }}"
                       class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                        Lihat Semua
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-y-2 text-left">
                        <thead>
                            <tr class="text-xs font-medium text-[#6B7280]">
                                <th class="pb-2 pl-4 pr-3 font-medium">Pengguna</th>
                                <th class="pb-2 px-3 font-medium">Role</th>
                                <th class="pb-2 px-3 font-medium">Aktivitas</th>
                                <th class="pb-2 pl-3 pr-4 font-medium text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse ($logs as $log)
                                <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                    <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-medium text-[#111827] whitespace-nowrap">
                                        {{ $log->user?->nama_lengkap ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-medium text-[#4B5563]">
                                            {{ ucfirst($log->user?->role ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-[#111827]">
                                        {{ $log->aktivitas }}
                                    </td>
                                    <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right text-xs text-[#6B7280] whitespace-nowrap tabular-nums">
                                        {{ $log->waktu_aktivitas ? $log->waktu_aktivitas->format('d M Y, H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-xs text-[#6B7280]">
                                        Belum ada log aktivitas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif ($role === 'petugas')
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-base font-bold text-[#111827]">Kendaraan Sedang Parkir</h2>
                    <div class="flex items-center gap-2">
                        <a href="{{ url('/riwayat') }}"
                           class="rounded-full border border-[#D1D5DB] text-[#111827] px-4 py-2 text-xs font-medium hover:border-[#0F0F10] transition-colors">
                            Riwayat
                        </a>
                        <a href="{{ url('/transaksi') }}"
                           class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                            + Kendaraan Masuk
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-y-2 text-left">
                        <thead>
                            <tr class="text-xs font-medium text-[#6B7280]">
                                <th class="pb-2 pl-4 pr-3 font-medium">Plat Nomor</th>
                                <th class="pb-2 px-3 font-medium">Jenis</th>
                                <th class="pb-2 px-3 font-medium">Area Parkir</th>
                                <th class="pb-2 px-3 font-medium">Waktu Masuk</th>
                                <th class="pb-2 px-3 font-medium">Status</th>
                                <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse ($activeTransactions as $trx)
                                <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                    <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-semibold text-[#111827] whitespace-nowrap">
                                        {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-[#4B5563] whitespace-nowrap">
                                        {{ ucfirst($trx->kendaraan?->jenis_kendaraan ?? '-') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-[#111827] whitespace-nowrap">
                                        {{ $trx->area?->nama_area ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-xs text-[#6B7280] whitespace-nowrap tabular-nums">
                                        {{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-medium text-[#111827]">
                                            Masuk
                                        </span>
                                    </td>
                                    <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ url('/transaksi/' . $trx->id_parkir . '/struk') }}"
                                               class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                                Struk
                                            </a>
                                            @if (Route::has('transaksi.keluar'))
                                                <button
                                                    type="button"
                                                    data-checkout-trigger
                                                    data-action="{{ route('transaksi.keluar', $trx->id_parkir) }}"
                                                    data-id="{{ $trx->id_parkir }}"
                                                    data-plat="{{ $trx->kendaraan?->plat_nomor ?? '-' }}"
                                                    data-jenis="{{ $trx->kendaraan?->jenis_kendaraan ?? 'lainnya' }}"
                                                    data-area="{{ $trx->area?->nama_area ?? '-' }}"
                                                    data-tarif="{{ (int) ($trx->tarif?->tarif_per_jam ?? 0) }}"
                                                    data-masuk-label="{{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}"
                                                    data-masuk-iso="{{ $trx->waktu_masuk ? $trx->waktu_masuk->toIso8601String() : '' }}"
                                                    class="rounded-full bg-[#0F0F10] text-white px-3.5 py-1.5 text-xs font-medium hover:bg-[#262626] transition-colors"
                                                >
                                                    Proses Keluar
                                                </button>
                                            @else
                                                <a href="{{ url('/transaksi') }}"
                                                   class="rounded-full bg-[#0F0F10] text-white px-3.5 py-1.5 text-xs font-medium hover:bg-[#262626] transition-colors">
                                                    Proses Keluar
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-[#6B7280]">
                                        Tidak ada kendaraan yang sedang parkir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif ($role === 'owner')
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-base font-bold text-[#111827]">Ringkasan Transaksi Terbaru</h2>
                    <div class="flex items-center gap-2">
                        <a href="{{ url('/rekap') }}"
                           class="rounded-full border border-[#D1D5DB] text-[#111827] px-4 py-2 text-xs font-medium hover:border-[#0F0F10] transition-colors">
                            Buka Rekap
                        </a>
                        <a href="{{ url('/rekap/export-csv') }}"
                           class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-medium hover:bg-[#262626] transition-colors inline-flex items-center gap-1.5">
                            <span>Export CSV</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-y-2 text-left">
                        <thead>
                            <tr class="text-xs font-medium text-[#6B7280]">
                                <th class="pb-2 pl-4 pr-3 font-medium">Plat Nomor</th>
                                <th class="pb-2 px-3 font-medium">Area Parkir</th>
                                <th class="pb-2 px-3 font-medium">Waktu Masuk</th>
                                <th class="pb-2 px-3 font-medium">Status</th>
                                <th class="pb-2 pl-3 pr-4 font-medium text-right">Biaya Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse ($recentTransactions as $trx)
                                <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                    <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-semibold text-[#111827] whitespace-nowrap">
                                        {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-[#4B5563] whitespace-nowrap">
                                        {{ $trx->area?->nama_area ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-xs text-[#6B7280] whitespace-nowrap tabular-nums">
                                        {{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $trx->status === 'keluar' ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#111827]' }}">
                                            {{ $trx->status === 'keluar' ? 'Keluar' : 'Masuk' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right font-semibold text-[#111827] whitespace-nowrap tabular-nums">
                                        {{ $trx->biaya_total !== null ? 'Rp ' . number_format((int) $trx->biaya_total, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-[#6B7280]">
                                        Belum ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    @if ($role === 'petugas')
        @include('components.checkout-modal')
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('dashboardChart');
            if (!canvas || typeof window.Chart === 'undefined') {
                return;
            }

            const chartConfig = @json($chart);
            const dualAxis = Boolean(chartConfig.dualAxis);

            const formatRupiah = (val) => 'Rp ' + Number(val || 0).toLocaleString('id-ID');

            const datasets = [
                {
                    label: chartConfig.series1.label,
                    data: chartConfig.series1.data,
                    backgroundColor: '#0F0F10',
                    hoverBackgroundColor: '#0F0F10',
                    borderRadius: 999,
                    borderSkipped: false,
                    barThickness: 8,
                    yAxisID: 'y',
                },
                {
                    label: chartConfig.series2.label,
                    data: chartConfig.series2.data,
                    backgroundColor: '#D1D5DB',
                    hoverBackgroundColor: '#9CA3AF',
                    borderRadius: 999,
                    borderSkipped: false,
                    barThickness: 8,
                    yAxisID: dualAxis ? 'y1' : 'y',
                },
            ];

            if (chartConfig.series3) {
                datasets.push({
                    label: chartConfig.series3.label,
                    data: chartConfig.series3.data,
                    backgroundColor: '#6B7280',
                    hoverBackgroundColor: '#4B5563',
                    borderRadius: 999,
                    borderSkipped: false,
                    barThickness: 8,
                    yAxisID: 'y',
                });
            }

            const scales = {
                x: {
                    grid: {
                        display: false,
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6',
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        color: '#6B7280',
                        precision: 0,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                        },
                        callback: function (value) {
                            if (chartConfig.series1.unit === 'rupiah') {
                                if (value >= 1000000) return (value / 1000000) + 'Jt';
                                if (value >= 1000) return (value / 1000) + 'K';
                                return value;
                            }
                            return value;
                        },
                    },
                },
            };

            if (dualAxis) {
                scales.y1 = {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        display: false,
                        precision: 0,
                    },
                };
            }

            new window.Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartConfig.labels,
                    datasets: datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#0F0F10',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            padding: {
                                top: 10,
                                right: 14,
                                bottom: 10,
                                left: 14,
                            },
                            cornerRadius: 12,
                            caretSize: 5,
                            displayColors: false,
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: '600',
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                            },
                            callbacks: {
                                label: function (ctx) {
                                    const seriesList = [chartConfig.series1, chartConfig.series2, chartConfig.series3];
                                    const series = seriesList[ctx.datasetIndex] || chartConfig.series1;
                                    if (series.unit === 'rupiah') {
                                        return ctx.dataset.label + ': ' + formatRupiah(ctx.parsed.y);
                                    }
                                    return ctx.dataset.label + ': ' + ctx.parsed.y + ' ' + series.unit;
                                },
                            },
                        },
                    },
                    scales: scales,
                },
            });
        });
    </script>
@endpush
