@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#08090A] tracking-tight">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="mt-1 text-sm text-[#575A5E]">
                    @if($user->isSuperAdmin())
                        Panel Kendali Utama Sistem Invenio (Akses Penuh Seluruh Tenant)
                    @elseif($user->isAdmin())
                        Panel Administrator Tenant: <strong class="text-[#222823]">{{ $user->tenant->name }}</strong>
                    @else
                        Panel Pengguna Tenant: <strong class="text-[#222823]">{{ $user->tenant->name }}</strong>
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
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#95A3A4] text-[#08090A] shadow-sm border border-[#B7D1DA]">
                        👤 Tingkat: User (Staff Tenant)
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tenant Context & Isolation Status Banner -->
    <div class="mb-8 bg-white rounded-2xl border border-[#E2E8DD] p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-[#B7D1DA]/30 flex items-center justify-center text-[#4E5340] font-bold text-lg flex-shrink-0 border border-[#B7D1DA]/50">
                🔒
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-[#222823] uppercase tracking-wide">
                    Status Isolasi Data Multi-Tenant
                </h3>
                <p class="mt-1 text-xs text-[#575A5E] leading-relaxed">
                    @if($user->isSuperAdmin())
                        Anda masuk sebagai <strong>Super Admin</strong>. Anda memiliki otorisasi penuh untuk mengelola pendaftaran tenant perusahaan dan memantau seluruh sistem.
                    @else
                        Akun Anda terikat secara eksklusif ke tenant <strong class="text-[#222823]">{{ $user->tenant->name }}</strong> (ID: {{ $user->tenant->id }}). 
                        Semua akses data, inventaris aset, dan manajemen pengguna terisolasi sepenuhnya dan terkunci pada tenant ini.
                    @endif
                </p>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                        Email: <strong class="ml-1 text-[#08090A]">{{ $user->email }}</strong>
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                        Status Akun: <strong class="ml-1 text-emerald-700">Aktif</strong>
                    </span>
                    @if($user->tenant)
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                        Tenant Slug: <strong class="ml-1 text-[#08090A]">{{ $user->tenant->slug }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Empty Dashboard Canvas (Placeholder sesuai instruksi: "dashboard kosongan, menu menu ntar belakangan") -->
    <div class="bg-white rounded-2xl border-2 border-dashed border-[#95A3A4]/40 p-12 text-center shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-[#F4F7F5] flex items-center justify-center mx-auto text-2xl text-[#95A3A4] border border-[#E2E8DD] mb-4">
            📊
        </div>
        <h3 class="text-lg font-bold text-[#222823]">
            Dashboard Kosong (Siap untuk Pengembangan Fitur)
        </h3>
        <p class="mt-1 text-xs text-[#575A5E] max-w-md mx-auto">
            Fondasi autentikasi, isolasi data multi-tenant, palet tema kustom, dan font Montserrat telah aktif dengan sempurna. Menu-menu modul IT Asset Management dapat ditambahkan pada tahap selanjutnya.
        </p>

        <div class="mt-8 flex justify-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Laravel 12
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                <span class="w-2 h-2 rounded-full bg-[#4E5340]"></span> PostgreSQL 17
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                <span class="w-2 h-2 rounded-full bg-[#B7D1DA]"></span> Tailwind CSS v4
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                <span class="w-2 h-2 rounded-full bg-[#697268]"></span> Montserrat Font
            </span>
        </div>
    </div>

</div>
@endsection
