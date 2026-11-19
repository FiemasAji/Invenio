@extends('layouts.app')

@section('title', 'Catat Mutasi Barang')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Catat Mutasi Perpindahan Barang
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Catat pemindahan lokasi site fisik atau pergantian karyawan pemakai aset.
            </p>
        </div>
        <a href="{{ route('admin.mutations.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Riwayat Mutasi
        </a>
    </div>

    <form method="POST" action="{{ route('admin.mutations.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Pemilihan Barang / Aset -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                1. Pilih Aset yang Dimutasi
            </h2>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Aset Inventaris *</label>
                <select name="asset_id" id="assetSelect" required 
                        class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    <option value="">-- Pilih Aset Inventaris --</option>
                    @foreach($assets as $ast)
                        <option value="{{ $ast->id }}" 
                                data-site="{{ $ast->site->name ?? 'Gudang Pusat / Tidak Ada' }}"
                                data-site-id="{{ $ast->site_id }}"
                                data-user="{{ $ast->user->name ?? 'Tidak Ada Pemakai' }}"
                                data-user-id="{{ $ast->user_id }}"
                                data-status="{{ $ast->status }}"
                                {{ (old('asset_id') == $ast->id || (isset($selectedAsset) && $selectedAsset->id == $ast->id)) ? 'selected' : '' }}>
                            [{{ $ast->asset_code }}] {{ $ast->name }} - {{ $ast->type->name ?? '' }} (Saat ini: {{ $ast->site->name ?? 'Pusat' }} / {{ $ast->user->name ?? 'Gudang' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Posisi Saat Ini Banner -->
            <div id="currentInfoCard" class="p-4 rounded-xl bg-[#F4F7F5] border border-[#E2E8DD]">
                <h4 class="text-[11px] font-bold text-[#222823] uppercase tracking-wide mb-2">
                    Posisi Saat Ini Sebelum Mutasi:
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[#575A5E] block text-[10px] uppercase font-semibold">Lokasi Site Asal:</span>
                        <strong id="curSiteLabel" class="text-[#08090A]">
                            {{ $selectedAsset->site->name ?? 'Gudang Pusat / Tidak Ada' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-[#575A5E] block text-[10px] uppercase font-semibold">Pemegang Asal:</span>
                        <strong id="curUserLabel" class="text-[#08090A]">
                            {{ $selectedAsset->user->name ?? 'Tidak Ada Pemegang (Gudang)' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Tujuan Mutasi Baru -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                2. Tujuan Mutasi (Lokasi & Pemegang Baru)
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Pindahkan ke Lokasi Site Baru</label>
                    <select name="to_site_id" id="toSiteSelect" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Tetap / Tidak Diubah --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('to_site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }} ({{ $site->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Serahkan ke Karyawan / Pemegang Baru</label>
                    <select name="to_user_id" id="toUserSelect" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Masukkan ke Gudang / Tanpa Pemegang --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->employee_id ? "({$user->employee_id})" : '' }} - {{ $user->position ?: 'Staff' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Tanggal Mutasi *</label>
                    <input type="date" name="mutation_date" value="{{ old('mutation_date', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Baru Aset *</label>
                    <select name="status" id="newStatusSelect" required class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="in_use" {{ old('status', 'in_use') === 'in_use' ? 'selected' : '' }}>Sedang Dipakai Karyawan (In Use)</option>
                        <option value="in_stock" {{ old('status') === 'in_stock' ? 'selected' : '' }}>Tersedia di Gudang (In Stock)</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Dalam Perbaikan / Maintenance</option>
                        <option value="disposed" {{ old('status') === 'disposed' ? 'selected' : '' }}>Non-Aktif / Rusak / Dihapus</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Catatan & Alasan Perpindahan (c3) -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                3. Alasan / Keterangan Mutasi *
            </h2>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Keterangan Tambahan / Alasan Pemindahan *</label>
                <textarea name="notes" rows="3" required
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                          placeholder="Misal: Penyerahan laptop inventaris untuk onboarding karyawan baru divisi IT, atau mutasi dari kantor pusat ke cabang Surabaya.">{{ old('notes') }}</textarea>
                <p class="text-[10px] text-[#A7A2A9] mt-1">Catatan ini akan tersimpan permanen dalam audit trail riwayat aset.</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.mutations.index') }}" 
               class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:text-[#08090A]">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                Simpan & Catat Mutasi
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    const assetSelect = document.getElementById('assetSelect');
    const curSiteLabel = document.getElementById('curSiteLabel');
    const curUserLabel = document.getElementById('curUserLabel');
    const toSiteSelect = document.getElementById('toSiteSelect');
    const toUserSelect = document.getElementById('toUserSelect');
    const newStatusSelect = document.getElementById('newStatusSelect');

    function updateCurrentInfo() {
        const selected = assetSelect.options[assetSelect.selectedIndex];
        if (!selected || !selected.value) {
            curSiteLabel.textContent = '-';
            curUserLabel.textContent = '-';
            return;
        }

        const site = selected.getAttribute('data-site');
        const user = selected.getAttribute('data-user');
        const siteId = selected.getAttribute('data-site-id');
        const userId = selected.getAttribute('data-user-id');
        const status = selected.getAttribute('data-status');

        curSiteLabel.textContent = site;
        curUserLabel.textContent = user;

        if (siteId && !toSiteSelect.value) {
            toSiteSelect.value = siteId;
        }
    }

    assetSelect.addEventListener('change', updateCurrentInfo);

    // Auto set status 'in_stock' jika to_user_id dikosongkan, atau 'in_use' jika ada user
    toUserSelect.addEventListener('change', function() {
        if (!this.value) {
            newStatusSelect.value = 'in_stock';
        } else {
            newStatusSelect.value = 'in_use';
        }
    });

    updateCurrentInfo();
</script>
@endpush
@endsection
