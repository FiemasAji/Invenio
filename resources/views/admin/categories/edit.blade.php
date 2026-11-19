@extends('layouts.app')

@section('title', 'Edit Kategori: ' . $category->name)

@section('content')
<div class="max-w-xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Edit Kategori Barang
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">{{ $category->name }} ({{ $category->code }})</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kode Khusus Kategori *</label>
                <input type="text" name="code" value="{{ old('code', $category->code) }}" required
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono uppercase">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">{{ old('description', $category->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
