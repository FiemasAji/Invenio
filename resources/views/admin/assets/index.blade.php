@extends('layouts.app')

@section('title', 'Kelola Inventaris Aset')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Kelola Inventaris Aset (IT Asset Management)
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Katalogisasi, kepemilikan karyawan, lokasi site, mutasi fisik, dan pelabelan QR Code aset.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mutations.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-bold bg-white border border-[#95A3A4]/40 hover:bg-[#F4F7F5] text-[#222823] shadow-sm transition">
                <span>🔄</span> Log Mutasi
            </a>
            <a href="{{ route('admin.assets.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Registrasi Aset Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (c4 Overview) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#4E5340]/10 flex items-center justify-center text-lg text-[#4E5340]">
                📦
            </div>
            <div>
                <p class="text-[11px] font-semibold text-[#575A5E]">Total Aset</p>
                <p class="text-xl font-extrabold text-[#08090A]">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-lg text-blue-600">
                👤
            </div>
            <div>
                <p class="text-[11px] font-semibold text-[#575A5E]">Sedang Dipakai</p>
                <p class="text-xl font-extrabold text-[#08090A]">{{ number_format($stats['in_use']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-lg text-emerald-600">
                🏢
            </div>
            <div>
                <p class="text-[11px] font-semibold text-[#575A5E]">Tersedia di Gudang</p>
                <p class="text-xl font-extrabold text-[#08090A]">{{ number_format($stats['in_stock']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-lg text-amber-600">
                🛠️
            </div>
            <div>
                <p class="text-[11px] font-semibold text-[#575A5E]">Perbaikan / Service</p>
                <p class="text-xl font-extrabold text-[#08090A]">{{ number_format($stats['maintenance']) }}</p>
            </div>
        </div>
    </div>

    <!-- Multi-Filter Bar (c4) -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.assets.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- 1. Search Keyword -->
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Cari Aset</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nama, kode aset, no seri..."
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>

                <!-- 2. Status -->
                <div>
                    <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">Semua Status</option>
                        <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>Dipakai</option>
                        <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>Tersedia (Stock)</option>
                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>Non-Aktif / Rusak</option>
                    </select>
                </div>

                <!-- 3. Site -->
                <div>
                    <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Lokasi Site</label>
                    <select name="site_id" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">Semua Site</option>
                        @foreach($sites as $st)
                            <option value="{{ $st->id }}" {{ request('site_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Karyawan / Pemegang -->
                <div>
                    <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Pemegang Aset</label>
                    <select name="user_id" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">Semua Pemegang</option>
                        @foreach($users as $usr)
                            <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>
                                {{ $usr->name }} {{ $usr->employee_id ? "({$usr->employee_id})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Kategori -->
                <div>
                    <label class="block text-[10px] font-semibold text-[#575A5E] uppercase mb-1">Kategori</label>
                    <select name="category_id" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-[#E2E8DD]">
                <div class="text-[11px] text-[#575A5E]">
                    Menampilkan <strong>{{ $assets->total() }}</strong> total data inventaris
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'status', 'site_id', 'user_id', 'category_id', 'type_id']))
                        <a href="{{ route('admin.assets.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:text-[#08090A]">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-[#222823] hover:bg-[#4E5340] text-white shadow-sm transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Assets Table (c1, c4) -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Kode Aset</th>
                    <th class="px-5 py-3.5">Nama & Nomor Seri</th>
                    <th class="px-5 py-3.5">Kategori / Jenis</th>
                    <th class="px-5 py-3.5">Lokasi Fisik (Site)</th>
                    <th class="px-5 py-3.5">Pemegang (User)</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                @forelse($assets as $asset)
                <tr class="hover:bg-[#F4F7F5]/50 transition">
                    <!-- Kode Aset -->
                    <td class="px-5 py-3.5 font-mono font-bold text-[#4E5340]">
                        <a href="{{ route('admin.assets.show', $asset) }}" class="hover:underline flex items-center gap-1.5">
                            <span>🏷️</span> {{ $asset->asset_code }}
                        </a>
                    </td>

                    <!-- Nama & Seri -->
                    <td class="px-5 py-3.5">
                        <div class="font-bold text-[#222823]">{{ $asset->name }}</div>
                        <div class="text-[11px] text-[#575A5E] font-mono">
                            SN: {{ $asset->serial_number ?: 'Tanpa No Seri' }}
                        </div>
                    </td>

                    <!-- Kategori / Jenis -->
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                            {{ $asset->type->category->name ?? '-' }}
                        </span>
                        <div class="font-semibold text-[#222823] mt-0.5">
                            {{ $asset->type->name ?? '-' }}
                        </div>
                    </td>

                    <!-- Lokasi Site -->
                    <td class="px-5 py-3.5">
                        @if($asset->site)
                            <div class="font-semibold text-[#222823] flex items-center gap-1">
                                <span>📍</span> {{ $asset->site->name }}
                            </div>
                            <div class="text-[10px] text-[#A7A2A9] font-mono">{{ $asset->site->code }}</div>
                        @else
                            <span class="text-[#A7A2A9] italic">Tidak ditentukan</span>
                        @endif
                    </td>

                    <!-- Pemegang User -->
                    <td class="px-5 py-3.5">
                        @if($asset->user)
                            <div class="font-semibold text-[#222823] flex items-center gap-1">
                                <span>👤</span> {{ $asset->user->name }}
                            </div>
                            <div class="text-[10px] text-[#575A5E]">
                                {{ $asset->user->position ?: 'Karyawan' }} {{ $asset->user->employee_id ? "• {$asset->user->employee_id}" : '' }}
                            </div>
                        @else
                            <span class="text-[#A7A2A9] italic">Belum di-assign</span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-5 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $asset->status_badge_class }}">
                            {{ $asset->status_label }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-5 py-3.5 text-right">
                        <div class="inline-flex items-center gap-1">
                            <!-- Detail -->
                            <a href="{{ route('admin.assets.show', $asset) }}" 
                               title="Lihat Detail & QR"
                               class="p-1.5 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#222823]">
                                👁️
                            </a>

                            <!-- Mutasi Cepat -->
                            <a href="{{ route('admin.mutations.create', ['asset_id' => $asset->id]) }}" 
                               title="Catat Mutasi Lokasi / Pemegang"
                               class="p-1.5 rounded bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700">
                                🔄
                            </a>

                            <!-- Cetak Label QR -->
                            <a href="{{ route('admin.assets.print-label', $asset) }}" 
                               target="_blank"
                               title="Cetak Stiker Label QR"
                               class="p-1.5 rounded bg-[#B7D1DA]/30 border border-[#95A3A4]/40 hover:bg-[#B7D1DA]/60 text-[#222823]">
                                🏷️
                            </a>

                            <!-- Edit -->
                            <a href="{{ route('admin.assets.edit', $asset) }}" 
                               title="Edit Aset"
                               class="p-1.5 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E]">
                                ✏️
                            </a>

                            <!-- Hapus -->
                            <form method="POST" action="{{ route('admin.assets.destroy', $asset) }}" 
                                  onsubmit="return confirm('Hapus aset {{ $asset->name }} ({{ $asset->asset_code }})?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Hapus Aset"
                                        class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-700 border border-red-200">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-[#95A3A4]">
                        <div class="text-3xl mb-2">📦</div>
                        <div class="font-bold text-[#575A5E]">Belum ada inventaris aset yang ditemukan.</div>
                        <div class="text-[11px] mt-1">Daftarkan aset baru untuk mulai melacak inventaris dan mencetak QR Code.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($assets->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
