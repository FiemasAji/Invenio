@extends('layouts.app')

@section('title', 'Master Jenis & Spesifikasi Barang')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Master Jenis Barang & Template Spesifikasi
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Kategori barang &rarr; Jenis barang (misal: Laptop) &rarr; Parameter spesifikasi dinamis (teks, angka, atau pilihan).
            </p>
        </div>
        <div>
            <a href="{{ route('admin.types.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Tambah Jenis Barang Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.types.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari jenis barang atau kode khusus..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>
            <div class="w-full sm:w-60">
                <select name="category_id" class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] text-white hover:bg-[#4E5340] transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id']))
            <a href="{{ route('admin.types.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Types Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode Khusus</th>
                        <th class="px-5 py-3.5">Jenis Barang</th>
                        <th class="px-5 py-3.5">Kategori Induk</th>
                        <th class="px-5 py-3.5">Parameter Spesifikasi Kustom</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Aset</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                    @forelse($types as $type)
                    <tr class="hover:bg-[#F4F7F5]/50 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-[#4E5340]">
                            {{ $type->code }}
                        </td>
                        <td class="px-5 py-3.5 font-bold text-[#222823]">
                            {{ $type->name }}
                            @if($type->description)
                                <div class="text-[10px] text-[#575A5E] font-normal truncate max-w-xs">{{ $type->description }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-[#575A5E]">
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#E2E8DD] text-[#222823] font-semibold">
                                {{ $type->category->name }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1 max-w-md">
                                @forelse($type->specFields as $field)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                                        <span class="font-bold text-[#222823]">{{ $field->name }}</span>
                                        <span class="text-[#95A3A4] font-mono">({{ $field->field_type }})</span>
                                    </span>
                                @empty
                                    <span class="text-[#A7A2A9] italic text-[11px]">Belum ada parameter kustom</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center font-bold text-[#4E5340]">
                            {{ $type->assets_count }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.types.edit', $type) }}" 
                                   class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] font-semibold text-[11px]">
                                    Edit Template
                                </a>
                                <form method="POST" action="{{ route('admin.types.destroy', $type) }}" 
                                      onsubmit="return confirm('Hapus jenis barang {{ $type->name }}?');" class="inline">
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
                        <td colspan="6" class="px-5 py-8 text-center text-[#95A3A4]">
                            Belum ada jenis barang dibuat. Silakan tambahkan jenis barang baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($types->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $types->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
