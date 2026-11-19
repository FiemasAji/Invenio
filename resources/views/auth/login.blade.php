@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[calc(100vh-6rem)] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <div class="inline-flex w-14 h-14 rounded-2xl bg-[#222823] items-center justify-center font-extrabold text-2xl text-[#B7D1DA] border-2 border-[#4E5340] shadow-md mb-4">
            IV
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-[#08090A]">
            INVENIO
        </h2>
        <p class="mt-1 text-sm text-[#575A5E]">
            IT Asset Management System
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm rounded-2xl border border-[#E2E8DD] sm:px-10">
            <h3 class="text-lg font-bold text-[#222823] mb-6 pb-2 border-b border-[#E2E8DD]">
                Masuk ke Akun Anda
            </h3>

            @if ($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#575A5E] mb-1">
                        Alamat Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           class="block w-full px-3.5 py-2.5 rounded-lg border border-[#95A3A4]/50 focus:border-[#4E5340] focus:ring-2 focus:ring-[#B7D1DA]/50 text-sm text-[#08090A] placeholder-[#A7A2A9] transition duration-150"
                           placeholder="nama@perusahaan.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#575A5E] mb-1">
                        Kata Sandi
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           class="block w-full px-3.5 py-2.5 rounded-lg border border-[#95A3A4]/50 focus:border-[#4E5340] focus:ring-2 focus:ring-[#B7D1DA]/50 text-sm text-[#08090A] placeholder-[#A7A2A9] transition duration-150"
                           placeholder="••••••••">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-xs font-medium text-[#575A5E] cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#4E5340] focus:ring-[#4E5340] border-[#95A3A4]/60">
                        <span class="ml-2">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-[#4E5340] hover:bg-[#697268] active:bg-[#222823] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4E5340] transition duration-150">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Demo Credentials Helper -->
            <div class="mt-8 pt-5 border-t border-[#E2E8DD] text-xs text-[#575A5E]">
                <p class="font-semibold text-[#222823] mb-2 flex items-center gap-1.5">
                    <span>💡</span> Akun Demo untuk Pengujian:
                </p>
                <div class="space-y-1.5 font-mono text-[11px] bg-[#F4F7F5] p-3 rounded-lg border border-[#E2E8DD]">
                    <div class="flex justify-between items-center">
                        <span class="text-[#4E5340] font-semibold">Super Admin:</span>
                        <code class="text-[#08090A]">superadmin@invenio.app</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#697268] font-semibold">Tenant Admin:</span>
                        <code class="text-[#08090A]">admin@nusantaratech.id</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#95A3A4] font-semibold">Tenant User:</span>
                        <code class="text-[#08090A]">user@nusantaratech.id</code>
                    </div>
                    <div class="pt-1 text-[#A7A2A9] text-[10px]">
                        Password semua akun: <code class="font-bold text-[#575A5E]">password</code>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
