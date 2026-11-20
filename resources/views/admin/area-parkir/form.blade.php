@extends('layouts.app')

@section('title', $area->exists ? 'Edit Area Parkir' : 'Tambah Area Parkir')

@section('content')
    @php
        $terisi = $area->exists ? (int) $area->terisi : 0;
        $kapasitasVal = (int) old('kapasitas', $area->kapasitas ?? 20);
        $kapasitasSafe = max(1, $kapasitasVal);
        $pct = min(100, (int) round(($terisi / $kapasitasSafe) * 100));
        $sisa = max(0, $kapasitasVal - $terisi);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full bg-[#0F0F10] text-white text-xs font-bold flex items-center justify-center shrink-0">
                    P
                </span>
                <div>
                    <h1 class="text-lg font-bold text-[#111827] tracking-tight">
                        {{ $area->exists ? 'Edit Area Parkir' : 'Tambah Area Parkir' }}
                    </h1>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Kelola nama zona dan kapasitas slot maksimal
                    </p>
                </div>
            </div>

            <a href="{{ route('area-parkir.index') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <section class="pk-card lg:col-span-8 bg-white rounded-[24px] shadow-card flex flex-col">
                <div class="pk-barrier-stripe w-full rounded-t-[24px] overflow-hidden"></div>

                <form method="POST"
                      action="{{ $area->exists ? route('area-parkir.update', $area) : route('area-parkir.store') }}"
                      class="p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-5">
                    @csrf
                    @if ($area->exists)
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label for="nama_area" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                Nama Area
                            </label>
                            <input
                                id="nama_area"
                                name="nama_area"
                                type="text"
                                maxlength="50"
                                value="{{ old('nama_area', $area->nama_area) }}"
                                placeholder="Contoh: Area A - Motor"
                                required
                                class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none transition-colors"
                            >
                            @error('nama_area')
                                <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label for="kapasitas" class="block text-xs font-medium text-[#6B7280]">
                                    Kapasitas (Slot)
                                </label>
                                @if ($area->exists)
                                    <span class="text-xs text-[#6B7280]">
                                        Sedang terisi: <strong class="text-[#111827]">{{ $terisi }}</strong>
                                    </span>
                                @endif
                            </div>
                            <input
                                id="kapasitas"
                                name="kapasitas"
                                type="number"
                                min="{{ $area->exists ? max(1, $terisi) : 1 }}"
                                value="{{ old('kapasitas', $area->kapasitas) }}"
                                required
                                class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] tabular-nums focus:border-[#0F0F10] focus:outline-none transition-colors"
                            >
                            @error('kapasitas')
                                <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-[#F3F4F6]">
                        <a href="{{ route('area-parkir.index') }}"
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

            {{-- Kolom Kanan: Kartu Zona Parkir --}}
            <aside class="pk-card lg:col-span-4 bg-white rounded-[24px] shadow-card overflow-hidden flex flex-col justify-between">
                <div class="bg-[#0F0F10] text-white px-5 py-3.5 flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider uppercase">Zona Parkir</span>
                    <span id="pvAreaStatus" class="rounded-full bg-white/15 px-2.5 py-0.5 text-[11px] font-medium">
                        {{ $sisa === 0 ? 'Penuh' : 'Tersedia' }}
                    </span>
                </div>

                <div class="px-6 py-6 flex-1 flex flex-col justify-center space-y-4">
                    <div>
                        <p id="pvAreaNama" class="text-base font-bold text-[#111827] truncate">
                            {{ old('nama_area', $area->nama_area) ?: 'Nama Area' }}
                        </p>
                        <p class="text-xs text-[#6B7280] mt-0.5">Kapasitas terpakai saat ini</p>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-semibold text-[#111827] mb-2 tabular-nums">
                            <span>{{ $terisi }} / <span id="pvAreaKap">{{ $kapasitasVal ?: 0 }}</span> slot</span>
                            <span id="pvAreaPct">{{ $pct }}%</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#F3F4F6] overflow-hidden">
                            <div id="pvAreaBar" class="h-full rounded-full bg-[#0F0F10] transition-all duration-300" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="relative border-t border-dashed border-[#E5E7EB] bg-[#FAFAFA] px-5 py-3.5 flex justify-between items-center text-xs">
                    <span class="absolute -top-2.5 -left-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <span class="absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <span class="text-[#6B7280]">Sisa slot kosong</span>
                    <span id="pvAreaSisa" class="font-bold text-[#111827] tabular-nums">{{ $sisa }} slot</span>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const namaInput = document.getElementById('nama_area');
        const kapInput = document.getElementById('kapasitas');
        const terisi = {{ $terisi }};

        const pvNama = document.getElementById('pvAreaNama');
        const pvKap = document.getElementById('pvAreaKap');
        const pvPct = document.getElementById('pvAreaPct');
        const pvBar = document.getElementById('pvAreaBar');
        const pvSisa = document.getElementById('pvAreaSisa');
        const pvStatus = document.getElementById('pvAreaStatus');

        function syncArea() {
            const nama = namaInput.value.trim() || 'Nama Area';
            const kap = Math.max(0, Number(kapInput.value) || 0);
            const safeKap = Math.max(1, kap);
            const pct = Math.min(100, Math.round((terisi / safeKap) * 100));
            const sisa = Math.max(0, kap - terisi);

            pvNama.textContent = nama;
            pvKap.textContent = kap;
            pvPct.textContent = pct + '%';
            pvBar.style.width = pct + '%';
            pvSisa.textContent = sisa + ' slot';
            pvStatus.textContent = sisa === 0 ? 'Penuh' : 'Tersedia';
        }

        namaInput.addEventListener('input', syncArea);
        kapInput.addEventListener('input', syncArea);
    });
</script>
@endpush
