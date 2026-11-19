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
</head>
<body class="min-h-full flex flex-col bg-[#F4F7F5] text-[#08090A]">

    @auth
    <!-- Top Navigation Bar -->
    <header class="bg-[#222823] text-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-lg bg-[#4E5340] flex items-center justify-center font-bold text-white tracking-wider border border-[#B7D1DA]/30 shadow-inner group-hover:bg-[#697268] transition">
                            IV
                        </div>
                        <span class="font-extrabold text-lg tracking-tight text-[#F4F7F5]">INVENIO</span>
                    </a>

                    <!-- Tenant Context Indicator -->
                    @if(auth()->user()->isSuperAdmin())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#4E5340] text-[#E2E8DD] border border-[#B7D1DA]/20">
                            🛡️ Super Admin
                        </span>
                    @elseif(auth()->user()->tenant)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#697268]/60 text-[#B7D1DA] border border-[#697268]">
                            🏢 {{ auth()->user()->tenant->name }}
                        </span>
                    @endif
                </div>

                <!-- Right Nav / User Profile & Logout -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-[#F4F7F5]">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-[#95A3A4] flex items-center justify-end gap-1">
                            @if(auth()->user()->isSuperAdmin())
                                <span class="text-[#B7D1DA] font-medium">Super Administrator</span>
                            @elseif(auth()->user()->isAdmin())
                                <span class="text-[#B7D1DA] font-medium">Tenant Admin</span>
                            @else
                                <span class="text-[#A7A2A9] font-medium">Tenant User</span>
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
    </header>
    @endauth

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 border-t border-[#E2E8DD] bg-[#F4F7F5] text-center text-xs text-[#575A5E]">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} <strong>Invenio</strong> — IT Asset Management System.
        </div>
    </footer>

</body>
</html>
