@extends('layouts.app')

@section('title', 'Kelola Tenant')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Manajemen Perusahaan Tenant
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Kelola pendaftaran perusahaan dan akun administrator tenant di Invenio.
            </p>
        </div>
        <div>
            <a href="{{ route('superadmin.tenants.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>➕</span> Tambah Tenant Baru
            </a>
        </div>
    </div>

    <!-- Overview Metrik (a2) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E] font-medium">Total Tenant</div>
            <div class="text-2xl font-extrabold text-[#08090A] mt-1">{{ $metrics['total_tenants'] }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E] font-medium">Tenant Aktif</div>
            <div class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $metrics['active_tenants'] }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E] font-medium">Total Akun Admin</div>
            <div class="text-2xl font-extrabold text-[#4E5340] mt-1">{{ $metrics['total_admins'] }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8DD] shadow-sm">
            <div class="text-xs text-[#575A5E] font-medium">Total Pengguna Sistem</div>
            <div class="text-2xl font-extrabold text-[#697268] mt-1">{{ $metrics['total_users'] }}</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] mb-6 shadow-sm">
        <form method="GET" action="{{ route('superadmin.tenants.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama tenant, slug, atau email..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] focus:ring-1 focus:ring-[#B7D1DA]">
            </div>
            <div class="w-full sm:w-44">
                <select name="status" class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] text-[#08090A]">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] text-white hover:bg-[#4E5340] transition">
                    Filter
                </button>
                <a href="{{ route('superadmin.tenants.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:bg-gray-100 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tenants Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Perusahaan Tenant</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5 text-center">Sites</th>
                        <th class="px-5 py-3.5 text-center">User</th>
                        <th class="px-5 py-3.5 text-center">Aset</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-[#F4F7F5]/50 transition">
                        <td class="px-5 py-4">
                            <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="font-bold text-[#222823] hover:text-[#4E5340] text-sm block">
                                {{ $tenant->name }}
                            </a>
                            <span class="text-[11px] text-[#95A3A4] font-mono">slug: {{ $tenant->slug }}</span>
                        </td>
                        <td class="px-5 py-4 text-[#575A5E]">
                            <div>{{ $tenant->email ?? '-' }}</div>
                            <div class="text-[11px] text-[#A7A2A9]">{{ $tenant->phone ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 text-center font-semibold text-[#575A5E]">
                            {{ $tenant->sites_count }}
                        </td>
                        <td class="px-5 py-4 text-center font-semibold text-[#575A5E]">
                            {{ $tenant->users_count }}
                        </td>
                        <td class="px-5 py-4 text-center font-semibold text-[#4E5340]">
                            {{ $tenant->assets_count }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($tenant->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                    Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" 
                                   class="px-2.5 py-1 rounded bg-[#B7D1DA]/30 hover:bg-[#B7D1DA]/60 text-[#222823] font-semibold text-[11px]">
                                    Detail
                                </a>
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" 
                                   class="px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] font-semibold text-[11px]">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" 
                                      onsubmit="return confirm('PERINGATAN: Menghapus tenant ini akan MENGHAPUS SEMUA user, site, dan data inventaris terkait! Yakin hapus?');" class="inline">
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
                        <td colspan="7" class="px-5 py-8 text-center text-xs text-[#95A3A4]">
                            Belum ada tenant terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="px-5 py-3 border-t border-[#E2E8DD] bg-[#F4F7F5]">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
