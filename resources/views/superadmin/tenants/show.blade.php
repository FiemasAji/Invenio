@extends('layouts.app')

@section('title', 'Detail Tenant: ' . $tenant->name)

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                    {{ $tenant->name }}
                </h1>
                @if($tenant->is_active)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Aktif
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                        Non-Aktif
                    </span>
                @endif
            </div>
            <p class="text-xs text-[#575A5E] mt-1 font-mono">
                Slug: {{ $tenant->slug }} &bull; Terdaftar sejak {{ $tenant->created_at->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.tenants.edit', $tenant) }}" 
               class="px-4 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#08090A] transition">
                ✏️ Edit Informasi
            </a>
            <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A] px-2">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E]">Total User/Karyawan</div>
            <div class="text-xl font-bold text-[#08090A] mt-1">{{ $tenant->users_count }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E]">Admin Perusahaan</div>
            <div class="text-xl font-bold text-[#4E5340] mt-1">{{ $tenant->admins_count }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E]">Jumlah Site/Lokasi</div>
            <div class="text-xl font-bold text-[#697268] mt-1">{{ $tenant->sites_count }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E]">Item Aset Inventaris</div>
            <div class="text-xl font-bold text-[#B7D1DA] mt-1">{{ $tenant->assets_count }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Profil & Admin -->
        <div class="space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider mb-3 pb-2 border-b border-[#E2E8DD]">
                    Kontak & Alamat
                </h3>
                <dl class="text-xs space-y-2 text-[#575A5E]">
                    <div>
                        <dt class="font-medium text-[#A7A2A9]">Email:</dt>
                        <dd class="text-[#08090A] font-semibold">{{ $tenant->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-[#A7A2A9]">Telepon:</dt>
                        <dd class="text-[#08090A] font-semibold">{{ $tenant->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-[#A7A2A9]">Alamat:</dt>
                        <dd class="text-[#08090A]">{{ $tenant->address ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Daftar Admin Tenant -->
            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider mb-3 pb-2 border-b border-[#E2E8DD]">
                    Akun Administrator Tenant
                </h3>
                <div class="space-y-3">
                    @forelse($admins as $admin)
                    <div class="p-3 rounded-lg bg-[#F4F7F5] border border-[#E2E8DD] text-xs">
                        <div class="font-bold text-[#222823]">{{ $admin->name }}</div>
                        <div class="text-[#575A5E] font-mono text-[11px]">{{ $admin->email }}</div>
                        <div class="text-[#95A3A4] text-[10px] mt-1">Jabatan: {{ $admin->position ?? 'Admin' }}</div>
                    </div>
                    @empty
                    <p class="text-xs text-[#95A3A4]">Belum ada akun admin.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Sites & Recent Assets -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sites -->
            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider mb-3 pb-2 border-b border-[#E2E8DD]">
                    Daftar Site / Lokasi Kantor ({{ $sites->count() }})
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($sites as $site)
                    <div class="p-3 rounded-lg bg-[#F4F7F5] border border-[#E2E8DD] text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#222823]">{{ $site->name }}</span>
                            <span class="font-mono text-[10px] font-semibold bg-[#E2E8DD] px-1.5 py-0.5 rounded">{{ $site->code }}</span>
                        </div>
                        <p class="text-[11px] text-[#575A5E] mt-1 truncate">{{ $site->address ?? 'Tidak ada alamat' }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-[#95A3A4] col-span-2">Belum ada site kantor yang dibuat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Assets -->
            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider mb-3 pb-2 border-b border-[#E2E8DD]">
                    Inventaris Terbaru
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-[#575A5E] uppercase border-b border-[#E2E8DD]">
                            <tr>
                                <th class="py-2">Kode Aset</th>
                                <th class="py-2">Nama Barang</th>
                                <th class="py-2">Site</th>
                                <th class="py-2">Pemegang</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8DD]">
                            @forelse($recentAssets as $asset)
                            <tr>
                                <td class="py-2.5 font-mono font-semibold text-[#4E5340]">{{ $asset->asset_code }}</td>
                                <td class="py-2.5 font-bold text-[#222823]">{{ $asset->name }}</td>
                                <td class="py-2.5 text-[#575A5E]">{{ $asset->site->name ?? '-' }}</td>
                                <td class="py-2.5 text-[#575A5E]">{{ $asset->user->name ?? '-' }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $asset->status_badge_class }}">
                                        {{ $asset->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-[#95A3A4]">Belum ada inventaris tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
