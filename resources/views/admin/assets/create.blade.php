@extends('layouts.app')

@section('title', 'Registrasi Aset Baru')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Registrasi Aset Baru
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Gunakan template jenis barang untuk mengisi spesifikasi teknis dan tentukan lokasi penempatan fisik.
            </p>
        </div>
        <a href="{{ route('admin.assets.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <form method="POST" action="{{ route('admin.assets.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Klasifikasi Jenis Barang & Template -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                1. Klasifikasi & Template Inventaris
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Pilih Kategori *</label>
                    <select id="categorySelect" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Pilih Jenis Barang (Template Spesifikasi) *</label>
                    <select name="asset_type_id" id="typeSelect" required 
                            class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Pilih Kategori Terlebih Dahulu --</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Data Pokok Aset -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                2. Data Pokok Aset
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kode Unik Aset *</label>
                    <div class="flex gap-2">
                        <input type="text" name="asset_code" id="assetCode" value="{{ old('asset_code') }}" required
                               class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono uppercase"
                               placeholder="Contoh: AST-LPT-001">
                        <button type="button" id="btnGenCode"
                                class="px-3 py-2 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] hover:bg-gray-100 text-[#575A5E] whitespace-nowrap">
                            ⚡ Auto
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama / Merk & Model Aset *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Contoh: Lenovo ThinkPad T14s Gen 4">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nomor Seri Pabrik (Serial Number)</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono"
                           placeholder="Contoh: PF3X98AB">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Aset *</label>
                    <select name="status" required class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="in_stock" {{ old('status') === 'in_stock' ? 'selected' : '' }}>Tersedia di Gudang (In Stock)</option>
                        <option value="in_use" {{ old('status') === 'in_use' ? 'selected' : '' }}>Sedang Dipakai Karyawan (In Use)</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Dalam Perbaikan / Maintenance</option>
                        <option value="disposed" {{ old('status') === 'disposed' ? 'selected' : '' }}>Non-Aktif / Rusak / Dihapus</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Tanggal Pembelian / Perolehan</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
            </div>
        </div>

        <!-- 3. Penempatan Lokasi & Pemilik (c1) -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                3. Penempatan Fisik & Kepemilikan (Site & User)
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Lokasi Site / Kantor Perusahaan</label>
                    <select name="site_id" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Tidak Ditentukan / Pusat --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }} ({{ $site->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#A7A2A9] mt-1">Gedung atau kantor cabang tempat aset disimpan/digunakan.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Pemegang Aset (Karyawan)</label>
                    <select name="user_id" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Belum Diserahkan (Gudang / Shared) --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->employee_id ? "({$user->employee_id})" : '' }} - {{ $user->position ?: 'Staff' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#A7A2A9] mt-1">Karyawan yang bertanggung jawab menggunakan barang ini.</p>
                </div>
            </div>
        </div>

        <!-- 4. Parameter Spesifikasi Dinamis (b3 / c1) -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E2E8DD]">
                <div>
                    <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider">
                        4. Parameter Spesifikasi Khusus (Custom Specs)
                    </h2>
                    <p class="text-[11px] text-[#575A5E]">
                        Bidang spesifikasi otomatis dimuat sesuai template jenis barang yang dipilih.
                    </p>
                </div>
                <span id="specBadge" class="text-[11px] font-semibold text-[#575A5E] px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD]">
                    Pilih Jenis Barang Dahulu
                </span>
            </div>

            <!-- Dynamic Fields Render Area -->
            <div id="dynamicSpecsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 py-6 text-center text-xs text-[#95A3A4]">
                    Silakan pilih Jenis Barang di bagian atas untuk mengisi spesifikasi teknis barang.
                </div>
            </div>
        </div>

        <!-- 5. Catatan Tambahan -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                5. Keterangan Tambahan
            </h2>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Catatan / Kondisi Fisik</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                          placeholder="Kelengkapan unit, kondisi fisik, aksesoris, dll...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Submit & Cancel Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.assets.index') }}" 
               class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:text-[#08090A]">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan & Daftarkan Aset
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    // Data Kategori & Jenis Barang beserta Field Spesifikasinya
    const categoriesData = @json($categories);

    const categorySelect = document.getElementById('categorySelect');
    const typeSelect = document.getElementById('typeSelect');
    const dynamicSpecsContainer = document.getElementById('dynamicSpecsContainer');
    const specBadge = document.getElementById('specBadge');
    const btnGenCode = document.getElementById('btnGenCode');
    const assetCodeInput = document.getElementById('assetCode');

    // Filter Type saat Kategori dipilih
    categorySelect.addEventListener('change', function() {
        const catId = parseInt(this.value);
        typeSelect.innerHTML = '<option value="">-- Pilih Jenis Barang --</option>';
        dynamicSpecsContainer.innerHTML = '<div class="sm:col-span-2 py-6 text-center text-xs text-[#95A3A4]">Silakan pilih Jenis Barang untuk mengisi spesifikasi teknis.</div>';
        specBadge.textContent = 'Pilih Jenis Barang';

        if (!catId) return;

        const category = categoriesData.find(c => c.id === catId);
        if (category && category.types) {
            category.types.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = `${t.name} (${t.code})`;
                typeSelect.appendChild(opt);
            });
        }
    });

    // Render Dynamic Specs saat Jenis Barang dipilih
    typeSelect.addEventListener('change', function() {
        const typeId = parseInt(this.value);
        dynamicSpecsContainer.innerHTML = '';

        if (!typeId) {
            dynamicSpecsContainer.innerHTML = '<div class="sm:col-span-2 py-6 text-center text-xs text-[#95A3A4]">Silakan pilih Jenis Barang untuk mengisi spesifikasi teknis.</div>';
            specBadge.textContent = 'Pilih Jenis Barang';
            return;
        }

        // Cari type di categoriesData
        let selectedType = null;
        for (const cat of categoriesData) {
            if (cat.types) {
                const found = cat.types.find(t => t.id === typeId);
                if (found) {
                    selectedType = found;
                    break;
                }
            }
        }

        if (!selectedType) return;

        // Auto suggest kode aset jika masih kosong
        if (!assetCodeInput.value) {
            generateAssetCode(selectedType.code);
        }

        const fields = selectedType.spec_fields || [];
        specBadge.textContent = `${fields.length} Parameter Tersedia`;

        if (fields.length === 0) {
            dynamicSpecsContainer.innerHTML = '<div class="sm:col-span-2 py-4 text-center text-xs text-[#95A3A4]">Jenis barang ini tidak memiliki parameter spesifikasi kustom.</div>';
            return;
        }

        fields.forEach(field => {
            const wrapper = document.createElement('div');
            const reqStar = field.is_required ? '<span class="text-red-500">*</span>' : '';
            const reqAttr = field.is_required ? 'required' : '';

            let inputHtml = '';
            if (field.field_type === 'select') {
                let optionsHtml = '<option value="">-- Pilih ' + field.name + ' --</option>';
                if (Array.isArray(field.options)) {
                    field.options.forEach(opt => {
                        optionsHtml += `<option value="${opt}">${opt}</option>`;
                    });
                }
                inputHtml = `
                    <select name="specs[${field.name}]" ${reqAttr} class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        ${optionsHtml}
                    </select>
                `;
            } else if (field.field_type === 'number') {
                inputHtml = `
                    <input type="number" step="any" name="specs[${field.name}]" ${reqAttr}
                           class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Masukkan angka...">
                `;
            } else {
                inputHtml = `
                    <input type="text" name="specs[${field.name}]" ${reqAttr}
                           class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Spesifikasi ${field.name}...">
                `;
            }

            wrapper.innerHTML = `
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">
                    ${field.name} ${reqStar}
                </label>
                ${inputHtml}
            `;
            dynamicSpecsContainer.appendChild(wrapper);
        });
    });

    function generateAssetCode(prefix = 'AST') {
        const rand = Math.floor(1000 + Math.random() * 9000);
        const cleanPrefix = prefix.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        assetCodeInput.value = `AST-${cleanPrefix}-${rand}`;
    }

    btnGenCode.addEventListener('click', function() {
        let prefix = 'GEN';
        const typeId = parseInt(typeSelect.value);
        if (typeId) {
            for (const cat of categoriesData) {
                if (cat.types) {
                    const found = cat.types.find(t => t.id === typeId);
                    if (found) {
                        prefix = found.code;
                        break;
                    }
                }
            }
        }
        generateAssetCode(prefix);
    });
</script>
@endpush
@endsection
