@extends('layouts.app')

@section('title', 'Tambah Tenant Baru')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Pendaftaran Tenant & Admin Baru
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">
                Daftarkan perusahaan baru sekaligus akun Administrator pertama untuk tenant tersebut.
            </p>
        </div>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="space-y-6">
        @csrf

        <!-- Bagian 1: Data Perusahaan Tenant -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD] flex items-center gap-2">
                <span>🏢</span> Informasi Perusahaan Tenant
            </h2>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Perusahaan / Tenant *</label>
                <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required
                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                       placeholder="Contoh: PT Teknologi Berkah Nusantara">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Slug / Identifier (Opsional)</label>
                    <input type="text" name="tenant_slug" value="{{ old('tenant_slug') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="otomatis dari nama jika kosong">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Email Resmi Perusahaan</label>
                    <input type="email" name="tenant_email" value="{{ old('tenant_email') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="contact@perusahaan.com">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nomor Telepon Kantor</label>
                    <input type="text" name="tenant_phone" value="{{ old('tenant_phone') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="021-12345678">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Keaktifan</label>
                    <select name="is_active" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#575A5E] mb-1">Alamat Lengkap Perusahaan</label>
                <textarea name="tenant_address" rows="2"
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                          placeholder="Alamat kantor pusat...">{{ old('tenant_address') }}</textarea>
            </div>
        </div>

        <!-- Bagian 2: Akun Admin Utama Tenant -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#222823] uppercase tracking-wider pb-2 border-b border-[#E2E8DD] flex items-center gap-2">
                <span>👤</span> Akun Administrator Tenant Pertama
            </h2>
            <p class="text-[11px] text-[#575A5E]">
                Akun ini akan menjadi pemilik tenant yang dapat masuk ke Invenio dan mengelola seluruh aset serta karyawan perusahaannya.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Lengkap Admin *</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Nama PIC / IT Manager">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Email Login Admin *</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="admin@perusahaan.com">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kata Sandi Awal *</label>
                    <input type="password" name="admin_password" required minlength="6"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Jabatan / Posisi</label>
                    <input type="text" name="admin_position" value="{{ old('admin_position', 'IT Manager') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">No Telepon / WhatsApp</label>
                    <input type="text" name="admin_phone" value="{{ old('admin_phone') }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="08123456789">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Daftarkan Tenant & Akun Admin
            </button>
        </div>
    </form>

</div>
@endsection
