<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Bidan Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine & ChartJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-hero-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(#22c55e 1px, transparent 1px);
            background-size: 32px 32px;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-hero-pattern text-gray-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-white flex flex-col h-full shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform duration-300 lg:static lg:translate-x-0 shrink-0 border-r border-emerald-100/50">
        <!-- Logo Area -->
        <div class="h-20 flex items-center px-8 border-b border-gray-100 shrink-0 relative overflow-hidden">
            <!-- Decorative blur -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
            
            <div class="flex items-center justify-between w-full relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-500/30">
                        P
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 tracking-tight leading-none">E-Posyandu</h1>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1">Bidan Desa</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-6 scrollbar-hide">
            
            <!-- Dashboard -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('bidan.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.dashboard') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Akses Cepat Layanan -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Akses Cepat Layanan</p>
                <div class="space-y-1">
                    <a href="{{ route('bidan.pemeriksaan.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.pemeriksaan.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.pemeriksaan.*') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Pemeriksaan & Kohort
                    </a>
                    
                    <a href="{{ route('bidan.kms.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.kms.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.kms.*') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        KMS & Stunting
                    </a>

                    <a href="{{ route('bidan.logistik.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.logistik.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.logistik.*') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Logistik Obat & Vaksin
                    </a>

                    <a href="{{ route('bidan.penyuluhan.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.penyuluhan.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.penyuluhan.*') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Penyuluhan
                    </a>
                </div>
            </div>

            <!-- Analitik -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Analitik & Interaksi</p>
                <div class="space-y-1">
                    <a href="{{ route('bidan.pelaporan.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('bidan.pelaporan.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm border border-emerald-100' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bidan.pelaporan.*') ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Laporan SIP
                    </a>
                </div>
            </div>

        </nav>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-gray-100 shrink-0">
            <div class="relative" x-data="{ userMenu: false }" @click.away="userMenu = false">
                <button @click="userMenu = !userMenu" class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Bidan') }}&background=10b981&color=fff&bold=true" alt="Avatar" class="w-10 h-10 rounded-xl shadow-sm shrink-0">
                        <div class="text-left truncate">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-semibold text-emerald-600 truncate">Bidan Desa</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="{'rotate-180': userMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userMenu" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute bottom-full mb-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 p-2" style="display: none;">
                    
                    <div class="px-4 py-3 bg-gray-50 rounded-xl mb-1">
                        <p class="text-xs text-gray-500 font-medium">Masuk sebagai</p>
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <a href="{{ route('bidan.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Pengaturan Profil
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left">
                            <svg class="w-4 h-4 mr-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <!-- Navbar Mobile (Visible only on mobile) -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 lg:hidden z-10 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                    P
                </div>
                <span class="font-bold text-gray-900 tracking-tight">E-Posyandu</span>
            </div>
            <button @click="sidebarOpen = true" class="text-gray-500 hover:text-emerald-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <!-- Dynamic Header Slot -->
        @if (isset($header))
            <header class="bg-white/60 backdrop-blur-xl border-b border-white/40 shadow-sm shrink-0 z-10 hidden lg:block">
                <div class="px-8 py-5 flex justify-between items-center">
                    <div class="flex-1">
                        {{ $header }}
                    </div>
                    
                    <!-- Global Top Right User Menu -->
                    <div class="relative ml-4" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100 hover:bg-slate-50 transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="text-sm text-left">
                                <p class="font-bold text-slate-700 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] uppercase tracking-wider text-slate-400 mt-0.5">Bidan Desa</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 ml-1 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden z-50 py-1" style="display: none;">
                            
                            <a href="{{ route('bidan.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Pengaturan Profil
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors text-left border-t border-slate-50 mt-1">
                                    <svg class="w-4 h-4 mr-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
        @endif

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto relative z-0 p-4 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>
