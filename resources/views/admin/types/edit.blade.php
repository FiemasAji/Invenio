@extends('layouts.app')

@section('title', 'Edit Jenis Barang: ' . $type->name)

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Edit Jenis Barang & Template Spesifikasi
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">{{ $type->name }} ({{ $type->code }})</p>
        </div>
        <a href="{{ route('admin.types.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.types.update', $type) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Informasi Pokok -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD]">
                Informasi Jenis Barang
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kategori Induk *</label>
                    <select name="asset_category_id" required class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('asset_category_id', $type->asset_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kode Khusus Jenis *</label>
                    <input type="text" name="code" value="{{ old('code', $type->code) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono uppercase">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Jenis Barang *</label>
                <input type="text" name="name" value="{{ old('name', $type->name) }}" required
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">{{ old('description', $type->description) }}</textarea>
            </div>
        </div>

        <!-- Bagian 2: Builder Parameter Spesifikasi Dinamis -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E2E8DD]">
                <div>
                    <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider">
                        Parameter Spesifikasi Kustom
                    </h2>
                    <p class="text-[11px] text-[#575A5E]">
                        Parameter yang diubah akan berlaku untuk pencatatan dan pembaruan aset jenis ini.
                    </p>
                </div>
                <button type="button" id="btnAddSpec"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-[#B7D1DA]/30 hover:bg-[#B7D1DA]/60 text-[#222823] border border-[#95A3A4]/40 transition">
                    ➕ Tambah Parameter
                </button>
            </div>

            <div id="specContainer" class="space-y-3">
                @forelse($type->specFields as $index => $field)
                <div class="spec-row grid grid-cols-12 gap-2 p-3 rounded-xl bg-[#F4F7F5] border border-[#E2E8DD] items-center">
                    <div class="col-span-4">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Nama Parameter *</label>
                        <input type="text" name="specs[{{ $index }}][name]" value="{{ $field->name }}" required
                               class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Tipe Input *</label>
                        <select name="specs[{{ $index }}][type]" onchange="toggleOptions(this)"
                                class="w-full px-2 py-1.5 text-xs rounded border border-[#95A3A4]/40 focus:border-[#4E5340]">
                            <option value="text" {{ $field->field_type === 'text' ? 'selected' : '' }}>Teks Bebas</option>
                            <option value="number" {{ $field->field_type === 'number' ? 'selected' : '' }}>Angka</option>
                            <option value="select" {{ $field->field_type === 'select' ? 'selected' : '' }}>Pilihan Dropdown</option>
                        </select>
                    </div>
                    <div class="col-span-4 opt-container {{ $field->field_type === 'select' ? '' : 'hidden' }}">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Pilihan (Pisah koma)</label>
                        <input type="text" name="specs[{{ $index }}][options]" value="{{ is_array($field->options) ? implode(', ', $field->options) : '' }}"
                               class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40"
                               placeholder="Opsi 1, Opsi 2">
                    </div>
                    <div class="col-span-1 text-center pt-3">
                        <button type="button" onclick="removeSpec(this)" class="text-red-500 hover:text-red-700 font-bold text-base">&times;</button>
                    </div>
                </div>
                @empty
                <div class="spec-row grid grid-cols-12 gap-2 p-3 rounded-xl bg-[#F4F7F5] border border-[#E2E8DD] items-center">
                    <div class="col-span-4">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Nama Parameter *</label>
                        <input type="text" name="specs[0][name]" required class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Tipe Input *</label>
                        <select name="specs[0][type]" onchange="toggleOptions(this)" class="w-full px-2 py-1.5 text-xs rounded border border-[#95A3A4]/40">
                            <option value="text">Teks Bebas</option>
                            <option value="number">Angka</option>
                            <option value="select">Pilihan Dropdown</option>
                        </select>
                    </div>
                    <div class="col-span-4 opt-container hidden">
                        <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Pilihan (Pisah koma)</label>
                        <input type="text" name="specs[0][options]" class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40">
                    </div>
                    <div class="col-span-1 text-center pt-3">
                        <button type="button" onclick="removeSpec(this)" class="text-red-500 hover:text-red-700 font-bold text-base">&times;</button>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.types.index') }}" class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let specIndex = {{ $type->specFields->count() ?: 1 }};

    function toggleOptions(select) {
        const row = select.closest('.spec-row');
        const optContainer = row.querySelector('.opt-container');
        if (select.value === 'select') {
            optContainer.classList.remove('hidden');
        } else {
            optContainer.classList.add('hidden');
        }
    }

    function removeSpec(btn) {
        const rows = document.querySelectorAll('.spec-row');
        if (rows.length > 1) {
            btn.closest('.spec-row').remove();
        } else {
            alert('Minimal harus ada 1 parameter spesifikasi!');
        }
    }

    document.getElementById('btnAddSpec').addEventListener('click', function() {
        const container = document.getElementById('specContainer');
        const newRow = document.createElement('div');
        newRow.className = 'spec-row grid grid-cols-12 gap-2 p-3 rounded-xl bg-[#F4F7F5] border border-[#E2E8DD] items-center';
        newRow.innerHTML = `
            <div class="col-span-4">
                <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Nama Parameter *</label>
                <input type="text" name="specs[${specIndex}][name]" required
                       class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40 focus:border-[#4E5340]"
                       placeholder="Misal: Storage (GB)">
            </div>
            <div class="col-span-3">
                <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Tipe Input *</label>
                <select name="specs[${specIndex}][type]" onchange="toggleOptions(this)"
                        class="w-full px-2 py-1.5 text-xs rounded border border-[#95A3A4]/40 focus:border-[#4E5340]">
                    <option value="text">Teks Bebas</option>
                    <option value="number">Angka</option>
                    <option value="select">Pilihan Dropdown</option>
                </select>
            </div>
            <div class="col-span-4 opt-container hidden">
                <label class="block text-[10px] font-semibold text-[#575A5E] mb-0.5">Pilihan (Pisah koma)</label>
                <input type="text" name="specs[${specIndex}][options]"
                       class="w-full px-2.5 py-1.5 text-xs rounded border border-[#95A3A4]/40"
                       placeholder="Opsi 1, Opsi 2">
            </div>
            <div class="col-span-1 text-center pt-3">
                <button type="button" onclick="removeSpec(this)" class="text-red-500 hover:text-red-700 font-bold text-base">&times;</button>
            </div>
        `;
        container.appendChild(newRow);
        specIndex++;
    });
</script>
@endpush
@endsection
