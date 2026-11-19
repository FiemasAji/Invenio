@extends('layouts.app')

@section('title', 'Edit Tenant ' . $tenant->name)

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Edit Informasi Tenant
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">{{ $tenant->name }}</p>
        </div>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Perusahaan / Tenant *</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Slug / Identifier *</label>
                    <input type="text" name="slug" value="{{ old('slug', $tenant->slug) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Keaktifan</label>
                    <select name="is_active" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="1" {{ old('is_active', $tenant->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $tenant->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Alamat</label>
                <textarea name="address" rows="3"
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">{{ old('address', $tenant->address) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
