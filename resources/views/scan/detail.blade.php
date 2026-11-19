<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $asset->asset_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F4F7F5;
            color: #08090A;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 flex flex-col justify-between">

    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Top Header / App Brand -->
        <div class="text-center">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-[#4E5340] text-white flex items-center justify-center font-black text-sm">
                    IN
                </div>
                <span class="text-lg font-black tracking-wider text-[#08090A]">INVENIO</span>
            </div>
            <p class="text-[11px] text-[#575A5E]">
                Sistem Manajemen Inventaris & Aset TI Multi-Tenant
            </p>
        </div>

        <!-- Main Asset Verification Card -->
        <div class="bg-white rounded-3xl border border-[#E2E8DD] shadow-lg p-6 space-y-5">

            <!-- Verified Badge -->
            <div class="flex items-center justify-between pb-4 border-b border-[#E2E8DD]">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                        ✓
                    </span>
                    <div>
                        <div class="text-[11px] font-extrabold text-[#222823] uppercase tracking-wide">Aset Terverifikasi</div>
                        <div class="text-[10px] text-[#575A5E]">{{ $asset->tenant->name ?? 'Perusahaan' }}</div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $asset->status_badge_class }}">
                    {{ $asset->status_label }}
                </span>
            </div>

            <!-- Asset Core Info -->
            <div class="text-center space-y-1">
                <div class="font-mono text-xs font-bold text-[#4E5340] bg-[#4E5340]/10 px-3 py-1 rounded-lg inline-block">
                    🏷️ {{ $asset->asset_code }}
                </div>
                <h1 class="text-xl font-extrabold text-[#08090A] tracking-tight pt-1">
                    {{ $asset->name }}
                </h1>
                <p class="text-xs text-[#575A5E]">
                    {{ $asset->type->category->name ?? 'Aset' }} &bull; {{ $asset->type->name ?? 'Umum' }}
                </p>
            </div>

            <!-- Quick Info Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs bg-[#F4F7F5] p-3.5 rounded-2xl border border-[#E2E8DD]">
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#575A5E] block">Nomor Seri</span>
                    <span class="font-mono font-bold text-[#08090A]">{{ $asset->serial_number ?: 'Tanpa SN' }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#575A5E] block">Tanggal Perolehan</span>
                    <span class="font-semibold text-[#222823]">{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : '-' }}</span>
                </div>
            </div>

            <!-- Lokasi & Pemegang -->
            <div class="space-y-3 text-xs pt-1">
                <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-[#E2E8DD]">
                    <span class="text-lg">📍</span>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-[#575A5E]">Lokasi Penempatan (Site)</div>
                        <div class="font-bold text-[#222823]">{{ $asset->site->name ?? 'Kantor Pusat / Gudang' }}</div>
                        @if($asset->site && $asset->site->address)
                            <div class="text-[11px] text-[#575A5E] mt-0.5">{{ $asset->site->address }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-[#E2E8DD]">
                    <span class="text-lg">👤</span>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-[#575A5E]">Pemegang / Pengguna Aset</div>
                        <div class="font-bold text-[#222823]">{{ $asset->user->name ?? 'Tersedia di Gudang (Belum Diberikan)' }}</div>
                        @if($asset->user)
                            <div class="text-[11px] text-[#575A5E]">
                                {{ $asset->user->position ?: 'Karyawan' }} {{ $asset->user->employee_id ? "({$asset->user->employee_id})" : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Technical Specifications (b3) -->
            @if(!empty($asset->specs) && is_array($asset->specs))
            <div class="pt-2 border-t border-[#E2E8DD]">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wide mb-2 flex items-center gap-1.5">
                    <span>⚙️</span> Spesifikasi Teknis Unit
                </h3>
                <div class="space-y-1.5 text-xs">
                    @foreach($asset->specs as $paramName => $paramVal)
                        @if($paramVal !== '' && $paramVal !== null)
                            <div class="flex justify-between py-1 border-b border-[#E2E8DD]/60">
                                <span class="text-[#575A5E]">{{ $paramName }}</span>
                                <span class="font-mono font-bold text-[#08090A]">{{ $paramVal }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($asset->notes)
            <div class="pt-2 border-t border-[#E2E8DD]">
                <h3 class="text-[11px] font-bold text-[#575A5E] uppercase tracking-wide mb-1">Catatan Tambahan</h3>
                <p class="text-xs text-[#575A5E] bg-[#F4F7F5] p-2.5 rounded-xl border border-[#E2E8DD] whitespace-pre-line">
                    {{ $asset->notes }}
                </p>
            </div>
            @endif

        </div>

        <!-- Action / Login Footer -->
        <div class="text-center pt-2">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center w-full py-2.5 rounded-xl text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                    &larr; Masuk ke Dashboard Invenio
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center w-full py-2.5 rounded-xl text-xs font-semibold bg-white border border-[#E2E8DD] text-[#222823] hover:bg-[#F4F7F5] shadow-sm transition">
                    Masuk ke Akun Invenio
                </a>
            @endauth
            <p class="text-[10px] text-[#A7A2A9] mt-3">
                &copy; {{ date('Y') }} Invenio IT Asset Management &bull; Multi-Tenant Architecture
            </p>
        </div>

    </div>

</body>
</html>
