<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - E-Posyandu</title>

    <!-- PWA Meta -->
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js and Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Responsive Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-slate-900 text-white flex flex-col h-full shadow-2xl transition-transform duration-300 lg:static lg:translate-x-0 shrink-0 border-r border-slate-800">
        <div class="h-20 flex items-center px-8 border-b border-slate-800 bg-slate-900/50 backdrop-blur-xl shrink-0">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-white">E-Posyandu</h1>
                        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-[0.2em]">Enterprise</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8">
            <!-- Group: Utama -->
            <div>
                <p class="px-4 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Dashboard & Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Overview</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Manajemen Pengguna</span>
                    </a>
                </div>
            </div>

            <!-- Group: Data Master -->
            <div>
                <p class="px-4 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Master Data</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.regions.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.regions.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.regions.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Data Wilayah</span>
                    </a>
                    <a href="{{ route('admin.wargas.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.wargas.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.wargas.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Data Warga</span>
                    </a>
                    <a href="{{ route('admin.vaksins.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.vaksins.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.vaksins.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Vaksin & Suplemen</span>
                    </a>
                    <a href="{{ route('admin.inventaris.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.inventaris.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.inventaris.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Alat Kesehatan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Layanan -->
            <div>
                <p class="px-4 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Layanan Operasional</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.sesi-posyandus.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.sesi-posyandus.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.sesi-posyandus.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Sesi Posyandu</span>
                    </a>
                    <a href="{{ route('admin.pelaporan.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pelaporan.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.pelaporan.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Analitik & SIP</span>
                    </a>
                    <a href="{{ route('admin.audits.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.audits.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent' }} rounded-2xl transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.audits.*') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm tracking-tight">Aktivitas Audit</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- User Info at Bottom -->
        <div class="p-6 border-t border-slate-800 bg-slate-900/50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=10b981&color=fff&bold=true" alt="Avatar" class="w-10 h-10 rounded-xl border-2 border-slate-700 shadow-md">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-black text-white truncate uppercase tracking-tighter">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] font-bold text-slate-500 truncate uppercase mt-0.5">S-Admin Access</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Topbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200/50 flex items-center justify-between px-8 z-10 shrink-0">
            <!-- Mobile Menu Toggle & Search -->
            <div class="flex items-center flex-1 gap-6">
                <!-- Hamburger button connected to sidebarOpen state -->
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-emerald-600 focus:outline-none lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="relative w-full max-w-lg hidden sm:block">
                    <div class="absolute inset-y-1 left-1 w-10 flex items-center justify-center text-gray-400 bg-gray-50 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" class="w-full bg-gray-50 border-none text-slate-700 rounded-2xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm font-medium" placeholder="Cari data, laporan, atau warga...">
                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-6">
                <!-- Notifications -->
                @php
                    $inventarisIssues = \App\Models\Inventaris::whereIn('kondisi', ['rusak', 'hilang'])->get();
                    $wargaBaru = \App\Models\Warga::where('created_at', '>=', now()->subDays(7))->latest()->take(5)->get();
                    $totalNotif = $inventarisIssues->count() + $wargaBaru->count();
                @endphp
                <div class="relative" x-data="{ openNotif: false }" @click.away="openNotif = false">
                    <button @click="openNotif = !openNotif" class="relative w-11 h-11 flex items-center justify-center text-gray-400 hover:text-emerald-600 transition-all rounded-xl hover:bg-emerald-50 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($totalNotif > 0)
                            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full flex items-center justify-center">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="openNotif" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                         class="absolute right-0 mt-4 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-gray-100 p-2 z-50 origin-top-right overflow-hidden flex flex-col max-h-[80vh]">
                        
                        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between shrink-0">
                            <p class="text-sm font-black text-slate-800">Notifikasi</p>
                            @if($totalNotif > 0)
                                <span class="bg-rose-100 text-rose-600 py-0.5 px-2 rounded-lg text-[10px] font-black">{{ $totalNotif }} Baru</span>
                            @endif
                        </div>

                        <div class="overflow-y-auto overflow-x-hidden flex-1 p-2 space-y-1">
                            @if($totalNotif === 0)
                                <div class="px-4 py-8 text-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-xs text-gray-400 font-bold">Tidak ada notifikasi baru</p>
                                </div>
                            @else
                                <!-- Inventaris Warnings -->
                                @foreach($inventarisIssues as $inv)
                                    <a href="{{ route('admin.inventaris.index') }}" class="flex items-start gap-3 p-3 hover:bg-rose-50/50 rounded-2xl transition-colors group">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-0.5">Peringatan Inventaris</p>
                                            <p class="text-xs font-bold text-slate-700 truncate group-hover:text-rose-700 transition-colors">{{ $inv->nama_alat }} ({{ $inv->kode_alat }})</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Kondisi saat ini: <span class="font-bold {{ $inv->kondisi == 'hilang' ? 'text-red-500' : 'text-orange-500' }}">{{ ucfirst($inv->kondisi) }}</span></p>
                                        </div>
                                    </a>
                                @endforeach

                                <!-- New Warga -->
                                @foreach($wargaBaru as $warga)
                                    <a href="{{ route('admin.wargas.index') }}" class="flex items-start gap-3 p-3 hover:bg-emerald-50/50 rounded-2xl transition-colors group">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-wider mb-0.5">Warga Baru (7 Hari Terakhir)</p>
                                            <p class="text-xs font-bold text-slate-700 truncate group-hover:text-emerald-700 transition-colors">{{ $warga->nama }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Terdaftar pada {{ $warga->created_at->format('d M Y') }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 p-1.5 pr-3 rounded-2xl border border-transparent hover:border-gray-200 hover:bg-gray-50 transition-all focus:outline-none">
                        <img class="h-9 w-9 rounded-xl object-cover shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=10b981&color=fff&bold=true" alt="Admin Profile" />
                        <div class="text-left hidden md:block">
                            <p class="text-xs font-black text-slate-800 leading-none uppercase">{{ auth()->user()->username ?? 'admin' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wider">Super Admin</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                         class="absolute right-0 mt-4 w-64 bg-white rounded-3xl shadow-2xl border border-gray-100 p-2 z-50 origin-top-right">
                        
                        <div class="px-5 py-4 mb-2 bg-slate-50 rounded-2xl">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Account</p>
                            <p class="text-sm font-black text-slate-800 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>

                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 rounded-2xl transition-all group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            My Profile
                        </a>
                        <div class="h-px bg-gray-100 my-2 mx-4"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-2xl transition-all group">
                                <svg class="w-5 h-5 text-rose-400 group-hover:text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 sm:p-10 bg-slate-50/50 shadow-[inset_0_10px_30px_-15px_rgba(0,0,0,0.05)]">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notifications -->
    <div x-data="{ 
            show: {{ session('success') || session('error') ? 'true' : 'false' }}, 
            message: '{{ session('success') ?? session('error') }}',
            type: '{{ session('success') ? 'success' : 'error' }}'
         }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         style="display: none;"
         class="fixed bottom-10 right-10 z-[100]">
        
        <div class="flex items-center gap-4 px-6 py-4 rounded-3xl shadow-2xl border"
             :class="type === 'success' ? 'bg-white border-emerald-100 text-emerald-600' : 'bg-white border-rose-100 text-rose-600'">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shadow-lg shadow-current/10"
                 :class="type === 'success' ? 'bg-emerald-50' : 'bg-rose-50'">
                <template x-if="type === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </template>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-0.5" x-text="type === 'success' ? 'Berhasil' : 'Peringatan'"></p>
                <p class="text-sm font-black text-slate-800" x-text="message"></p>
            </div>
            <button @click="show = false" class="ml-4 p-1 hover:bg-slate-50 rounded-lg transition-colors">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
</body>
</html>
