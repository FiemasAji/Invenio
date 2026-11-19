@extends('layouts.app')

@section('title', $asset->name . ' (' . $asset->asset_code . ')')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Top Navigation / Breadcrumb -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#575A5E] mb-1">
                <a href="{{ route('admin.assets.index') }}" class="hover:underline">Inventaris Aset</a>
                <span>&rarr;</span>
                <span class="font-mono text-[#4E5340] font-bold">{{ $asset->asset_code }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#08090A] tracking-tight">
                {{ $asset->name }}
            </h1>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Mutasi -->
            <a href="{{ route('admin.mutations.create', ['asset_id' => $asset->id]) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                <span>🔄</span> Catat Mutasi
            </a>

            <!-- Cetak Label QR (c2) -->
            <a href="{{ route('admin.assets.print-label', $asset) }}" 
               target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                <span>🏷️</span> Cetak Stiker QR
            </a>

            <!-- Edit -->
            <a href="{{ route('admin.assets.edit', $asset) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-white border border-[#95A3A4]/40 hover:bg-[#F4F7F5] text-[#222823] shadow-sm transition">
                <span>✏️</span> Edit
            </a>

            <!-- Hapus -->
            <form method="POST" action="{{ route('admin.assets.destroy', $asset) }}" 
                  onsubmit="return confirm('Hapus aset {{ $asset->name }} ({{ $asset->asset_code }}) secara permanen?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: QR Code & Current Placement -->
        <div class="space-y-6">

            <!-- QR Code Card (c2) -->
            <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border mb-4 {{ $asset->status_badge_class }}">
                    Status: {{ $asset->status_label }}
                </span>

                <div class="p-4 bg-white rounded-xl border border-[#E2E8DD] inline-block shadow-sm">
                    {!! $asset->getQrCodeSvg(180) !!}
                </div>

                <div class="mt-4">
                    <p class="font-mono text-sm font-extrabold text-[#4E5340] tracking-wider">{{ $asset->asset_code }}</p>
                    <p class="text-[11px] text-[#575A5E] mt-0.5">Scan lewat kamera ponsel untuk melihat informasi unit</p>
                </div>

                <div class="mt-4 pt-4 border-t border-[#E2E8DD] flex flex-col gap-2">
                    <a href="{{ route('admin.assets.print-label', $asset) }}" target="_blank"
                       class="w-full py-2 rounded-lg text-xs font-bold bg-[#F4F7F5] hover:bg-[#E2E8DD] text-[#222823] border border-[#E2E8DD] transition flex items-center justify-center gap-1.5">
                        <span>🖨️</span> Buka Halaman Cetak Label
                    </a>
                    <a href="{{ route('assets.scan', $asset->qr_token) }}" target="_blank"
                       class="text-[11px] text-[#4E5340] hover:underline flex items-center justify-center gap-1">
                        <span>🔗</span> Pratinjau Tampilan Scan Mobile &rarr;
                    </a>
                </div>
            </div>

            <!-- Lokasi Fisik & Pemegang Saat Ini (c1) -->
            <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD] flex items-center gap-1.5">
                    <span>📍</span> Lokasi & Pemegang Aset
                </h3>

                <!-- Site -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-[#575A5E]">Lokasi Penempatan (Site)</p>
                    @if($asset->site)
                        <div class="mt-1">
                            <p class="text-sm font-bold text-[#222823]">{{ $asset->site->name }}</p>
                            <p class="text-xs font-mono text-[#4E5340]">{{ $asset->site->code }}</p>
                            @if($asset->site->address)
                                <p class="text-xs text-[#575A5E] mt-1">{{ $asset->site->address }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-[#A7A2A9] italic mt-1">Belum dialokasikan ke site tertentu</p>
                    @endif
                </div>

                <!-- User -->
                <div class="pt-3 border-t border-[#E2E8DD]">
                    <p class="text-[10px] uppercase font-bold text-[#575A5E]">Penanggung Jawab / Pemakai</p>
                    @if($asset->user)
                        <div class="mt-1">
                            <p class="text-sm font-bold text-[#222823]">{{ $asset->user->name }}</p>
                            <p class="text-xs text-[#575A5E]">
                                {{ $asset->user->position ?: 'Karyawan' }}
                                @if($asset->user->employee_id)
                                    &bull; NIP: <span class="font-mono text-[#08090A]">{{ $asset->user->employee_id }}</span>
                                @endif
                            </p>
                            @if($asset->user->phone)
                                <p class="text-xs text-[#575A5E] mt-0.5">📞 {{ $asset->user->phone }}</p>
                            @endif
                            <p class="text-xs text-[#575A5E]">✉️ {{ $asset->user->email }}</p>
                        </div>
                    @else
                        <p class="text-xs text-[#A7A2A9] italic mt-1">Belum diserahkan ke pengguna (Tersedia di Gudang)</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Technical Details, Custom Specs, & Mutation Logs -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Data Teknis & Spesifikasi (b3 / c1) -->
            <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                    Spesifikasi & Informasi Detail Aset
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Kategori</span>
                        <span class="font-bold text-[#222823]">{{ $asset->type->category->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Jenis Barang</span>
                        <span class="font-bold text-[#222823]">{{ $asset->type->name ?? '-' }} ({{ $asset->type->code ?? '-' }})</span>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Nomor Seri (SN)</span>
                        <span class="font-mono font-bold text-[#08090A]">{{ $asset->serial_number ?: 'Tanpa Serial' }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Tanggal Perolehan</span>
                        <span class="font-semibold text-[#222823]">
                            {{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'Tidak tercatat' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Tanggal Didaftarkan</span>
                        <span class="font-semibold text-[#575A5E]">{{ $asset->created_at->format('d M Y H:i') }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-semibold text-[#575A5E] block">Update Terakhir</span>
                        <span class="font-semibold text-[#575A5E]">{{ $asset->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Custom Parameters Breakdown (b3) -->
                <div class="pt-4 border-t border-[#E2E8DD]">
                    <h4 class="text-xs font-bold text-[#222823] uppercase tracking-wide mb-3 flex items-center gap-1.5">
                        <span>⚙️</span> Parameter Spesifikasi Teknis (Custom Template)
                    </h4>

                    @if(!empty($asset->specs) && is_array($asset->specs))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($asset->specs as $key => $value)
                                <div class="p-3 rounded-xl bg-[#F4F7F5] border border-[#E2E8DD] flex items-center justify-between">
                                    <span class="text-xs font-semibold text-[#575A5E]">{{ $key }}</span>
                                    <span class="text-xs font-bold text-[#08090A] font-mono">{{ $value !== '' && $value !== null ? $value : '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-[#A7A2A9] italic">Tidak ada parameter spesifikasi khusus yang diisi.</p>
                    @endif
                </div>

                @if($asset->notes)
                <div class="pt-4 border-t border-[#E2E8DD]">
                    <h4 class="text-xs font-bold text-[#222823] uppercase tracking-wide mb-1">Catatan Tambahan</h4>
                    <p class="text-xs text-[#575A5E] bg-[#F4F7F5] p-3 rounded-xl border border-[#E2E8DD] whitespace-pre-line leading-relaxed">
                        {{ $asset->notes }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Audit Trail & Log Mutasi Barang (c3) -->
            <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-[#E2E8DD]">
                    <div>
                        <h3 class="text-xs font-bold text-[#222823] uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔄</span> Riwayat & Log Mutasi Barang
                        </h3>
                        <p class="text-[11px] text-[#575A5E]">
                            Jejak audit perpindahan lokasi site dan pergantian pemegang unit aset.
                        </p>
                    </div>
                    <a href="{{ route('admin.mutations.create', ['asset_id' => $asset->id]) }}" 
                       class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                        ➕ Catat Mutasi
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F4F7F5] text-[#575A5E] uppercase border-b border-[#E2E8DD] font-semibold">
                            <tr>
                                <th class="px-3 py-2.5">Tanggal</th>
                                <th class="px-3 py-2.5">Perpindahan Lokasi (Site)</th>
                                <th class="px-3 py-2.5">Perpindahan Pemegang (User)</th>
                                <th class="px-3 py-2.5">Catatan Mutasi</th>
                                <th class="px-3 py-2.5 text-right">Operator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8DD] text-[#08090A]">
                            @forelse($asset->mutations as $mutation)
                            <tr class="hover:bg-[#F4F7F5]/40 transition">
                                <td class="px-3 py-3 font-semibold text-[#4E5340] whitespace-nowrap">
                                    {{ $mutation->mutation_date ? $mutation->mutation_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-1 font-semibold">
                                        <span class="text-[#575A5E]">{{ $mutation->fromSite->name ?? 'Awal' }}</span>
                                        <span class="text-[#95A3A4]">&rarr;</span>
                                        <span class="text-[#08090A]">{{ $mutation->toSite->name ?? 'Gudang/Pusat' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-1">
                                        <span class="text-[#575A5E]">{{ $mutation->fromUser->name ?? '-' }}</span>
                                        <span class="text-[#95A3A4]">&rarr;</span>
                                        <span class="font-bold text-[#08090A]">{{ $mutation->toUser->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-[#575A5E] max-w-xs">
                                    {{ $mutation->notes }}
                                </td>
                                <td class="px-3 py-3 text-right text-[#A7A2A9] text-[11px] whitespace-nowrap">
                                    {{ $mutation->creator->name ?? 'System' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-[#95A3A4] italic">
                                    Belum ada catatan mutasi perpindahan untuk aset ini.
                                </td>
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
