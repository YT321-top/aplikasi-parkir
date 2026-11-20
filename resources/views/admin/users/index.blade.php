@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">User</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Kelola akun admin, petugas, dan owner</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="rounded-full bg-[#0F0F10] text-white px-5 py-2.5 text-xs font-medium hover:bg-[#262626] transition-colors shadow-pill">
                + Tambah User
            </a>
        </div>

        <section class="pk-card bg-white rounded-[24px] p-6 shadow-card">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau username"
                    class="w-full sm:w-64 rounded-xl border border-[#E5E7EB] px-4 py-2 text-xs text-[#111827] placeholder-[#9CA3AF] focus:border-[#0F0F10] focus:outline-none"
                >
                <select
                    name="role"
                    class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs text-[#111827] focus:border-[#0F0F10] focus:outline-none"
                >
                    <option value="">Semua Role</option>
                    <option value="admin" @selected($role === 'admin')>Admin</option>
                    <option value="petugas" @selected($role === 'petugas')>Petugas</option>
                    <option value="owner" @selected($role === 'owner')>Owner</option>
                </select>
                <button type="submit"
                        class="rounded-full bg-[#0F0F10] text-white px-4 py-2 text-xs font-medium hover:bg-[#262626] transition-colors">
                    Filter
                </button>
                @if ($search !== '' || $role !== '')
                    <a href="{{ route('users.index') }}"
                       class="rounded-full border border-[#D1D5DB] px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2 text-left">
                    <thead>
                        <tr class="text-xs font-medium text-[#6B7280]">
                            <th class="pb-2 pl-4 pr-3 font-medium">Nama Lengkap</th>
                            <th class="pb-2 px-3 font-medium">Username</th>
                            <th class="pb-2 px-3 font-medium">Role</th>
                            <th class="pb-2 px-3 font-medium">Status</th>
                            <th class="pb-2 pl-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($users as $item)
                            <tr class="bg-[#FAFAFA] hover:bg-[#F3F4F6] transition-colors">
                                <td class="py-3.5 pl-4 pr-3 rounded-l-2xl font-semibold text-[#111827] whitespace-nowrap">
                                    {{ $item->nama_lengkap }}
                                </td>
                                <td class="py-3.5 px-3 text-[#4B5563] whitespace-nowrap">
                                    {{ '@' . $item->username }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full bg-[#F3F4F6] px-3 py-1 text-xs font-medium text-[#111827]">
                                        {{ ucfirst($item->role) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ (int) $item->status_aktif === 1 ? 'bg-[#0F0F10] text-white' : 'bg-[#F3F4F6] text-[#6B7280]' }}">
                                        {{ (int) $item->status_aktif === 1 ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 pl-3 pr-4 rounded-r-2xl text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('users.edit', $item) }}"
                                           class="rounded-full border border-[#D1D5DB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                                            Edit
                                        </a>
                                        @if ($item->id_user !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $item) }}"
                                                  onsubmit="return confirm('Hapus user {{ $item->username }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-full border border-[#E5E7EB] bg-white px-3.5 py-1.5 text-xs font-medium text-[#6B7280] hover:border-[#DC2626] hover:text-[#DC2626] transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-[#6B7280]">
                                    Data user tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links('components.pagination') }}
        </section>
    </div>
@endsection
