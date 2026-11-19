@extends('layouts.app')

@section('title', 'Master Sites & Lokasi')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Master Site & Lokasi Kantor
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Daftar kantor cabang, gedung, atau lokasi penempatan aset inventaris perusahaan.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.sites.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Tambah Site Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.sites.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama site, kode site, atau alamat..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] text-white hover:bg-[#4E5340] transition">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('admin.sites.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Sites Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Kode Site</th>
                    <th class="px-5 py-3.5">Nama Lokasi / Kantor</th>
                    <th class="px-5 py-3.5">Alamat</th>
                    <th class="px-5 py-3.5 text-center">Karyawan</th>
                    <th class="px-5 py-3.5 text-center">Aset</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                @forelse($sites as $site)
                <tr class="hover:bg-[#F4F7F5]/50 transition">
                    <td class="px-5 py-3.5 font-mono font-bold text-[#4E5340]">
                        {{ $site->code }}
                    </td>
                    <td class="px-5 py-3.5 font-bold text-[#222823]">
                        {{ $site->name }}
                        @if($site->phone)
                            <div class="text-[10px] text-[#A7A2A9] font-normal">📞 {{ $site->phone }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-[#575A5E] max-w-xs truncate">
                        {{ $site->address ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-center font-semibold text-[#575A5E]">
                        {{ $site->users_count }}
                    </td>
                    <td class="px-5 py-3.5 text-center font-semibold text-[#4E5340]">
                        {{ $site->assets_count }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($site->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                Non-Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('admin.sites.edit', $site) }}" 
                               class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] font-semibold text-[11px]">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.sites.destroy', $site) }}" 
                                  onsubmit="return confirm('Hapus site {{ $site->name }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-[11px] border border-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-[#95A3A4]">
                        Belum ada data site kantor yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($sites->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $sites->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
