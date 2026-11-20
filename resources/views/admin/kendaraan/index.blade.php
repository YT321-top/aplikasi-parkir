@extends('layouts.app')

@section('title', 'Kendaraan')

@section('content')
    @php
        $typeThemes = [
            'motor' => [
                'badge' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'bay' => 'bg-emerald-50/70 border-emerald-200/80',
                'hover' => 'hover:border-emerald-300',
            ],
            'mobil' => [
                'badge' => 'bg-sky-50 text-sky-700 border border-sky-200',
                'bay' => 'bg-sky-50/70 border-sky-200/80',
                'hover' => 'hover:border-sky-300',
            ],
            'lainnya' => [
                'badge' => 'bg-amber-50 text-amber-700 border border-amber-200',
                'bay' => 'bg-amber-50/70 border-amber-200/80',
                'hover' => 'hover:border-amber-300',
            ],
        ];

        $resolveColorSwatch = static function (?string $warna): array {
            $w = mb_strtolower(trim((string) $warna));
            return match (true) {
                str_contains($w, 'hitam') || str_contains($w, 'black') => ['#111827', '#111827'],
                str_contains($w, 'putih') || str_contains($w, 'white') => ['#FFFFFF', '#D1D5DB'],
                str_contains($w, 'silver') || str_contains($w, 'abu') || str_contains($w, 'grey') => ['#D1D5DB', '#9CA3AF'],
                str_contains($w, 'merah') || str_contains($w, 'red') => ['#EF4444', '#DC2626'],
                str_contains($w, 'biru') || str_contains($w, 'blue') => ['#3B82F6', '#2563EB'],
                str_contains($w, 'kuning') || str_contains($w, 'yellow') => ['#F59E0B', '#D97706'],
                str_contains($w, 'hijau') || str_contains($w, 'green') => ['#10B981', '#059669'],
                str_contains($w, 'oranye') || str_contains($w, 'orange') => ['#F97316', '#EA580C'],
                str_contains($w, 'coklat') || str_contains($w, 'brown') => ['#92400E', '#78350F'],
                default => ['#9CA3AF', '#6B7280'],
            };
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">Kendaraan</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Daftar kendaraan terdaftar berdasarkan plat nomor</p>
            </div>
            <a href="{{ route('kendaraan.create') }}"
               class="rounded-full bg-[#0F0F10] text-white px-5 py-2.5 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill">
                + Tambah Kendaraan
            </a>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <form method="GET" action="{{ route('kendaraan.index') }}" class="flex flex-wrap items-center gap-3 mb-6">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari plat nomor atau pemilik"
                    class="w-full sm:w-64 rounded-xl border border-[#E5E7EB] px-4 py-2 text-xs text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none"
                >
                <select
                    name="jenis_kendaraan"
                    class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                >
                    <option value="">Semua Jenis</option>
                    <option value="motor" @selected($jenis === 'motor')>Motor</option>
                    <option value="mobil" @selected($jenis === 'mobil')>Mobil</option>
                    <option value="lainnya" @selected($jenis === 'lainnya')>Lainnya</option>
                </select>
                <button type="submit"
                        class="rounded-full bg-[#0F0F10] text-white px-4 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                    Filter
                </button>
                @if ($search !== '' || $jenis !== '')
                    <a href="{{ route('kendaraan.index') }}"
                       class="rounded-full border border-[#D1D5DB] px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($kendaraans as $item)
                    @php
                        $jenisKey = in_array($item->jenis_kendaraan, ['motor', 'mobil', 'lainnya'], true)
                            ? $item->jenis_kendaraan
                            : 'lainnya';
                        $theme = $typeThemes[$jenisKey];
                        [$swatchBg, $swatchBorder] = $resolveColorSwatch($item->warna);
                    @endphp

                    <article class="pk-card bg-[#FAFAFA] hover:bg-white border border-[#E5E7EB] {{ $theme['hover'] }} rounded-[24px] p-5 transition flex flex-col justify-between group">
                        <div>
                            {{-- Baris Atas: Siluet Kendaraan + Badge Jenis --}}
                            <div class="flex justify-between items-center gap-2">
                                <div class="rounded-2xl border px-3 py-1.5 flex items-center justify-center {{ $theme['bay'] }}">
                                    @if ($jenisKey === 'motor')
                                        <svg class="h-9 w-auto text-[#0F0F10] transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 64 42" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15 21a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17Zm0 12.8a4.3 4.3 0 1 1 0-8.6 4.3 4.3 0 0 1 0 8.6Z"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M47 21a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17Zm0 12.8a4.3 4.3 0 1 1 0-8.6 4.3 4.3 0 0 1 0 8.6Z"/>
                                            <path d="M33.5 7.5a1.6 1.6 0 1 0 0 3.2h3.1l1.5 3.4c-1.8-.9-3.9-1.4-6.2-1.4-3.1 0-5.7 1.3-7.6 3.1-1.2 1.1-2.8 1.6-4.5 1.3l-3.6-.7H9.2c-1 0-1.4 1.3-.5 1.9l9.8 4.4 1.7 3.8H15a2 2 0 0 0 0 4h18.5c1.4 0 2.6-.9 3.1-2.2l2.9-7.9 6 9.8a1.7 1.7 0 0 0 2.9-1.8l-6.1-10 2.3-.7c.8-.3 1.4-1 1.4-1.9v-2.2c0-1.1-.6-2.1-1.5-2.7l-1.9-1.3-1.4-3.1a2.4 2.4 0 0 0-2.2-1.4h-5.5Z"/>
                                        </svg>
                                    @elseif ($jenisKey === 'mobil')
                                        <svg class="h-9 w-auto text-[#0F0F10] transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 72 34" fill="currentColor" aria-hidden="true">
                                            <path d="M18 20.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                                            <path d="M54 20.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M64.8 17.5 55 15.7l-7.2-6.1A6.5 6.5 0 0 0 43.6 8H24.2a6.5 6.5 0 0 0-4.9 2.2l-5.6 6.3-6.2 1.2A3.2 3.2 0 0 0 5 20.8V24a2.5 2.5 0 0 0 2.5 2.5h3.6a7.3 7.3 0 0 1 13.8 0h22.2a7.3 7.3 0 0 1 13.8 0h3.6A2.5 2.5 0 0 0 67 24v-3.5a3 3 0 0 0-2.2-3ZM24.5 11.2h9.2v5.3H18.4l4-4.5a2.8 2.8 0 0 1 2.1-.8Zm12 0h6.9a3 3 0 0 1 2 .8l5.1 4.5H36.5v-5.3Z"/>
                                        </svg>
                                    @else
                                        <svg class="h-9 w-auto text-[#0F0F10] transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 68 36" fill="currentColor" aria-hidden="true">
                                            <path d="M18 22a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                                            <path d="M51 22a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.5 7A2.5 2.5 0 0 0 6 9.5v16A2.5 2.5 0 0 0 8.5 28h2.6a7.2 7.2 0 0 1 13.8 0h19.2a7.2 7.2 0 0 1 13.8 0h2.6a2.5 2.5 0 0 0 2.5-2.5v-6.2a3.5 3.5 0 0 0-.8-2.3l-4.9-5.8A3.5 3.5 0 0 0 54.6 10H45V9.5A2.5 2.5 0 0 0 42.5 7h-34ZM45 13h8.8l3.9 4.7H45V13Z"/>
                                        </svg>
                                    @endif
                                </div>

                                <span class="{{ $theme['badge'] }} text-xs font-medium px-3 py-1 rounded-full capitalize">
                                    {{ $item->jenis_kendaraan }}
                                </span>
                            </div>

                            {{-- Tengah: Bingkai Plat Nomor --}}
                            <div class="relative my-4 bg-white border-2 border-[#111827] rounded-xl py-2.5 px-6 text-center shadow-[0_2px_6px_rgba(15,15,16,0.06)] transition-transform duration-200 group-hover:scale-[1.015]">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                                <span class="text-lg font-extrabold tracking-widest text-[#111827] uppercase">{{ $item->plat_nomor }}</span>
                            </div>

                            {{-- Detail Informasi --}}
                            <div class="text-xs text-[#6B7280] space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Pemilik</span>
                                    <span class="font-semibold text-[#111827] truncate">{{ $item->pemilik }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span>Warna</span>
                                    <span class="inline-flex items-center gap-1.5 font-medium text-[#111827]">
                                        <span class="w-2.5 h-2.5 rounded-full border shrink-0" style="background-color: {{ $swatchBg }}; border-color: {{ $swatchBorder }};"></span>
                                        {{ $item->warna }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span>Diinput Oleh</span>
                                    <span class="text-[#4B5563] truncate">{{ $item->user?->nama_lengkap ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bawah: Kode Unit + Tombol Aksi --}}
                        <div class="mt-4 pt-3.5 border-t border-dashed border-[#E5E7EB] flex justify-between items-center gap-2">
                            <span class="font-mono text-[11px] text-[#9CA3AF]">
                                #KND-{{ str_pad((string) $item->id_kendaraan, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kendaraan.edit', $item) }}"
                                   class="rounded-full border border-[#D1D5DB] bg-white px-4 py-1.5 text-xs font-medium text-[#111827] hover:bg-[#0F0F10] hover:text-white hover:border-[#0F0F10] transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('kendaraan.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus kendaraan {{ $item->plat_nomor }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-full border border-[#D1D5DB] bg-white px-4 py-1.5 text-xs font-medium text-[#111827] hover:border-[#DC2626] hover:text-[#DC2626] transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-10 text-center text-xs text-[#6B7280]">
                        Data kendaraan tidak ditemukan.
                    </div>
                @endforelse
            </div>

            {{ $kendaraans->links('components.pagination') }}
        </section>
    </div>
@endsection
