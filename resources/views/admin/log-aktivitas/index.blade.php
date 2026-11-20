@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-lg font-bold text-[#111827] tracking-tight">Log Aktivitas</h1>
            <p class="text-xs text-[#6B7280] mt-0.5">Riwayat aktivitas pengguna sistem (read-only)</p>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <form method="GET" action="{{ route('log-aktivitas.index') }}" class="flex flex-wrap items-end gap-3 mb-5">
                <div>
                    <label for="id_user" class="block text-[11px] font-medium text-[#6B7280] mb-1">Pengguna</label>
                    <select
                        id="id_user"
                        name="id_user"
                        class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id_user }}" @selected((string) $idUser === (string) $u->id_user)>
                                {{ $u->nama_lengkap }} ({{ '@' . $u->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tanggal_mulai" class="block text-[11px] font-medium text-[#6B7280] mb-1">Dari Tanggal</label>
                    <input
                        id="tanggal_mulai"
                        type="date"
                        name="tanggal_mulai"
                        value="{{ $tanggalMulai }}"
                        class="rounded-xl border border-[#E5E7EB] bg-white px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-[11px] font-medium text-[#6B7280] mb-1">Sampai Tanggal</label>
                    <input
                        id="tanggal_selesai"
                        type="date"
                        name="tanggal_selesai"
                        value="{{ $tanggalSelesai }}"
                        class="rounded-xl border border-[#E5E7EB] bg-white px-3.5 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="rounded-full bg-[#0F0F10] text-white px-4 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                        Filter
                    </button>
                    @if ($idUser || $tanggalMulai !== '' || $tanggalSelesai !== '')
                        <a href="{{ route('log-aktivitas.index') }}"
                           class="rounded-full border border-[#D1D5DB] px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">Waktu</th>
                            <th class="pb-2 px-3 font-medium">Pengguna</th>
                            <th class="pb-2 px-3 font-medium">Role</th>
                            <th class="pb-2 pl-3 pr-4 font-medium">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($logs as $log)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl text-xs text-[#6B7280] whitespace-nowrap tabular-nums">
                                    {{ $log->waktu_aktivitas ? $log->waktu_aktivitas->format('d M Y, H:i:s') : '-' }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $log->user?->nama_lengkap ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-medium text-[#4B5563]">
                                        {{ ucfirst($log->user?->role ?? '-') }}
                                    </span>
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-[#111827]">
                                    {{ $log->aktivitas }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs text-[#6B7280]">
                                    Tidak ada data log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links('components.pagination') }}
        </section>
    </div>
@endsection
