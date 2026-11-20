@extends('layouts.app')

@section('title', 'Riwayat & Struk')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Riwayat &amp; Struk</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Daftar transaksi parkir dan cetak ulang struk</p>
            </div>
            <a href="{{ route('transaksi.index') }}"
               class="rounded-full bg-[#0F0F10] text-white px-5 py-2.5 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill">
                + Kendaraan Masuk
            </a>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <form method="GET" action="{{ route('transaksi.riwayat') }}" class="flex flex-wrap items-center gap-3 mb-5">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari plat nomor atau pemilik"
                    class="w-full sm:w-64 rounded-xl border border-[#E5E7EB] px-4 py-2 text-xs text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none"
                >
                <select
                    name="status"
                    class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                >
                    <option value="">Semua Status</option>
                    <option value="masuk" @selected($status === 'masuk')>Masuk</option>
                    <option value="keluar" @selected($status === 'keluar')>Keluar</option>
                </select>
                <button type="submit"
                        class="rounded-full bg-[#0F0F10] text-white px-4 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                    Filter
                </button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('transaksi.riwayat') }}"
                       class="rounded-full border border-[#D1D5DB] px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">No</th>
                            <th class="pb-2 px-3 font-medium">Plat Nomor</th>
                            <th class="pb-2 px-3 font-medium">Area Parkir</th>
                            <th class="pb-2 px-3 font-medium">Waktu Masuk</th>
                            <th class="pb-2 px-3 font-medium">Waktu Keluar</th>
                            <th class="pb-2 px-3 font-medium">Durasi</th>
                            <th class="pb-2 px-3 font-medium">Biaya Total</th>
                            <th class="pb-2 px-3 font-medium">Status</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($transaksis as $trx)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl text-xs font-medium text-[#6B7280] tabular-nums whitespace-nowrap">
                                    #{{ $trx->id_parkir }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-[#4B5563] whitespace-nowrap">
                                    {{ $trx->area?->nama_area ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-xs text-[#6B7280] tabular-nums whitespace-nowrap">
                                    {{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-xs text-[#6B7280] tabular-nums whitespace-nowrap">
                                    {{ $trx->waktu_keluar ? $trx->waktu_keluar->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-xs text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $trx->durasi_jam !== null ? $trx->durasi_jam . ' jam' : '-' }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $trx->biaya_total !== null ? 'Rp ' . number_format((int) $trx->biaya_total, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $trx->status === 'keluar' ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#111827]' }}">
                                        {{ $trx->status === 'keluar' ? 'Keluar' : 'Masuk' }}
                                    </span>
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                    <a href="{{ route('transaksi.struk', $trx) }}"
                                       class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                        Cetak Struk
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-xs text-[#6B7280]">
                                    Data transaksi tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transaksis->links('components.pagination') }}
        </section>
    </div>
@endsection
