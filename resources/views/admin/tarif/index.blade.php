@extends('layouts.app')

@section('title', 'Tarif Parkir')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Tarif Parkir</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Pengaturan tarif per jam berdasarkan jenis kendaraan</p>
            </div>
            <a href="{{ route('tarif.create') }}"
               class="rounded-full bg-[#0F0F10] text-white px-5 py-2.5 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill">
                + Tambah Tarif
            </a>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">Jenis Kendaraan</th>
                            <th class="pb-2 px-3 font-medium">Tarif per Jam</th>
                            <th class="pb-2 px-3 font-medium">Digunakan Transaksi</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($tarifs as $item)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-semibold text-[#111827] whitespace-nowrap">
                                    {{ ucfirst($item->jenis_kendaraan) }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] tabular-nums whitespace-nowrap">
                                    Rp {{ number_format((int) $item->tarif_per_jam, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-medium text-[#4B5563] tabular-nums">
                                        {{ $item->transaksi_count }} transaksi
                                    </span>
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('tarif.edit', $item) }}"
                                           class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('tarif.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus tarif {{ $item->jenis_kendaraan }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-full border border-[#E5E7EB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#6B7280] hover:border-[#DC2626] hover:text-[#DC2626] transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs text-[#6B7280]">
                                    Belum ada data tarif parkir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tarifs->links('components.pagination') }}
        </section>
    </div>
@endsection
