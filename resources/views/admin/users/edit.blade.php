@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#08090A] tracking-tight">
                Edit Data Pengguna / Karyawan
            </h1>
            <p class="text-xs text-[#575A5E] mt-1">{{ $user->name }} ({{ $user->email }})</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-[#575A5E] hover:text-[#08090A]">
            &larr; Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-2xl border border-[#E2E8DD] shadow-sm space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Email Login *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Ubah Kata Sandi (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Tingkatan / Peran Akun *</label>
                    <select name="role" required class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Karyawan Biasa)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin Tenant</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">ID Karyawan / NIP</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Kode Khusus Pengguna</label>
                    <input type="text" name="custom_code" value="{{ old('custom_code', $user->custom_code) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340] font-mono uppercase">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Posisi / Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $user->position) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Penempatan Site / Lokasi Kantor</label>
                    <select name="site_id" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="">-- Belum Ditentukan --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $user->site_id) == $site->id ? 'selected' : '' }}>
                                {{ $site->name }} ({{ $site->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#575A5E] mb-1">Status Keaktifan</label>
                    <select name="is_active" class="w-full px-3.5 py-2 text-xs rounded-lg border border-[#95A3A4]/40 focus:border-[#4E5340]">
                        <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#F4F7F5] border border-[#E2E8DD] text-[#575A5E]">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold bg-[#4E5340] hover:bg-[#697268] text-white shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
