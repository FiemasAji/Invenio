@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#08090A] tracking-tight">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-[#575A5E]">
                @if($user->isSuperAdmin())
                    Panel Kendali Utama Multi-Tenant Invenio (Monitoring Global Seluruh Perusahaan)
                @elseif($user->isAdmin())
                    Panel Administrator Tenant: <strong class="text-[#222823]">{{ $user->tenant->name ?? 'Perusahaan' }}</strong>
                @else
                    Panel Pengguna & Inventaris Karyawan: <strong class="text-[#222823]">{{ $user->tenant->name ?? 'Perusahaan' }}</strong>
                @endif
            </p>
        </div>

        <!-- Role Badge -->
        <div>
            @if($user->isSuperAdmin())
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#4E5340] text-[#F4F7F5] shadow-sm border border-[#697268]">
                    🛡️ Tingkat: Super Admin
                </span>
            @elseif($user->isAdmin())
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#697268] text-[#F4F7F5] shadow-sm border border-[#95A3A4]">
                    👔 Tingkat: Tenant Admin (Pemilik Tenant)
                </span>
            @else
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#95A3A4]/30 text-[#222823] shadow-sm border border-[#B7D1DA]">
                    👤 Tingkat: User (Staff Tenant)
                </span>
            @endif
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════════
         1. SUPER ADMIN DASHBOARD (a2: Overview Tiap Tenant Aktif & Metrik Sistem)
         ════════════════════════════════════════════════════════════════════════════ -->
    @if($user->isSuperAdmin())
        <!-- Metrics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#4E5340]/10 flex items-center justify-center text-xl text-[#4E5340]">
                    🏢
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#575A5E]">Total Tenant</p>
                    <p class="text-2xl font-extrabold text-[#08090A]">{{ number_format($metrics['total_tenants'] ?? 0) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-xl text-emerald-600">
                    ✅
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#575A5E]">Tenant Aktif</p>
                    <p class="text-2xl font-extrabold text-[#08090A]">{{ number_format($metrics['active_tenants'] ?? 0) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-xl text-blue-600">
                    👔
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#575A5E]">Tenant Admin</p>
                    <p class="text-2xl font-extrabold text-[#08090A]">{{ number_format($metrics['total_admins'] ?? 0) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-xl text-purple-600">
                    👥
                </div>
                <div>
                    <p class="text-xs font-semibold text-[#575A5E]">Total Pengguna</p>
                    <p class="text-2xl font-extrabold text-[#08090A]">{{ number_format($metrics['total_users'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Tenant Overview Section -->
        <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-[#E2E8DD] flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider">
                        Overview Tenant Perusahaan Terdaftar (a2)
                    </h2>
                    <p class="text-xs text-[#575A5E] mt-0.5">
                        Pemantauan isolasi data, jumlah admin, karyawan, sites, dan inventaris aktif per tenant.
                    </p>
                </div>
                <a href="{{ route('superadmin.tenants.create') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                    <span>➕</span> Tambah Tenant & Admin
                </a>
            </div>

            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Nama Perusahaan</th>
                        <th class="px-5 py-3.5">Identifier (Slug)</th>
                        <th class="px-5 py-3.5 text-center">Admin</th>
                        <th class="px-5 py-3.5 text-center">Karyawan</th>
                        <th class="px-5 py-3.5 text-center">Sites</th>
                        <th class="px-5 py-3.5 text-center">Aset</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                    @forelse($recentTenants ?? [] as $tenant)
                    <tr class="hover:bg-[#F4F7F5]/50 transition">
                        <td class="px-5 py-3.5 font-bold text-[#222823]">
                            {{ $tenant->name }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[#575A5E]">
                            {{ $tenant->slug }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-[#08090A]">
                            {{ $tenant->admins_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-[#575A5E]">
                            {{ $tenant->users_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-[#575A5E]">
                            {{ $tenant->sites_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-[#4E5340]">
                            {{ $tenant->assets_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($tenant->is_active)
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
                            <a href="{{ route('superadmin.tenants.show', $tenant) }}" 
                               class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#222823] font-semibold text-[11px]">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-[#95A3A4]">
                            Belum ada tenant yang terdaftar dalam sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3 bg-[#F4F7F5] border-t border-[#E2E8DD] text-right">
                <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-semibold text-[#4E5340] hover:underline">
                    Lihat Seluruh Manajemen Tenant &rarr;
                </a>
            </div>
        </div>

    <!-- ════════════════════════════════════════════════════════════════════════════
         2. TENANT ADMIN DASHBOARD (c4 Overview & Quick Actions)
         ════════════════════════════════════════════════════════════════════════════ -->
    @elseif($user->isAdmin())
        <!-- Asset & Master Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-[#575A5E]">Total Aset</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['total_assets'] ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-blue-600">Dipakai</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['in_use_assets'] ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-emerald-600">Di Gudang (Stock)</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['in_stock_assets'] ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-amber-600">Perbaikan</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['maintenance'] ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-[#575A5E]">Karyawan</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['total_users'] ?? 0) }}</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
                <p class="text-[11px] font-semibold text-[#575A5E]">Sites Kantor</p>
                <p class="text-xl font-extrabold text-[#08090A] mt-1">{{ number_format($stats['total_sites'] ?? 0) }}</p>
            </div>
        </div>

        <!-- Quick Access Actions -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            <a href="{{ route('admin.assets.create') }}" 
               class="p-4 rounded-2xl bg-white border border-[#E2E8DD] hover:border-[#4E5340] shadow-sm transition flex items-center gap-3">
                <span class="text-2xl">➕</span>
                <div>
                    <h3 class="text-xs font-bold text-[#08090A]">Registrasi Aset</h3>
                    <p class="text-[10px] text-[#575A5E]">Daftarkan unit baru</p>
                </div>
            </a>

            <a href="{{ route('admin.mutations.create') }}" 
               class="p-4 rounded-2xl bg-white border border-[#E2E8DD] hover:border-blue-500 shadow-sm transition flex items-center gap-3">
                <span class="text-2xl">🔄</span>
                <div>
                    <h3 class="text-xs font-bold text-[#08090A]">Catat Mutasi</h3>
                    <p class="text-[10px] text-[#575A5E]">Pindah site / karyawan</p>
                </div>
            </a>

            <a href="{{ route('admin.assets.index') }}" 
               class="p-4 rounded-2xl bg-white border border-[#E2E8DD] hover:border-[#4E5340] shadow-sm transition flex items-center gap-3">
                <span class="text-2xl">🔍</span>
                <div>
                    <h3 class="text-xs font-bold text-[#08090A]">Overview & Filter</h3>
                    <p class="text-[10px] text-[#575A5E]">Katalog & cetak QR</p>
                </div>
            </a>

            <a href="{{ route('admin.types.create') }}" 
               class="p-4 rounded-2xl bg-white border border-[#E2E8DD] hover:border-[#4E5340] shadow-sm transition flex items-center gap-3">
                <span class="text-2xl">⚙️</span>
                <div>
                    <h3 class="text-xs font-bold text-[#08090A]">Template Barang</h3>
                    <p class="text-[10px] text-[#575A5E]">Kategori & parameter</p>
                </div>
            </a>
        </div>

        <!-- Two Column: Recent Assets & Recent Mutations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Assets -->
            <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#E2E8DD]">
                    <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider flex items-center gap-1.5">
                        <span>📦</span> Aset Baru Terdaftar
                    </h3>
                    <a href="{{ route('admin.assets.index') }}" class="text-[11px] font-semibold text-[#4E5340] hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="divide-y divide-[#E2E8DD]">
                    @forelse($recentAssets ?? [] as $asset)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <a href="{{ route('admin.assets.show', $asset) }}" class="font-bold text-xs text-[#08090A] hover:underline">
                                {{ $asset->name }}
                            </a>
                            <div class="flex items-center gap-2 text-[10px] text-[#575A5E] mt-0.5">
                                <span class="font-mono text-[#4E5340] font-bold">{{ $asset->asset_code }}</span>
                                <span>&bull;</span>
                                <span>{{ $asset->type->name ?? '-' }}</span>
                                <span>&bull;</span>
                                <span>{{ $asset->site->name ?? 'Gudang' }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $asset->status_badge_class }}">
                            {{ $asset->status_label }}
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-[#95A3A4] py-4 text-center italic">Belum ada aset terdaftar.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Mutations -->
            <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#E2E8DD]">
                    <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider flex items-center gap-1.5">
                        <span>🔄</span> Mutasi & Perpindahan Terkini (c3)
                    </h3>
                    <a href="{{ route('admin.mutations.index') }}" class="text-[11px] font-semibold text-[#4E5340] hover:underline">
                        Lihat Log &rarr;
                    </a>
                </div>

                <div class="divide-y divide-[#E2E8DD]">
                    @forelse($recentMutations ?? [] as $mut)
                    <div class="py-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#08090A]">{{ $mut->asset->name ?? 'Aset' }}</span>
                            <span class="text-[10px] text-[#575A5E] font-medium">{{ $mut->mutation_date ? $mut->mutation_date->format('d M Y') : '' }}</span>
                        </div>
                        <div class="text-[11px] text-[#575A5E] mt-1 flex items-center gap-1">
                            <span>📍 {{ $mut->toSite->name ?? 'Pusat' }}</span>
                            <span>&bull;</span>
                            <span>👤 {{ $mut->toUser->name ?? 'Gudang' }}</span>
                        </div>
                        <p class="text-[10px] text-[#A7A2A9] mt-0.5 truncate">{{ $mut->notes }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-[#95A3A4] py-4 text-center italic">Belum ada catatan mutasi.</p>
                    @endforelse
                </div>
            </div>

        </div>

    <!-- ════════════════════════════════════════════════════════════════════════════
         3. TENANT USER DASHBOARD (Personal Assigned Assets)
         ════════════════════════════════════════════════════════════════════════════ -->
    @else
        <!-- User Personal Assets Overview -->
        <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8DD]">
                <div>
                    <h2 class="text-base font-extrabold text-[#08090A] tracking-tight">
                        Inventaris yang Anda Pegang / Gunakan
                    </h2>
                    <p class="text-xs text-[#575A5E] mt-0.5">
                        Daftar barang inventaris resmi perusahaan yang saat ini diamanahkan kepada Anda.
                    </p>
                </div>
                <a href="{{ route('user.my-assets') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                    <span>📦</span> Buka Tampilan Lengkap Aset Saya
                </a>
            </div>

            <div class="mt-4 divide-y divide-[#E2E8DD]">
                @forelse($myAssets ?? [] as $asset)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-[#08090A]">{{ $asset->name }}</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#4E5340]/10 text-[#4E5340]">
                                {{ $asset->asset_code }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-[#575A5E] mt-1">
                            <span>Tipe: <strong>{{ $asset->type->name ?? '-' }}</strong></span>
                            <span>&bull;</span>
                            <span>SN: <strong class="font-mono">{{ $asset->serial_number ?: '-' }}</strong></span>
                            <span>&bull;</span>
                            <span>Lokasi: <strong>{{ $asset->site->name ?? '-' }}</strong></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $asset->status_badge_class }}">
                            {{ $asset->status_label }}
                        </span>
                        <a href="{{ route('assets.scan', $asset->qr_token) }}" target="_blank"
                           class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] text-[10px] font-semibold text-[#4E5340] hover:underline">
                            QR Detail
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-[#95A3A4]">
                    <p class="text-2xl mb-1">💻</p>
                    <p class="font-bold text-[#575A5E]">Tidak ada aset yang sedang ditugaskan ke akun Anda.</p>
                </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
