@extends('layouts.app')

@section('title', $tarif->exists ? 'Edit Tarif Parkir' : 'Tambah Tarif Parkir')

@section('content')
    @php
        $currentJenis = old('jenis_kendaraan', $tarif->jenis_kendaraan ?: 'motor');
        $jenisLabels = ['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'];
        $nominalVal = (int) old('tarif_per_jam', $tarif->tarif_per_jam ?? 2000);
        $bayClass = match ($currentJenis) {
            'mobil' => 'bg-sky-50/70 border-sky-200/80',
            'lainnya' => 'bg-amber-50/70 border-amber-200/80',
            default => 'bg-emerald-50/70 border-emerald-200/80',
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full bg-[#0F0F10] text-white text-xs font-bold flex items-center justify-center shrink-0">
                    P
                </span>
                <div>
                    <h1 class="text-lg font-bold text-[#111827] tracking-tight">
                        {{ $tarif->exists ? 'Edit Tarif Parkir' : 'Tambah Tarif Parkir' }}
                    </h1>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Atur tarif per jam berdasarkan jenis kendaraan
                    </p>
                </div>
            </div>

            <a href="{{ route('tarif.index') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <section class="pk-card lg:col-span-8 bg-white rounded-[24px] shadow-card flex flex-col z-20">
                <div class="pk-barrier-stripe w-full rounded-t-[24px] overflow-hidden"></div>

                <form method="POST"
                      action="{{ $tarif->exists ? route('tarif.update', $tarif) : route('tarif.store') }}"
                      class="p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-5">
                    @csrf
                    @if ($tarif->exists)
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        <div class="relative" id="tarifJenisDropdown">
                            <label class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                Jenis Kendaraan
                            </label>
                            <input type="hidden" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ $currentJenis }}">
                            <button
                                type="button"
                                aria-haspopup="listbox"
                                aria-expanded="false"
                                class="dd-trigger w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] flex items-center justify-between transition-colors"
                            >
                                <span class="dd-label font-medium">{{ $jenisLabels[$currentJenis] ?? 'Motor' }}</span>
                                <svg class="dd-chevron w-4 h-4 text-[#6B7280] transition-transform duration-150" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                            <div class="pk-dropdown-menu is-closed absolute left-0 right-0 z-40 mt-1.5 rounded-2xl bg-white border border-[#E5E7EB] p-1.5 shadow-[0_10px_28px_rgba(0,0,0,0.08)]" role="listbox">
                                @foreach ($jenisLabels as $val => $label)
                                    @php $selected = $currentJenis === $val; @endphp
                                    <button
                                        type="button"
                                        data-value="{{ $val }}"
                                        class="dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors {{ $selected ? 'bg-[#F3F4F6] font-semibold text-[#111827]' : 'text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]' }}"
                                    >
                                        <span>{{ $label }}</span>
                                        <svg class="dd-check w-4 h-4 text-[#111827] {{ $selected ? '' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                            @error('jenis_kendaraan')
                                <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tarif_per_jam" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                Tarif per Jam (Rp)
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-xs font-semibold text-[#6B7280]">Rp</span>
                                <input
                                    id="tarif_per_jam"
                                    name="tarif_per_jam"
                                    type="number"
                                    min="0"
                                    step="500"
                                    value="{{ old('tarif_per_jam', $tarif->tarif_per_jam) }}"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-[#111827] tabular-nums focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                            </div>
                            <div class="mt-2.5 flex flex-wrap gap-2">
                                @foreach ([2000, 3000, 5000, 10000] as $preset)
                                    <button type="button" data-tarif-preset="{{ $preset }}"
                                            class="tarif-preset rounded-full bg-[#F3F4F6] hover:bg-[#0F0F10] hover:text-white px-3 py-1 text-[11px] font-medium text-[#111827] transition-colors">
                                        Rp {{ number_format($preset, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>
                            @error('tarif_per_jam')
                                <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-[#F3F4F6]">
                        <a href="{{ route('tarif.index') }}"
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

            {{-- Kolom Kanan: Kartu Tarif Live Preview --}}
            <aside class="pk-card lg:col-span-4 bg-white rounded-[24px] shadow-card overflow-hidden flex flex-col justify-between">
                <div class="bg-[#0F0F10] text-white px-5 py-3.5 flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider uppercase">Kartu Tarif</span>
                    <span id="pvTarifBadge" class="rounded-full bg-white/15 px-2.5 py-0.5 text-[11px] font-medium capitalize">
                        {{ $currentJenis }}
                    </span>
                </div>

                <div class="px-6 py-6 text-center flex-1 flex flex-col items-center justify-center">
                    <div id="pvTarifBay" class="rounded-2xl border px-4 py-2.5 mb-3 {{ $bayClass }}">
                        <svg data-svg="motor" class="h-9 w-auto text-[#0F0F10] {{ $currentJenis === 'motor' ? '' : 'hidden' }}" viewBox="0 0 64 42" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15 21a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17Zm0 12.8a4.3 4.3 0 1 1 0-8.6 4.3 4.3 0 0 1 0 8.6Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M47 21a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17Zm0 12.8a4.3 4.3 0 1 1 0-8.6 4.3 4.3 0 0 1 0 8.6Z"/>
                            <path d="M33.5 7.5a1.6 1.6 0 1 0 0 3.2h3.1l1.5 3.4c-1.8-.9-3.9-1.4-6.2-1.4-3.1 0-5.7 1.3-7.6 3.1-1.2 1.1-2.8 1.6-4.5 1.3l-3.6-.7H9.2c-1 0-1.4 1.3-.5 1.9l9.8 4.4 1.7 3.8H15a2 2 0 0 0 0 4h18.5c1.4 0 2.6-.9 3.1-2.2l2.9-7.9 6 9.8a1.7 1.7 0 0 0 2.9-1.8l-6.1-10 2.3-.7c.8-.3 1.4-1 1.4-1.9v-2.2c0-1.1-.6-2.1-1.5-2.7l-1.9-1.3-1.4-3.1a2.4 2.4 0 0 0-2.2-1.4h-5.5Z"/>
                        </svg>
                        <svg data-svg="mobil" class="h-9 w-auto text-[#0F0F10] {{ $currentJenis === 'mobil' ? '' : 'hidden' }}" viewBox="0 0 72 34" fill="currentColor" aria-hidden="true">
                            <path d="M18 20.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                            <path d="M54 20.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M64.8 17.5 55 15.7l-7.2-6.1A6.5 6.5 0 0 0 43.6 8H24.2a6.5 6.5 0 0 0-4.9 2.2l-5.6 6.3-6.2 1.2A3.2 3.2 0 0 0 5 20.8V24a2.5 2.5 0 0 0 2.5 2.5h3.6a7.3 7.3 0 0 1 13.8 0h22.2a7.3 7.3 0 0 1 13.8 0h3.6A2.5 2.5 0 0 0 67 24v-3.5a3 3 0 0 0-2.2-3ZM24.5 11.2h9.2v5.3H18.4l4-4.5a2.8 2.8 0 0 1 2.1-.8Zm12 0h6.9a3 3 0 0 1 2 .8l5.1 4.5H36.5v-5.3Z"/>
                        </svg>
                        <svg data-svg="lainnya" class="h-9 w-auto text-[#0F0F10] {{ $currentJenis === 'lainnya' ? '' : 'hidden' }}" viewBox="0 0 68 36" fill="currentColor" aria-hidden="true">
                            <path d="M18 22a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                            <path d="M51 22a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.5 7A2.5 2.5 0 0 0 6 9.5v16A2.5 2.5 0 0 0 8.5 28h2.6a7.2 7.2 0 0 1 13.8 0h19.2a7.2 7.2 0 0 1 13.8 0h2.6a2.5 2.5 0 0 0 2.5-2.5v-6.2a3.5 3.5 0 0 0-.8-2.3l-4.9-5.8A3.5 3.5 0 0 0 54.6 10H45V9.5A2.5 2.5 0 0 0 42.5 7h-34ZM45 13h8.8l3.9 4.7H45V13Z"/>
                        </svg>
                    </div>

                    <p id="pvTarifHarga" class="text-2xl font-extrabold text-[#111827] tabular-nums">
                        Rp {{ number_format($nominalVal, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-[#6B7280] mt-0.5">per jam parkir</p>
                </div>

                <div class="relative border-t border-dashed border-[#E5E7EB] bg-[#FAFAFA] px-5 py-3.5 text-xs space-y-1.5">
                    <span class="absolute -top-2.5 -left-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <span class="absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full bg-[#F3F4F6]"></span>
                    <div class="flex justify-between">
                        <span class="text-[#6B7280]">Simulasi 2 jam</span>
                        <span id="pvSim2" class="font-semibold text-[#111827] tabular-nums">Rp {{ number_format($nominalVal * 2, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#6B7280]">Simulasi 5 jam</span>
                        <span id="pvSim5" class="font-semibold text-[#111827] tabular-nums">Rp {{ number_format($nominalVal * 5, 0, ',', '.') }}</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dd = document.getElementById('tarifJenisDropdown');
        const trigger = dd.querySelector('.dd-trigger');
        const menu = dd.querySelector('.pk-dropdown-menu');
        const chevron = dd.querySelector('.dd-chevron');
        const jenisInput = document.getElementById('jenis_kendaraan');
        const labelEl = dd.querySelector('.dd-label');
        const nominalInput = document.getElementById('tarif_per_jam');

        const pvBadge = document.getElementById('pvTarifBadge');
        const pvBay = document.getElementById('pvTarifBay');
        const pvHarga = document.getElementById('pvTarifHarga');
        const pvSim2 = document.getElementById('pvSim2');
        const pvSim5 = document.getElementById('pvSim5');

        const bayThemes = {
            motor: 'rounded-2xl border px-4 py-2.5 mb-3 bg-emerald-50/70 border-emerald-200/80',
            mobil: 'rounded-2xl border px-4 py-2.5 mb-3 bg-sky-50/70 border-sky-200/80',
            lainnya: 'rounded-2xl border px-4 py-2.5 mb-3 bg-amber-50/70 border-amber-200/80',
        };

        function syncTarif() {
            const j = jenisInput.value || 'motor';
            const val = Math.max(0, Number(nominalInput.value) || 0);
            pvBadge.textContent = j;
            pvBay.className = bayThemes[j] || bayThemes.motor;
            pvBay.querySelectorAll('[data-svg]').forEach((svg) => {
                svg.classList.toggle('hidden', svg.getAttribute('data-svg') !== j);
            });
            pvHarga.textContent = 'Rp ' + val.toLocaleString('id-ID');
            pvSim2.textContent = 'Rp ' + (val * 2).toLocaleString('id-ID');
            pvSim5.textContent = 'Rp ' + (val * 5).toLocaleString('id-ID');
        }

        function closeDD() {
            menu.classList.add('is-closed');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.classList.remove('border-[#0F0F10]');
            trigger.classList.add('border-[#E5E7EB]');
            chevron.classList.remove('rotate-180');
        }

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const closed = menu.classList.contains('is-closed');
            if (closed) {
                menu.classList.remove('is-closed');
                trigger.setAttribute('aria-expanded', 'true');
                trigger.classList.remove('border-[#E5E7EB]');
                trigger.classList.add('border-[#0F0F10]');
                chevron.classList.add('rotate-180');
            } else {
                closeDD();
            }
        });

        dd.querySelectorAll('.dd-option').forEach((opt) => {
            opt.addEventListener('click', () => {
                jenisInput.value = opt.getAttribute('data-value');
                labelEl.textContent = opt.querySelector('span').textContent;
                dd.querySelectorAll('.dd-option').forEach((o) => {
                    const active = o === opt;
                    o.className = active
                        ? 'dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors bg-[#F3F4F6] font-semibold text-[#111827]'
                        : 'dd-option w-full rounded-xl px-3.5 py-2 text-sm text-left flex items-center justify-between transition-colors text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]';
                    o.querySelector('.dd-check').classList.toggle('hidden', !active);
                });
                closeDD();
                syncTarif();
            });
        });

        document.addEventListener('click', closeDD);
        nominalInput.addEventListener('input', syncTarif);
        document.querySelectorAll('[data-tarif-preset]').forEach((btn) => {
            btn.addEventListener('click', () => {
                nominalInput.value = btn.getAttribute('data-tarif-preset');
                syncTarif();
            });
        });
    });
</script>
@endpush
