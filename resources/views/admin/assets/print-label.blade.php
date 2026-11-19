<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR - {{ $asset->asset_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F4F7F5;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .label-card {
                box-shadow: none !important;
                border: 1.5px solid #08090A !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center justify-center min-h-screen">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print mb-6 flex items-center gap-3 bg-white p-3.5 rounded-xl border border-[#E2E8DD] shadow-sm">
        <button onclick="window.print()" 
                class="px-5 py-2 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm flex items-center gap-1.5 transition">
            <span>🖨️</span> Cetak Stiker Label
        </button>
        <button onclick="window.close()" 
                class="px-4 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] hover:bg-[#E2E8DD] text-[#575A5E] border border-[#E2E8DD] transition">
            Tutup Tab
        </button>
        <a href="{{ route('admin.assets.show', $asset) }}" 
           class="px-4 py-2 rounded-lg text-xs font-semibold text-[#4E5340] hover:underline">
            &larr; Kembali ke Detail
        </a>
    </div>

    <!-- Sticker Label Card (Optimized for label printers ~80mm x 50mm) -->
    <div class="label-card w-[380px] bg-white rounded-xl border-2 border-[#08090A] p-4 text-[#08090A] shadow-md">
        
        <!-- Header: Company & Asset Tag -->
        <div class="flex items-center justify-between border-b-2 border-[#08090A] pb-2 mb-3">
            <div>
                <span class="text-[9px] uppercase tracking-widest font-extrabold text-[#575A5E] block">PROPERTY OF</span>
                <span class="text-sm font-extrabold uppercase tracking-tight text-[#08090A]">
                    {{ $asset->tenant->name ?? 'INVENIO IT ASSET' }}
                </span>
            </div>
            <div class="text-right">
                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-[#08090A] text-white">
                    IT ASSET
                </span>
            </div>
        </div>

        <!-- Body: QR Code + Details -->
        <div class="flex items-center gap-3">
            <!-- QR Code (Crisp Vector SVG) -->
            <div class="p-1 bg-white border border-[#08090A] rounded flex-shrink-0">
                {!! $asset->getQrCodeSvg(110) !!}
            </div>

            <!-- Asset Info -->
            <div class="flex-1 min-w-0">
                <div class="text-[10px] text-[#575A5E] uppercase font-semibold">Asset Tag ID</div>
                <div class="font-mono text-sm font-black text-[#08090A] tracking-wider truncate">
                    {{ $asset->asset_code }}
                </div>

                <div class="mt-1">
                    <div class="text-[10px] text-[#575A5E] uppercase font-semibold">Nama Unit</div>
                    <div class="text-xs font-bold text-[#08090A] truncate">
                        {{ $asset->name }}
                    </div>
                </div>

                <div class="mt-1 flex items-center justify-between text-[10px]">
                    <div>
                        <span class="text-[#575A5E]">SN:</span>
                        <span class="font-mono font-semibold">{{ $asset->serial_number ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[#575A5E]">Site:</span>
                        <span class="font-bold">{{ $asset->site->code ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer: Scan notice -->
        <div class="mt-3 pt-1.5 border-t border-[#08090A]/30 flex items-center justify-between text-[8px] text-[#575A5E] font-medium">
            <span>DO NOT REMOVE THIS STICKER</span>
            <span class="font-mono">SCAN TO VERIFY</span>
        </div>

    </div>

    <!-- Print Tip for User -->
    <p class="no-print mt-4 text-[11px] text-[#575A5E] text-center max-w-sm">
        💡 <em>Petunjuk:</em> Pastikan opsi "Background graphics" dicentang pada dialog print browser untuk hasil cetak stiker yang optimal.
    </p>

</body>
</html>
