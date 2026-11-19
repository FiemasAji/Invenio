@extends('layouts.app')

@section('title', 'Master Pengguna & Karyawan')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Master Pengguna & Karyawan
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Daftar staf dan administrator perusahaan beserta ID karyawan, site penempatan, dan kode khusus.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, email, ID karyawan, jabatan, kode..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>
            <div>
                <select name="role" class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Tenant</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User (Staff)</option>
                </select>
            </div>
            <div>
                <select name="site_id" class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    <option value="">Semua Site Penempatan</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }} ({{ $site->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-4 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] text-white hover:bg-[#4E5340] transition">
                    Filter Data
                </button>
                @if(request()->hasAny(['search', 'role', 'site_id']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode / ID Karyawan</th>
                        <th class="px-5 py-3.5">Nama & Email</th>
                        <th class="px-5 py-3.5">Jabatan / Kontak</th>
                        <th class="px-5 py-3.5">Site Penempatan</th>
                        <th class="px-5 py-3.5 text-center">Peran</th>
                        <th class="px-5 py-3.5 text-center">Aset Digunakan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#F4F7F5]/50 transition">
                        <td class="px-5 py-3.5 font-mono">
                            <span class="font-bold text-[#4E5340] block">{{ $user->custom_code ?? '-' }}</span>
                            <span class="text-[10px] text-[#A7A2A9]">NIP: {{ $user->employee_id ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-[#222823]">
                            {{ $user->name }}
                            <div class="text-[11px] text-[#575A5E] font-normal font-mono">{{ $user->email }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-[#575A5E]">
                            <div class="font-semibold text-[#08090A]">{{ $user->position ?? 'Staff' }}</div>
                            <div class="text-[11px] text-[#A7A2A9]">📞 {{ $user->phone ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-[#575A5E]">
                            @if($user->site)
                                <span class="inline-flex items-center gap-1 font-semibold text-[#222823]">
                                    🏢 {{ $user->site->name }}
                                </span>
                            @else
                                <span class="text-[#A7A2A9] italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($user->isAdmin())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#697268]/20 text-[#222823] border border-[#697268]/40">
                                    Admin Tenant
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E2E8DD] text-[#575A5E]">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center font-bold text-[#4E5340]">
                            {{ $user->assets_count }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] font-semibold text-[11px]">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                      onsubmit="return confirm('Hapus akun {{ $user->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-[11px] border border-red-200">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-[#95A3A4]">
                            Belum ada pengguna / karyawan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
