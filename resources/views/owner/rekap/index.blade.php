@extends('layouts.app')

@section('title', 'Rekap Transaksi')

@section('content')
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                background: #FFFFFF !important;
            }

            main > div {
                padding: 0 !important;
                max-width: none !important;
            }

            .print-card {
                box-shadow: none !important;
                border: 1px solid #E5E7EB !important;
                border-radius: 8px !important;
            }
        }
    </style>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Rekap Transaksi</h1>
                <p class="text-xs text-[#6B7280] mt-0.5 tabular-nums">
                    Periode {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d M Y') }}
                    s/d {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d M Y') }}
                </p>
            </div>
            <div class="no-print flex items-center gap-2">
                <button type="button" onclick="window.print()"
                        class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                    Cetak Rekap
                </button>
                <a href="{{ route('rekap.export-csv', request()->query()) }}"
                   class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill inline-flex items-center gap-1.5">
                    <span>Export CSV</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Filter Rentang Waktu & Opsional --}}
        <section class="no-print pk-card bg-white rounded-[24px] p-6 shadow-card">
            <form method="GET" action="{{ route('rekap.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div>
                    <label for="tanggal_mulai" class="block text-[11px] font-medium text-[#6B7280] mb-1">
                        Tanggal Mulai
                    </label>
                    <input
                        id="tanggal_mulai"
                        type="date"
                        name="tanggal_mulai"
                        value="{{ $filters['tanggal_mulai'] }}"
                        required
                        class="w-full rounded-xl border border-[#E5E7EB] px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-[11px] font-medium text-[#6B7280] mb-1">
                        Tanggal Selesai
                    </label>
                    <input
                        id="tanggal_selesai"
                        type="date"
                        name="tanggal_selesai"
                        value="{{ $filters['tanggal_selesai'] }}"
                        required
                        class="w-full rounded-xl border border-[#E5E7EB] px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                </div>

                <div>
                    <label for="id_area" class="block text-[11px] font-medium text-[#6B7280] mb-1">
                        Area Parkir
                    </label>
                    <select
                        id="id_area"
                        name="id_area"
                        class="w-full rounded-xl border border-[#E5E7EB] bg-white px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                        <option value="">Semua Area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id_area }}" @selected((string) $filters['id_area'] === (string) $area->id_area)>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="jenis_kendaraan" class="block text-[11px] font-medium text-[#6B7280] mb-1">
                        Jenis Kendaraan
                    </label>
                    <select
                        id="jenis_kendaraan"
                        name="jenis_kendaraan"
                        class="w-full rounded-xl border border-[#E5E7EB] bg-white px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                        <option value="">Semua Jenis</option>
                        <option value="motor" @selected($filters['jenis_kendaraan'] === 'motor')>Motor</option>
                        <option value="mobil" @selected($filters['jenis_kendaraan'] === 'mobil')>Mobil</option>
                        <option value="lainnya" @selected($filters['jenis_kendaraan'] === 'lainnya')>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-[11px] font-medium text-[#6B7280] mb-1">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-[#E5E7EB] bg-white px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                        <option value="">Semua Status</option>
                        <option value="masuk" @selected($filters['status'] === 'masuk')>Masuk</option>
                        <option value="keluar" @selected($filters['status'] === 'keluar')>Keluar</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 rounded-full bg-[#0F0F10] text-white px-4 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                        Terapkan
                    </button>
                    <a href="{{ route('rekap.index') }}"
                       class="rounded-full border border-[#D1D5DB] px-3.5 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Ringkasan Rekap --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="print-card pk-card pk-clickable bg-white rounded-[24px] py-5 px-4 text-center shadow-card">
                <p class="text-xl font-bold text-[#111827] tabular-nums">
                    {{ number_format($totalTransaksi, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-[#6B7280]">Total Transaksi</p>
            </div>
            <div class="print-card pk-card pk-clickable bg-white rounded-[24px] py-5 px-4 text-center shadow-card">
                <p class="text-xl font-bold text-[#111827] tabular-nums">
                    {{ number_format($transaksiSelesai, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-[#6B7280]">Transaksi Selesai</p>
            </div>
            <div class="print-card pk-card pk-clickable bg-white rounded-[24px] py-5 px-4 text-center shadow-card border border-[#0F0F10]/10">
                <p class="text-xl font-bold text-[#111827] tabular-nums">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-[#6B7280]">Total Pendapatan</p>
            </div>
            <div class="print-card pk-card pk-clickable bg-white rounded-[24px] py-5 px-4 text-center shadow-card">
                <p class="text-xl font-bold text-[#111827] tabular-nums">
                    {{ number_format($rataDurasi, 1, ',', '.') }} jam
                </p>
                <p class="mt-1 text-xs text-[#6B7280]">Rata-rata Durasi</p>
            </div>
        </section>

        {{-- Tabel Rincian --}}
        <section class="print-card pk-card bg-white rounded-[24px] p-6 shadow-card">
            <h2 class="text-base font-bold text-[#111827] mb-4">Rincian Transaksi</h2>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">No</th>
                            <th class="pb-2 px-3 font-medium">Plat Nomor</th>
                            <th class="pb-2 px-3 font-medium">Jenis</th>
                            <th class="pb-2 px-3 font-medium">Area Parkir</th>
                            <th class="pb-2 px-3 font-medium">Masuk</th>
                            <th class="pb-2 px-3 font-medium">Keluar</th>
                            <th class="pb-2 px-3 font-medium">Durasi</th>
                            <th class="pb-2 px-3 font-medium">Status</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Biaya Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($transaksis as $trx)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3 pl-4 pr-3 rounded-l-2xl text-xs font-medium text-[#6B7280] tabular-nums whitespace-nowrap">
                                    #{{ $trx->id_parkir }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-xs text-[#4B5563] whitespace-nowrap">
                                    {{ ucfirst($trx->kendaraan?->jenis_kendaraan ?? '-') }}
                                </td>
                                <td class="py-3 px-3 text-xs text-[#111827] whitespace-nowrap">
                                    {{ $trx->area?->nama_area ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-xs text-[#6B7280] tabular-nums whitespace-nowrap">
                                    {{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-xs text-[#6B7280] tabular-nums whitespace-nowrap">
                                    {{ $trx->waktu_keluar ? $trx->waktu_keluar->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-xs text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $trx->durasi_jam !== null ? $trx->durasi_jam . ' jam' : '-' }}
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $trx->status === 'keluar' ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#111827]' }}">
                                        {{ $trx->status === 'keluar' ? 'Keluar' : 'Masuk' }}
                                    </span>
                                </td>
                                <td class="py-3 pl-3 pr-4 rounded-r-2xl text-right font-semibold text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $trx->biaya_total !== null ? 'Rp ' . number_format((int) $trx->biaya_total, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-xs text-[#6B7280]">
                                    Tidak ada transaksi pada rentang waktu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="no-print">
                {{ $transaksis->links('components.pagination') }}
            </div>
        </section>
    </div>
@endsection
