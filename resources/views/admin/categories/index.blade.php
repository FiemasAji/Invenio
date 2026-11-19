@extends('layouts.app')

@section('title', 'Master Kategori Barang')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Master Kategori Barang
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Kategori utama inventaris (misal: Komputer, Jaringan, Perabot Kantor, dll).
            </p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Tambah Kategori Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama kategori atau kode..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] text-white hover:bg-[#4E5340] transition">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Kode Kategori</th>
                    <th class="px-5 py-3.5">Nama Kategori</th>
                    <th class="px-5 py-3.5">Deskripsi</th>
                    <th class="px-5 py-3.5 text-center">Jumlah Jenis Barang</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                @forelse($categories as $category)
                <tr class="hover:bg-[#F4F7F5]/50 transition">
                    <td class="px-5 py-3.5 font-mono font-bold text-[#4E5340]">
                        {{ $category->code }}
                    </td>
                    <td class="px-5 py-3.5 font-bold text-[#222823]">
                        {{ $category->name }}
                    </td>
                    <td class="px-5 py-3.5 text-[#575A5E] max-w-sm truncate">
                        {{ $category->description ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-center font-bold text-[#697268]">
                        {{ $category->types_count }} jenis
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('admin.types.index', ['category_id' => $category->id]) }}" 
                               class="px-2.5 py-1 rounded bg-[#B7D1DA]/30 hover:bg-[#B7D1DA]/60 text-[#222823] font-semibold text-[11px]">
                                Lihat Jenis
                            </a>
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] font-semibold text-[11px]">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                  onsubmit="return confirm('Hapus kategori {{ $category->name }}?');" class="inline">
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
                    <td colspan="5" class="px-5 py-8 text-center text-[#95A3A4]">
                        Belum ada kategori barang inventaris.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
