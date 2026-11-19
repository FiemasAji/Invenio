@extends('layouts.app')

@section('title', 'Inventaris Aset Saya')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Profile & Header Section -->
    <div class="bg-white rounded-2xl border border-[#E2E8DD] p-6 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#4E5340] text-white flex items-center justify-center font-extrabold text-xl shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#08090A] tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-[#575A5E] mt-1">
                        <span class="font-semibold text-[#222823]">{{ $user->position ?: 'Karyawan / Staff' }}</span>
                        @if($user->employee_id)
                            <span>&bull;</span>
                            <span>ID: <strong class="font-mono text-[#4E5340]">{{ $user->employee_id }}</strong></span>
                        @endif
                        @if($user->site)
                            <span>&bull;</span>
                            <span>Lokasi: <strong>{{ $user->site->name }}</strong> ({{ $user->site->code }})</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#B7D1DA]/30 text-[#222823] border border-[#95A3A4]/40">
                    📦 {{ $myAssets->total() }} Unit Aset Dipegang
                </span>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="w-full sm:max-w-md">
            <form method="GET" action="{{ route('user.my-assets') }}" class="flex gap-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama barang, kode aset, atau no seri..."
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                <button type="submit" 
                        class="px-4 py-2 rounded-lg text-xs font-bold bg-[#222823] hover:bg-[#4E5340] text-white transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('user.my-assets') }}" 
                       class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="text-xs text-[#575A5E]">
            Daftar inventaris resmi yang diamanahkan kepada Anda.
        </div>
    </div>

    <!-- Assets Grid View (Card layout for user friendliness) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($myAssets as $asset)
        <div class="bg-white rounded-2xl border border-[#E2E8DD] shadow-sm hover:shadow-md transition p-5 flex flex-col justify-between">
            <div>
                <!-- Top Badge: Category & Status -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F4F7F5] text-[#575A5E] border border-[#E2E8DD]">
                        {{ $asset->type->category->name ?? 'Aset IT' }} &bull; {{ $asset->type->name ?? 'Umum' }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $asset->status_badge_class }}">
                        {{ $asset->status_label }}
                    </span>
                </div>

                <!-- Asset Title & Code -->
                <h3 class="text-base font-extrabold text-[#08090A] tracking-tight mb-1">
                    {{ $asset->name }}
                </h3>
                <div class="font-mono text-xs font-bold text-[#4E5340] bg-[#4E5340]/10 px-2 py-0.5 rounded inline-block mb-3">
                    🏷️ {{ $asset->asset_code }}
                </div>

                <!-- Serial & Site -->
                <div class="space-y-1 text-xs text-[#575A5E] border-t border-[#E2E8DD] pt-3 mb-3">
                    <div class="flex justify-between">
                        <span>Nomor Seri (SN):</span>
                        <strong class="font-mono text-[#08090A]">{{ $asset->serial_number ?: 'Tanpa SN' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Lokasi Site:</span>
                        <strong class="text-[#222823]">{{ $asset->site->name ?? 'Kantor Pusat' }}</strong>
                    </div>
                    @if($asset->purchase_date)
                    <div class="flex justify-between">
                        <span>Tanggal Perolehan:</span>
                        <span>{{ $asset->purchase_date->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Custom Technical Specs -->
                @if(!empty($asset->specs) && is_array($asset->specs))
                <div class="bg-[#F4F7F5] p-3 rounded-xl border border-[#E2E8DD] mb-3">
                    <div class="text-[10px] uppercase font-bold text-[#575A5E] mb-1.5">Spesifikasi Unit:</div>
                    <div class="space-y-1 text-[11px]">
                        @foreach($asset->specs as $key => $val)
                            @if($val !== '' && $val !== null)
                                <div class="flex justify-between">
                                    <span class="text-[#575A5E]">{{ $key }}:</span>
                                    <span class="font-semibold text-[#08090A]">{{ $val }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Card Footer: QR Code & Scan Info -->
            <div class="pt-3 border-t border-[#E2E8DD] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 p-1 bg-white border border-[#E2E8DD] rounded-lg">
                        {!! $asset->getQrCodeSvg(32) !!}
                    </div>
                    <div class="text-[10px] text-[#575A5E]">
                        <div>Label QR Terpasang</div>
                        <a href="{{ route('assets.scan', $asset->qr_token) }}" target="_blank" class="text-[#4E5340] hover:underline font-semibold">
                            Cek Detail Publik &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-[#95A3A4]/40 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-[#F4F7F5] flex items-center justify-center mx-auto text-2xl text-[#95A3A4] mb-3">
                💻
            </div>
            <h3 class="text-base font-bold text-[#222823]">
                Belum Ada Aset yang Ditugaskan
            </h3>
            <p class="text-xs text-[#575A5E] mt-1 max-w-sm mx-auto">
                Saat ini belum ada perangkat atau barang inventaris IT yang tercatat dipegang oleh akun Anda. Hubungi Administrator IT jika ada unit yang perlu didaftarkan.
            </p>
        </div>
        @endforelse
    </div>

    @if($myAssets->hasPages())
    <div class="mt-6 bg-white p-4 rounded-2xl border border-[#E2E8DD] shadow-sm">
        {{ $myAssets->links() }}
    </div>
    @endif

</div>
@endsection
