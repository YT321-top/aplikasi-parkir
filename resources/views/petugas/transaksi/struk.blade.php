@extends('layouts.app')

@section('title', 'Struk Parkir #' . $transaksi->id_parkir)

@section('content')
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 4mm;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                background: #FFFFFF !important;
            }

            main > div {
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
            }

            #receiptCard {
                width: 72mm !important;
                max-width: 72mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
                font-size: 11px !important;
                color: #000000 !important;
            }
        }
    </style>

    <div class="max-w-md mx-auto space-y-5">
        <div class="no-print flex items-center justify-between gap-3">
            <a href="{{ route('transaksi.index') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-semibold hover:bg-[#262626] transition-colors shadow-pill">
                Cetak Struk
            </button>
        </div>

        <section id="receiptCard" class="pk-card bg-white rounded-[24px] p-7 shadow-card font-mono text-xs text-[#111827]">
            <div class="text-center pb-4 border-b border-dashed border-[#D1D5DB]">
                <p class="text-base font-bold tracking-wider">STRUK PARKIR</p>
                <p class="text-[11px] text-[#6B7280] mt-0.5">Parkirku</p>
            </div>

            <div class="py-4 space-y-2 border-b border-dashed border-[#D1D5DB]">
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">No. Parkir</span>
                    <span class="font-semibold">#{{ $transaksi->id_parkir }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Plat Nomor</span>
                    <span class="font-bold">{{ $transaksi->kendaraan?->plat_nomor ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Jenis</span>
                    <span>{{ ucfirst($transaksi->kendaraan?->jenis_kendaraan ?? '-') }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Area Parkir</span>
                    <span class="text-right">{{ $transaksi->area?->nama_area ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Status</span>
                    <span class="font-semibold uppercase">{{ $transaksi->status }}</span>
                </div>
            </div>

            <div class="py-4 space-y-2 border-b border-dashed border-[#D1D5DB]">
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Waktu Masuk</span>
                    <span>{{ $transaksi->waktu_masuk ? $transaksi->waktu_masuk->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Waktu Keluar</span>
                    <span>{{ $transaksi->waktu_keluar ? $transaksi->waktu_keluar->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Durasi</span>
                    <span>{{ $transaksi->durasi_jam !== null ? $transaksi->durasi_jam . ' jam' : '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Tarif / Jam</span>
                    <span>Rp {{ number_format((int) ($transaksi->tarif?->tarif_per_jam ?? 0), 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="py-4 border-b border-dashed border-[#D1D5DB]">
                <div class="flex justify-between items-center text-sm font-bold">
                    <span>TOTAL BIAYA</span>
                    <span>
                        {{ $transaksi->biaya_total !== null ? 'Rp ' . number_format((int) $transaksi->biaya_total, 0, ',', '.') : 'Belum Keluar' }}
                    </span>
                </div>
            </div>

            <div class="pt-4 space-y-1 text-[11px] text-[#6B7280] text-center">
                <p>Petugas: {{ $transaksi->user?->nama_lengkap ?? '-' }}</p>
                <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </section>
    </div>
@endsection
