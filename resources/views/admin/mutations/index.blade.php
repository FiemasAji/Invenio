@extends('layouts.app')

@section('title', 'Audit Log & Riwayat Mutasi Barang')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Audit Log & Riwayat Mutasi Barang
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Catatan kronologis seluruh perpindahan aset antar lokasi site dan perubahan pengguna/karyawan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.assets.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-white border border-[#95A3A4]/40 hover:bg-[#F4F7F5] text-[#222823] shadow-sm transition">
                <span>📦</span> Daftar Aset
            </a>
            <a href="{{ route('admin.mutations.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Catat Mutasi Baru
            </a>
        </div>
    </div>

    <!-- Filter & Date Range (c3) -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.mutations.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Cari Aset</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Nama atau kode aset..."
                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Dari Tanggal</label>
                <input type="date" 
                       name="date_from" 
                       value="{{ request('date_from') }}" 
                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Sampai Tanggal</label>
                <input type="date" 
                       name="date_to" 
                       value="{{ request('date_to') }}" 
                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] hover:bg-[#4E5340] text-white shadow-sm transition">
                    Filter Log
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to']))
                    <a href="{{ route('admin.mutations.index') }}" 
                       class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:text-[#08090A]">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mutations Audit Table (c3) -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Tanggal Mutasi</th>
                    <th class="px-5 py-3.5">Barang / Aset</th>
                    <th class="px-5 py-3.5">Perpindahan Lokasi (Site)</th>
                    <th class="px-5 py-3.5">Perpindahan Pengguna (User)</th>
                    <th class="px-5 py-3.5">Keterangan / Alasan</th>
                    <th class="px-5 py-3.5 text-right">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                @forelse($mutations as $mutation)
                <tr class="hover:bg-[#F4F7F5]/50 transition">
                    <!-- Tanggal Mutasi -->
                    <td class="px-5 py-3.5 font-semibold text-[#4E5340] whitespace-nowrap">
                        <span class="text-sm font-bold block">{{ $mutation->mutation_date ? $mutation->mutation_date->format('d M Y') : '-' }}</span>
                        <span class="text-[10px] text-[#A7A2A9]">{{ $mutation->created_at->format('H:i') }} WIB</span>
                    </td>

                    <!-- Aset -->
                    <td class="px-5 py-3.5">
                        @if($mutation->asset)
                            <a href="{{ route('admin.assets.show', $mutation->asset) }}" 
                               class="font-bold text-[#222823] hover:underline hover:text-[#4E5340] block">
                                {{ $mutation->asset->name }}
                            </a>
                            <div class="font-mono text-[10px] text-[#4E5340] font-bold">
                                {{ $mutation->asset->asset_code }}
                            </div>
                        @else
                            <span class="text-[#A7A2A9] italic">Aset telah dihapus</span>
                        @endif
                    </td>

                    <!-- Site: Dari -> Ke -->
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="text-[#575A5E]">
                                {{ $mutation->fromSite->name ?? 'Gudang Pusat' }}
                            </span>
                            <span class="text-[#95A3A4] font-bold">&rarr;</span>
                            <span class="font-bold text-[#222823] bg-[#B7D1DA]/30 px-2 py-0.5 rounded">
                                {{ $mutation->toSite->name ?? 'Gudang Pusat' }}
                            </span>
                        </div>
                    </td>

                    <!-- User: Dari -> Ke -->
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#575A5E]">
                                {{ $mutation->fromUser->name ?? '-' }}
                            </span>
                            <span class="text-[#95A3A4] font-bold">&rarr;</span>
                            <span class="font-bold text-[#08090A] bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded">
                                {{ $mutation->toUser->name ?? 'Gudang / Stock' }}
                            </span>
                        </div>
                    </td>

                    <!-- Keterangan -->
                    <td class="px-5 py-3.5 text-[#575A5E] max-w-sm">
                        <p class="line-clamp-2 leading-relaxed">{{ $mutation->notes }}</p>
                    </td>

                    <!-- Operator -->
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <div class="font-semibold text-[#222823]">{{ $mutation->creator->name ?? 'System' }}</div>
                        <div class="text-[10px] text-[#A7A2A9]">{{ $mutation->creator->role ?? 'admin' }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-[#95A3A4]">
                        <div class="text-3xl mb-2">🔄</div>
                        <div class="font-bold text-[#575A5E]">Belum ada riwayat mutasi yang tercatat.</div>
                        <div class="text-[11px] mt-1">Gunakan tombol "Catat Mutasi Baru" untuk mencatat pemindahan lokasi atau pergantian user.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($mutations->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $mutations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
