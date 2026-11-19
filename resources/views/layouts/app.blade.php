<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Invenio') }} - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --inv-forest:   #4E5340;
            --inv-moss:     #697268;
            --inv-slate:    #95A3A4;
            --inv-sky:      #B7D1DA;
            --inv-mist:     #E2E8DD;
            --inv-midnight: #08090A;
            --inv-stone:    #A7A2A9;
            --inv-snow:     #F4F7F5;
            --inv-iron:     #575A5E;
            --inv-charcoal: #222823;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--inv-snow);
            color: var(--inv-midnight);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-[#F4F7F5] text-[#08090A]">

    @auth
    <!-- Top Navigation Bar -->
    <header class="bg-[#222823] text-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-lg bg-[#4E5340] flex items-center justify-center font-bold text-white tracking-wider border border-[#B7D1DA]/30 shadow-inner group-hover:bg-[#697268] transition">
                            IV
                        </div>
                        <span class="font-extrabold text-lg tracking-tight text-[#F4F7F5]">INVENIO</span>
                    </a>

                    <!-- Navigation Links by Role -->
                    <nav class="hidden md:flex items-center space-x-1 text-xs font-semibold">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('dashboard') ? 'bg-[#4E5340] text-[#F4F7F5]' : 'text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white' }}">
                            Dashboard
                        </a>

                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('superadmin.tenants.index') }}" 
                               class="px-3 py-2 rounded-md transition {{ request()->routeIs('superadmin.tenants.*') ? 'bg-[#4E5340] text-[#F4F7F5]' : 'text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white' }}">
                                Kelola Tenant
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <!-- Master Data Links -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md flex items-center gap-1 text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white transition {{ request()->routeIs('admin.sites.*', 'admin.users.*', 'admin.categories.*', 'admin.types.*') ? 'bg-[#4E5340] text-[#F4F7F5]' : '' }}">
                                    <span>Master Data</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="absolute left-0 mt-1 w-48 bg-[#222823] border border-[#697268] rounded-lg shadow-lg py-1.5 hidden group-hover:block z-50">
                                    <a href="{{ route('admin.sites.index') }}" class="block px-4 py-2 text-xs text-[#E2E8DD] hover:bg-[#4E5340] hover:text-white">🏢 Sites / Lokasi</a>
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-xs text-[#E2E8DD] hover:bg-[#4E5340] hover:text-white">👥 User & Karyawan</a>
                                    <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-xs text-[#E2E8DD] hover:bg-[#4E5340] hover:text-white">📁 Kategori Barang</a>
                                    <a href="{{ route('admin.types.index') }}" class="block px-4 py-2 text-xs text-[#E2E8DD] hover:bg-[#4E5340] hover:text-white">🏷️ Jenis & Spek Barang</a>
                                </div>
                            </div>

                            <!-- Kelola Inventaris Links -->
                            <a href="{{ route('admin.assets.index') }}" 
                               class="px-3 py-2 rounded-md transition {{ request()->routeIs('admin.assets.*') ? 'bg-[#4E5340] text-[#F4F7F5]' : 'text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white' }}">
                                📦 Data Inventaris
                            </a>

                            <a href="{{ route('admin.mutations.index') }}" 
                               class="px-3 py-2 rounded-md transition {{ request()->routeIs('admin.mutations.*') ? 'bg-[#4E5340] text-[#F4F7F5]' : 'text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white' }}">
                                🔄 Log Mutasi
                            </a>
                        @else
                            <a href="{{ route('user.my-assets') }}" 
                               class="px-3 py-2 rounded-md transition {{ request()->routeIs('user.my-assets') ? 'bg-[#4E5340] text-[#F4F7F5]' : 'text-[#B7D1DA] hover:bg-[#697268]/50 hover:text-white' }}">
                                🎒 Barang Saya
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Right Nav / User Profile & Logout -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-semibold text-[#F4F7F5]">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-[#95A3A4] flex items-center justify-end gap-1">
                            @if(auth()->user()->isSuperAdmin())
                                <span class="text-[#B7D1DA] font-semibold">🛡️ Super Admin</span>
                            @elseif(auth()->user()->isAdmin())
                                <span class="text-[#B7D1DA] font-semibold">🏢 {{ auth()->user()->tenant->name ?? 'Admin' }}</span>
                            @else
                                <span class="text-[#A7A2A9]">👤 Staff ({{ auth()->user()->tenant->name ?? '' }})</span>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-[#4E5340] hover:bg-[#697268] text-[#F4F7F5] transition duration-150 border border-[#95A3A4]/30 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Nav Bar -->
        <div class="md:hidden border-t border-[#697268]/40 px-4 py-2 flex flex-wrap gap-1 text-xs">
            <a href="{{ route('dashboard') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Dashboard</a>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.tenants.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Tenant</a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('admin.sites.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Sites</a>
                <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Users</a>
                <a href="{{ route('admin.categories.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Kategori</a>
                <a href="{{ route('admin.types.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Jenis</a>
                <a href="{{ route('admin.assets.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Inventaris</a>
                <a href="{{ route('admin.mutations.index') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Mutasi</a>
            @else
                <a href="{{ route('user.my-assets') }}" class="px-2.5 py-1 rounded text-[#B7D1DA] hover:bg-[#4E5340]">Barang Saya</a>
            @endif
        </div>
    </header>
    @endauth

    <!-- Flash Notification Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-3.5 mb-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="p-3.5 mb-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-base">⚠️</span>
                    <span>{{ session('error') ?? 'Terjadi kesalahan input data:' }}</span>
                </div>
                @if($errors->any())
                    <ul class="list-disc list-inside space-y-0.5 ml-6 font-normal">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 pb-12">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 border-t border-[#E2E8DD] bg-[#F4F7F5] text-center text-xs text-[#575A5E]">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} <strong>Invenio</strong> — IT Asset Management System.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
