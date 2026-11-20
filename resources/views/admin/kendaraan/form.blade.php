@extends('layouts.app')

@section('title', $kendaraan->exists ? 'Edit Kendaraan' : 'Tambah Kendaraan')

@section('content')
    @php
        $currentJenis = old('jenis_kendaraan', $kendaraan->jenis_kendaraan ?: 'motor');
        $jenisLabels = ['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'];
        $bayThemes = [
            'motor' => 'bg-emerald-50/70 border-emerald-200/80',
            'mobil' => 'bg-sky-50/70 border-sky-200/80',
            'lainnya' => 'bg-amber-50/70 border-amber-200/80',
        ];
        $badgeThemes = [
            'motor' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'mobil' => 'bg-sky-50 text-sky-700 border border-sky-200',
            'lainnya' => 'bg-amber-50 text-amber-700 border border-amber-200',
        ];
        $passCode = $kendaraan->exists
            ? '#KND-' . str_pad((string) $kendaraan->id_kendaraan, 3, '0', STR_PAD_LEFT)
            : '#KND-BARU';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full bg-[#0F0F10] text-white text-xs font-bold flex items-center justify-center shrink-0">
                    P
                </span>
                <div>
                    <h1 class="text-lg font-bold text-[#111827] tracking-tight">
                        {{ $kendaraan->exists ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}
                    </h1>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Perbarui identitas plat nomor dan pemilik kendaraan
                    </p>
                </div>
            </div>

            <a href="{{ route('kendaraan.index') }}"
               class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <section class="pk-card lg:col-span-8 bg-white rounded-[24px] shadow-card flex flex-col z-20">
                <div class="pk-barrier-stripe w-full rounded-t-[24px] overflow-hidden"></div>

                <form method="POST"
                      action="{{ $kendaraan->exists ? route('kendaraan.update', $kendaraan) : route('kendaraan.store') }}"
                      class="p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-5">
                    @csrf
                    @if ($kendaraan->exists)
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="plat_nomor" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Plat Nomor
                                </label>
                                <input
                                    id="plat_nomor"
                                    name="plat_nomor"
                                    type="text"
                                    maxlength="15"
                                    value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}"
                                    placeholder="Contoh: B 1234 ABC"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] uppercase focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                @error('plat_nomor')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="relative" id="kndJenisDropdown">
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
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="warna" class="block text-xs font-medium text-[#6B7280] mb-1.5">
                                    Warna
                                </label>
                                <input
                                    id="warna"
                                    name="warna"
                                    type="text"
                                    maxlength="20"
                                    value="{{ old('warna', $kendaraan->warna) }}"
                                    placeholder="Contoh: Hitam"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                @error('warna')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
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
                                    value="{{ old('pemilik', $kendaraan->pemilik) }}"
                                    placeholder="Nama lengkap pemilik"
                                    required
                                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-sm text-[#111827] focus:border-[#0F0F10] focus:outline-none transition-colors"
                                >
                                @error('pemilik')
                                    <p class="mt-1 text-xs text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-[#F3F4F6]">
                        <a href="{{ route('kendaraan.index') }}"
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

            {{-- Kolom Kanan: Pratinjau Kartu Kendaraan --}}
            <aside class="pk-card lg:col-span-4 bg-white rounded-[24px] p-6 shadow-card flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center gap-2">
                        <div id="pvKndBay" class="rounded-2xl border px-3 py-1.5 flex items-center justify-center {{ $bayThemes[$currentJenis] ?? $bayThemes['motor'] }}">
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

                        <span id="pvKndBadge" class="{{ $badgeThemes[$currentJenis] ?? $badgeThemes['motor'] }} text-xs font-medium px-3 py-1 rounded-full capitalize">
                            {{ $currentJenis }}
                        </span>
                    </div>

                    <div class="relative my-5 bg-white border-2 border-[#111827] rounded-xl py-2.5 px-6 text-center shadow-[0_2px_6px_rgba(15,15,16,0.06)]">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                        <span id="pvKndPlat" class="text-lg font-extrabold tracking-widest text-[#111827] uppercase">
                            {{ old('plat_nomor', $kendaraan->plat_nomor) ?: 'B 1234 ABC' }}
                        </span>
                    </div>

                    <div class="text-xs text-[#6B7280] space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span>Pemilik</span>
                            <span id="pvKndPemilik" class="font-semibold text-[#111827] truncate">
                                {{ old('pemilik', $kendaraan->pemilik) ?: '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span>Warna</span>
                            <span id="pvKndWarna" class="font-medium text-[#111827]">
                                {{ old('warna', $kendaraan->warna) ?: '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3.5 border-t border-dashed border-[#E5E7EB] flex items-center justify-between text-[11px] text-[#9CA3AF]">
                    <span class="font-mono">{{ $passCode }}</span>
                    <span>Pratinjau Kartu</span>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dd = document.getElementById('kndJenisDropdown');
        const trigger = dd.querySelector('.dd-trigger');
        const menu = dd.querySelector('.pk-dropdown-menu');
        const chevron = dd.querySelector('.dd-chevron');
        const jenisInput = document.getElementById('jenis_kendaraan');
        const labelEl = dd.querySelector('.dd-label');

        const platInput = document.getElementById('plat_nomor');
        const warnaInput = document.getElementById('warna');
        const pemilikInput = document.getElementById('pemilik');

        const pvBay = document.getElementById('pvKndBay');
        const pvBadge = document.getElementById('pvKndBadge');
        const pvPlat = document.getElementById('pvKndPlat');
        const pvPemilik = document.getElementById('pvKndPemilik');
        const pvWarna = document.getElementById('pvKndWarna');

        const bayThemes = {
            motor: 'rounded-2xl border px-3 py-1.5 flex items-center justify-center bg-emerald-50/70 border-emerald-200/80',
            mobil: 'rounded-2xl border px-3 py-1.5 flex items-center justify-center bg-sky-50/70 border-sky-200/80',
            lainnya: 'rounded-2xl border px-3 py-1.5 flex items-center justify-center bg-amber-50/70 border-amber-200/80',
        };
        const badgeThemes = {
            motor: 'bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-medium px-3 py-1 rounded-full capitalize',
            mobil: 'bg-sky-50 text-sky-700 border border-sky-200 text-xs font-medium px-3 py-1 rounded-full capitalize',
            lainnya: 'bg-amber-50 text-amber-700 border border-amber-200 text-xs font-medium px-3 py-1 rounded-full capitalize',
        };

        function syncKnd() {
            const j = jenisInput.value || 'motor';
            pvBay.className = bayThemes[j] || bayThemes.motor;
            pvBadge.className = badgeThemes[j] || badgeThemes.motor;
            pvBadge.textContent = j;
            pvBay.querySelectorAll('[data-svg]').forEach((svg) => {
                svg.classList.toggle('hidden', svg.getAttribute('data-svg') !== j);
            });
            pvPlat.textContent = (platInput.value.trim() || 'B 1234 ABC').toUpperCase();
            pvPemilik.textContent = pemilikInput.value.trim() || '-';
            pvWarna.textContent = warnaInput.value.trim() || '-';
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
                syncKnd();
            });
        });

        document.addEventListener('click', closeDD);
        [platInput, warnaInput, pemilikInput].forEach((el) => el.addEventListener('input', syncKnd));
    });
</script>
@endpush
