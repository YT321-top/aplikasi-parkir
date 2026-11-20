@extends('layouts.app')

@section('title', 'Area Parkir')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Area Parkir</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Kelola zona parkir, kapasitas, dan sisa slot</p>
            </div>
            <a href="{{ route('area-parkir.create') }}"
               class="rounded-full bg-[#0F0F10] text-white px-5 py-2.5 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill">
                + Tambah Area
            </a>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">Nama Area</th>
                            <th class="pb-2 px-3 font-medium">Kapasitas</th>
                            <th class="pb-2 px-3 font-medium">Terisi</th>
                            <th class="pb-2 px-3 font-medium">Sisa Slot</th>
                            <th class="pb-2 px-3 font-medium">Status</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($areas as $item)
                            @php
                                $penuh = $item->terisi >= $item->kapasitas;
                            @endphp
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $item->nama_area }}
                                </td>
                                <td class="py-3.5 px-3 text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $item->kapasitas }}
                                </td>
                                <td class="py-3.5 px-3 text-[#4B5563] tabular-nums whitespace-nowrap">
                                    {{ $item->terisi }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] tabular-nums whitespace-nowrap">
                                    {{ $item->sisa_slot }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $penuh ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#111827]' }}">
                                        {{ $penuh ? 'Penuh' : 'Tersedia' }}
                                    </span>
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('area-parkir.edit', $item) }}"
                                           class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('area-parkir.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus area {{ $item->nama_area }}?');" class="inline">
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
                                <td colspan="6" class="py-8 text-center text-xs text-[#6B7280]">
                                    Belum ada data area parkir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $areas->links('components.pagination') }}
        </section>
    </div>
@endsection
