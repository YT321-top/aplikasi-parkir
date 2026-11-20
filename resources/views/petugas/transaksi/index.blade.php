@extends('layouts.app')

@section('title', 'Transaksi Parkir')

@section('content')
    @php
        $initialMode = old('mode_kendaraan', 'terdaftar');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Transaksi Parkir</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Pencatatan kendaraan masuk dan proses keluar</p>
            </div>
            <a href="{{ route('transaksi.riwayat') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Riwayat &amp; Struk
            </a>
        </div>

        {{-- Form Kendaraan Masuk --}}
        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <h2 class="text-base font-bold text-[#111827]">Kendaraan Masuk</h2>

                <div class="inline-flex rounded-full bg-[#F3F4F6] p-1">
                    <button type="button" id="btnModeTerdaftar"
                            class="rounded-full px-4 py-1.5 text-xs font-medium transition-colors">
                        Terdaftar
                    </button>
                    <button type="button" id="btnModeBaru"
                            class="rounded-full px-4 py-1.5 text-xs font-medium transition-colors">
                        Kendaraan Baru
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] px-4 py-3 text-xs font-medium text-[#DC2626]">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('transaksi.masuk') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="mode_kendaraan" id="mode_kendaraan" value="{{ $initialMode }}">

                {{-- Mode Terdaftar --}}
                <div id="sectionTerdaftar" class="space-y-4">
                    <div>
                        <label for="id_kendaraan" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Pilih Kendaraan Terdaftar
                        </label>
                        <select
                            id="id_kendaraan"
                            name="id_kendaraan"
                            class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                            <option value="">Pilih Plat Nomor</option>
                            @foreach ($kendaraans as $k)
                                @php $sedangParkir = in_array($k->id_kendaraan, $activeKendaraanIds, true); @endphp
                                <option
                                    value="{{ $k->id_kendaraan }}"
                                    data-jenis="{{ $k->jenis_kendaraan }}"
                                    @selected((string) old('id_kendaraan') === (string) $k->id_kendaraan)
                                    @disabled($sedangParkir)
                                >
                                    {{ $k->plat_nomor }} — {{ ucfirst($k->jenis_kendaraan) }} ({{ $k->pemilik }}){{ $sedangParkir ? ' [Sedang Parkir]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Mode Input Cepat Kendaraan Baru --}}
                <div id="sectionBaru" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="plat_nomor" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Plat Nomor
                        </label>
                        <input
                            id="plat_nomor"
                            name="plat_nomor"
                            type="text"
                            maxlength="15"
                            value="{{ old('plat_nomor') }}"
                            placeholder="B 1234 XYZ"
                            class="w-full rounded-xl border border-[#E5E7EB] px-4 py-2.5 text-sm text-[#111827] uppercase focus:border-[#0F0F10] focus:outline-none"
                        >
                    </div>
                    <div>
                        <label for="jenis_kendaraan" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Jenis Kendaraan
                        </label>
                        <select
                            id="jenis_kendaraan"
                            name="jenis_kendaraan"
                            class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                            <option value="motor" @selected(old('jenis_kendaraan', 'motor') === 'motor')>Motor</option>
                            <option value="mobil" @selected(old('jenis_kendaraan') === 'mobil')>Mobil</option>
                            <option value="lainnya" @selected(old('jenis_kendaraan') === 'lainnya')>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="warna" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Warna
                        </label>
                        <input
                            id="warna"
                            name="warna"
                            type="text"
                            maxlength="20"
                            value="{{ old('warna') }}"
                            placeholder="Hitam"
                            class="w-full rounded-xl border border-[#E5E7EB] px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                    </div>
                    <div>
                        <label for="pemilik" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Nama Pemilik
                        </label>
                        <input
                            id="pemilik"
                            name="pemilik"
                            type="text"
                            maxlength="100"
                            value="{{ old('pemilik') }}"
                            placeholder="Nama pemilik"
                            class="w-full rounded-xl border border-[#E5E7EB] px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                    </div>
                </div>

                {{-- Area Parkir & Tarif --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="id_area" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Area Parkir
                        </label>
                        <select
                            id="id_area"
                            name="id_area"
                            required
                            class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                            <option value="">Pilih Area Parkir</option>
                            @foreach ($areas as $area)
                                @php $penuh = $area->terisi >= $area->kapasitas; @endphp
                                <option
                                    value="{{ $area->id_area }}"
                                    @selected((string) old('id_area') === (string) $area->id_area)
                                    @disabled($penuh)
                                >
                                    {{ $area->nama_area }} (Sisa {{ $area->sisa_slot }} / {{ $area->kapasitas }}){{ $penuh ? ' — Penuh' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_tarif" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                            Tarif Parkir
                        </label>
                        <select
                            id="id_tarif"
                            name="id_tarif"
                            required
                            class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                        >
                            <option value="">Pilih Tarif</option>
                            @foreach ($tarifs as $t)
                                <option
                                    value="{{ $t->id_tarif }}"
                                    data-jenis="{{ $t->jenis_kendaraan }}"
                                    @selected((string) old('id_tarif') === (string) $t->id_tarif)
                                >
                                    {{ ucfirst($t->jenis_kendaraan) }} — Rp {{ number_format((int) $t->tarif_per_jam, 0, ',', '.') }}/jam
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="rounded-full bg-[#0F0F10] text-white px-6 py-2.5 text-xs font-semibold hover:bg-[#262626] transition-colors shadow-pill">
                        Catat Kendaraan Masuk
                    </button>
                </div>
            </form>
        </section>

        {{-- Tabel Kendaraan Sedang Parkir --}}
        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-base font-bold text-[#111827]">Kendaraan Sedang Parkir</h2>
                <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-semibold text-[#111827] tabular-nums">
                    {{ $activeTransactions->count() }} aktif
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">No</th>
                            <th class="pb-2 px-3 font-medium">Plat Nomor</th>
                            <th class="pb-2 px-3 font-medium">Jenis</th>
                            <th class="pb-2 px-3 font-medium">Area Parkir</th>
                            <th class="pb-2 px-3 font-medium">Tarif/Jam</th>
                            <th class="pb-2 px-3 font-medium">Waktu Masuk</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($activeTransactions as $trx)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl text-xs font-medium text-[#6B7280] tabular-nums whitespace-nowrap">
                                    #{{ $trx->id_parkir }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $trx->kendaraan?->plat_nomor ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-[#4B5563] whitespace-nowrap">
                                    {{ ucfirst($trx->kendaraan?->jenis_kendaraan ?? '-') }}
                                </td>
                                <td class="py-3.5 px-3 text-[#111827] whitespace-nowrap">
                                    {{ $trx->area?->nama_area ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-xs font-medium text-[#111827] tabular-nums whitespace-nowrap">
                                    Rp {{ number_format((int) ($trx->tarif?->tarif_per_jam ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-xs text-[#6B7280] tabular-nums whitespace-nowrap">
                                    {{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('transaksi.struk', $trx) }}"
                                           class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                            Struk
                                        </a>
                                        <button
                                            type="button"
                                            data-checkout-trigger
                                            data-action="{{ route('transaksi.keluar', $trx) }}"
                                            data-id="{{ $trx->id_parkir }}"
                                            data-plat="{{ $trx->kendaraan?->plat_nomor ?? '-' }}"
                                            data-jenis="{{ $trx->kendaraan?->jenis_kendaraan ?? 'lainnya' }}"
                                            data-area="{{ $trx->area?->nama_area ?? '-' }}"
                                            data-tarif="{{ (int) ($trx->tarif?->tarif_per_jam ?? 0) }}"
                                            data-masuk-label="{{ $trx->waktu_masuk ? $trx->waktu_masuk->format('d M Y, H:i') : '-' }}"
                                            data-masuk-iso="{{ $trx->waktu_masuk ? $trx->waktu_masuk->toIso8601String() : '' }}"
                                            class="rounded-full bg-[#0F0F10] text-white px-4 py-1.5 text-xs font-medium hover:bg-[#262626] transition-colors"
                                        >
                                            Proses Keluar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-[#6B7280]">
                                    Tidak ada kendaraan yang sedang parkir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @include('components.checkout-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modeInput = document.getElementById('mode_kendaraan');
            const btnTerdaftar = document.getElementById('btnModeTerdaftar');
            const btnBaru = document.getElementById('btnModeBaru');
            const sectionTerdaftar = document.getElementById('sectionTerdaftar');
            const sectionBaru = document.getElementById('sectionBaru');
            const selectKendaraan = document.getElementById('id_kendaraan');
            const selectJenisBaru = document.getElementById('jenis_kendaraan');
            const selectTarif = document.getElementById('id_tarif');

            function syncTarifByJenis(jenis) {
                if (!jenis || !selectTarif) return;
                for (const opt of selectTarif.options) {
                    if (opt.dataset.jenis === jenis) {
                        selectTarif.value = opt.value;
                        selectTarif.dispatchEvent(new Event('change', { bubbles: true }));
                        break;
                    }
                }
            }

            function setMode(mode) {
                modeInput.value = mode;
                const isBaru = mode === 'baru';

                sectionTerdaftar.classList.toggle('hidden', isBaru);
                sectionBaru.classList.toggle('hidden', !isBaru);

                btnTerdaftar.className = isBaru
                    ? 'rounded-full px-4 py-1.5 text-xs font-medium text-[#6B7280] hover:text-[#111827] transition-colors'
                    : 'rounded-full px-4 py-1.5 text-xs font-medium bg-[#0F0F10] text-white transition-colors';

                btnBaru.className = isBaru
                    ? 'rounded-full px-4 py-1.5 text-xs font-medium bg-[#0F0F10] text-white transition-colors'
                    : 'rounded-full px-4 py-1.5 text-xs font-medium text-[#6B7280] hover:text-[#111827] transition-colors';

                if (isBaru) {
                    syncTarifByJenis(selectJenisBaru.value);
                } else if (selectKendaraan.selectedOptions[0]?.dataset.jenis) {
                    syncTarifByJenis(selectKendaraan.selectedOptions[0].dataset.jenis);
                }
            }

            btnTerdaftar.addEventListener('click', () => setMode('terdaftar'));
            btnBaru.addEventListener('click', () => setMode('baru'));

            selectKendaraan.addEventListener('change', function () {
                const jenis = this.selectedOptions[0]?.dataset.jenis;
                syncTarifByJenis(jenis);
            });

            selectJenisBaru.addEventListener('change', function () {
                syncTarifByJenis(this.value);
            });

            setMode(modeInput.value || 'terdaftar');
        });
    </script>
@endpush
