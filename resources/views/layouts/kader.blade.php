<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kader Dashboard - {{ config('app.name', 'E-Posyandu') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Style -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-hero-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(#10b981 1px, transparent 1px);
            background-size: 32px 32px;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-hero-pattern text-slate-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-white flex flex-col h-full shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform duration-300 lg:static lg:translate-x-0 shrink-0 border-r border-teal-100/50">
        <!-- Logo Area -->
        <div class="h-20 flex items-center px-8 border-b border-slate-100 shrink-0 relative overflow-hidden bg-gradient-to-r from-emerald-600 to-teal-600">
            <!-- Decorative blur -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-white rounded-full mix-blend-overlay filter blur-2xl opacity-20"></div>
            
            <div class="flex items-center justify-between w-full relative z-10">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center font-black text-xl backdrop-blur-sm border border-white/30">
                        P
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight leading-none">E-Posyandu</h1>
                        <p class="text-[10px] font-bold text-teal-100 uppercase tracking-widest mt-1">Kader Posyandu</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-6 scrollbar-hide">
            
            <!-- Beranda -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('kader.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kader.dashboard') ? 'bg-teal-50 text-teal-700 font-bold shadow-sm border border-teal-100' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kader.dashboard') ? 'text-teal-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Manajemen Data -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Manajemen Data</p>
                <div class="space-y-1">
                    <a href="{{ route('kader.warga.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kader.warga.*') ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm border border-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kader.warga.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Buku Register Warga
                    </a>
                </div>
            </div>

            <!-- Operasional Meja -->
            <div>
                <p class="px-4 mt-6 mb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Operasional Posyandu</p>
                <div class="space-y-1">
                    <a href="{{ route('kader.pendaftaran.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kader.pendaftaran.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm border border-emerald-100' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kader.pendaftaran.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Meja 1: Absensi Kehadiran
                    </a>
                    
                    <a href="{{ route('kader.pengukuran.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kader.pengukuran.*') ? 'bg-teal-50 text-teal-700 font-bold shadow-sm border border-teal-100' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kader.pengukuran.*') ? 'text-teal-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        Meja 2 & 3: Pengukuran
                    </a>

                    <a href="{{ route('kader.penyuluhan.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kader.penyuluhan.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm border border-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 font-medium border border-transparent' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kader.penyuluhan.*') ? 'text-blue-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Materi Penyuluhan
                    </a>
                </div>
            </div>

        </nav>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-slate-100 shrink-0">
            <div class="relative" x-data="{ userMenu: false }" @click.away="userMenu = false">
                <button @click="userMenu = !userMenu" class="w-full flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-600 font-black text-lg flex items-center justify-center shrink-0">
                            {{ substr(auth()->user()->name ?? 'Kader', 0, 1) }}
                        </div>
                        <div class="text-left truncate">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest truncate">Kader Posyandu</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform" :class="{'rotate-180': userMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userMenu" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute bottom-full mb-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 p-2" style="display: none;">
                    
                    <div class="px-4 py-3 bg-slate-50 rounded-xl mb-1">
                        <p class="text-xs text-slate-500 font-medium">Masuk sebagai</p>
                        <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <a href="{{ route('kader.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-bold text-slate-700 hover:text-teal-600 hover:bg-teal-50 rounded-xl transition-colors">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Pengaturan Profil
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block w-full mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left">
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
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-4 lg:hidden z-10 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-black text-sm shadow-md">
                    P
                </div>
                <span class="font-bold text-slate-800 tracking-tight">Kader Portal</span>
            </div>
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-teal-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <!-- Top Header for Desktop -->
        <header class="bg-white/60 backdrop-blur-xl border-b border-white/40 shadow-sm shrink-0 z-10 hidden lg:block">
            <div class="px-8 py-4 flex justify-between items-center">
                <!-- Empty div or breadcrumbs can go here -->
                <div class="flex-1">
                    @if (isset($header))
                        {{ $header }}
                    @endif
                </div>
                
                <!-- Global Top Right User Menu -->
                <div class="relative ml-4 flex items-center gap-4">
                    <!-- Date Pill -->
                    <div class="hidden md:flex items-center gap-2 bg-white/80 px-4 py-2 rounded-full border border-slate-100 shadow-sm text-sm font-medium text-slate-500">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ now()->translatedFormat('d F Y') }}
                    </div>

                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 bg-white pl-2 pr-4 py-2 rounded-full shadow-sm border border-slate-100 hover:bg-slate-50 hover:border-teal-200 transition-all focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-black text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="text-sm text-left">
                                <p class="font-bold text-slate-800 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-teal-600 mt-0.5">Kader</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 ml-1 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden z-50 p-2" style="display: none;">
                            
                            <a href="{{ route('kader.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-teal-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Profil Saya
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="block w-full mt-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left rounded-xl">
                                    <svg class="w-4 h-4 mr-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto relative z-0 p-4 lg:p-8">
            
            <!-- Flash Messages -->
            <div class="max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 p-1.5 rounded-full">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <div class="bg-rose-100 p-1.5 rounded-full">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-4 rounded-2xl flex items-start justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start gap-3">
                            <div class="bg-rose-100 p-1.5 rounded-full mt-0.5">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <span class="font-black text-sm">Terjadi Kesalahan:</span>
                                <ul class="list-disc list-inside text-sm font-medium mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Content Slot -->
            {{ $slot }}
        </main>
    </div>

    <!-- Additional Scripts Slot -->
    @if (isset($scripts))
        {{ $scripts }}
    @endif
</body>
</html>
