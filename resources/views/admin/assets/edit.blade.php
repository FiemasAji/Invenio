@extends('layouts.app')

@section('title', 'Edit Data Aset - ' . $asset->asset_code)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Edit Data Aset: {{ $asset->name }}
            </h1>
            <p class="text-xs text-[#575A5E] mt-1 font-mono">
                Kode Aset: <strong class="text-[#4E5340]">{{ $asset->asset_code }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.assets.show', $asset) }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
                Lihat Detail &rarr;
            </a>
            <span class="text-[#95A3A4]">|</span>
            <a href="{{ route('admin.assets.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.assets.update', $asset) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Klasifikasi Jenis Barang -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                1. Klasifikasi & Template
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kategori</label>
                    <select id="categorySelect" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $asset->type->asset_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Jenis Barang *</label>
                    <select name="asset_type_id" id="typeSelect" required 
                            class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        @foreach($categories as $category)
                            @if($category->id == $asset->type->asset_category_id)
                                @foreach($category->types as $t)
                                    <option value="{{ $t->id }}" {{ $asset->asset_type_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->code }})
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
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
                    <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono uppercase">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama / Merk & Model Aset *</label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nomor Seri Pabrik (Serial Number)</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Aset *</label>
                    <select name="status" required class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="in_stock" {{ old('status', $asset->status) === 'in_stock' ? 'selected' : '' }}>Tersedia di Gudang (In Stock)</option>
                        <option value="in_use" {{ old('status', $asset->status) === 'in_use' ? 'selected' : '' }}>Sedang Dipakai Karyawan (In Use)</option>
                        <option value="maintenance" {{ old('status', $asset->status) === 'maintenance' ? 'selected' : '' }}>Dalam Perbaikan / Maintenance</option>
                        <option value="disposed" {{ old('status', $asset->status) === 'disposed' ? 'selected' : '' }}>Non-Aktif / Rusak / Dihapus</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Tanggal Pembelian / Perolehan</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
            </div>
        </div>

        <!-- 3. Penempatan Lokasi & Pemegang -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                3. Penempatan Fisik & Kepemilikan (Site & User)
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Lokasi Site / Kantor</label>
                    <select name="site_id" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Tidak Ditentukan / Pusat --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $asset->site_id) == $site->id ? 'selected' : '' }}>
                                {{ $site->name }} ({{ $site->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Pemegang Aset (Karyawan)</label>
                    <select name="user_id" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Belum Diserahkan (Gudang / Shared) --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $asset->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->employee_id ? "({$user->employee_id})" : '' }} - {{ $user->position ?: 'Staff' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. Parameter Spesifikasi Khusus (Custom Specs) -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E2E8DD]">
                <div>
                    <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider">
                        4. Parameter Spesifikasi Khusus (Custom Specs)
                    </h2>
                    <p class="text-[11px] text-[#575A5E]">
                        Nilai parameter spesifikasi teknis untuk unit aset ini.
                    </p>
                </div>
                <span id="specBadge" class="text-[11px] font-semibold text-[#575A5E] px-2.5 py-1 rounded bg-[#F4F7F5] border border-[#E2E8DD]">
                    Spesifikasi Terpasang
                </span>
            </div>

            <!-- Dynamic Fields Render Area -->
            <div id="dynamicSpecsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- JavaScript will populate or re-populate -->
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
                          placeholder="Kelengkapan unit, kondisi fisik, aksesoris, dll...">{{ old('notes', $asset->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.assets.show', $asset) }}" 
               class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E] hover:text-[#08090A]">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    const categoriesData = @json($categories);
    const existingSpecs = @json($asset->specs ?? []);

    const categorySelect = document.getElementById('categorySelect');
    const typeSelect = document.getElementById('typeSelect');
    const dynamicSpecsContainer = document.getElementById('dynamicSpecsContainer');
    const specBadge = document.getElementById('specBadge');

    function renderSpecs(typeId) {
        dynamicSpecsContainer.innerHTML = '';
        if (!typeId) {
            dynamicSpecsContainer.innerHTML = '<div class="sm:col-span-2 py-6 text-center text-xs text-[#95A3A4]">Silakan pilih Jenis Barang untuk mengisi spesifikasi teknis.</div>';
            specBadge.textContent = 'Pilih Jenis Barang';
            return;
        }

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
            const curVal = existingSpecs[field.name] !== undefined ? existingSpecs[field.name] : '';

            let inputHtml = '';
            if (field.field_type === 'select') {
                let optionsHtml = '<option value="">-- Pilih ' + field.name + ' --</option>';
                if (Array.isArray(field.options)) {
                    field.options.forEach(opt => {
                        const sel = String(curVal) === String(opt) ? 'selected' : '';
                        optionsHtml += `<option value="${opt}" ${sel}>${opt}</option>`;
                    });
                }
                inputHtml = `
                    <select name="specs[${field.name}]" ${reqAttr} class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        ${optionsHtml}
                    </select>
                `;
            } else if (field.field_type === 'number') {
                inputHtml = `
                    <input type="number" step="any" name="specs[${field.name}]" value="${curVal}" ${reqAttr}
                           class="w-full px-3 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Masukkan angka...">
                `;
            } else {
                inputHtml = `
                    <input type="text" name="specs[${field.name}]" value="${curVal}" ${reqAttr}
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
    }

    categorySelect.addEventListener('change', function() {
        const catId = parseInt(this.value);
        typeSelect.innerHTML = '<option value="">-- Pilih Jenis Barang --</option>';
        if (!catId) return;

        const category = categoriesData.find(c => c.id === catId);
        if (category && category.types) {
            category.types.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = `${t.name} (${t.code})`;
                typeSelect.appendChild(opt);
            });
            if (category.types.length > 0) {
                typeSelect.value = category.types[0].id;
                renderSpecs(category.types[0].id);
            }
        }
    });

    typeSelect.addEventListener('change', function() {
        renderSpecs(parseInt(this.value));
    });

    // Initial render
    renderSpecs(parseInt(typeSelect.value));
</script>
@endpush
@endsection
