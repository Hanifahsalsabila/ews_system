<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>@yield('title', 'Portal Keluarga') - E-Posyandu</title>

    <!-- PWA Meta -->
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
        
        /* Gamifikasi Badge Animation */
        @keyframes badgePop {
            0% { transform: scale(0) rotate(-30deg); opacity: 0; }
            50% { transform: scale(1.3) rotate(5deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .badge-pop { animation: badgePop 0.6s ease-out forwards; }

        @keyframes confetti {
            0% { transform: translateY(0) rotate(0); opacity: 1; }
            100% { transform: translateY(-200px) rotate(720deg); opacity: 0; }
        }
        .confetti-particle {
            animation: confetti 1.5s ease-out forwards;
            position: absolute;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-slate-800 antialiased font-sans min-h-screen">

    <!-- Mobile Top Bar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-emerald-100/50 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <div>
                    <h1 class="text-sm font-black text-slate-800 tracking-tight">E-Posyandu</h1>
                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-[0.15em]">Portal Keluarga</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-3 rounded-2xl hover:bg-emerald-50 transition-all">
                        <img class="h-8 w-8 rounded-xl object-cover shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Ibu') }}&background=10b981&color=fff&bold=true" alt="Profile" />
                        <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" x-transition style="display: none;" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 p-2 z-50">
                        <div class="px-4 py-3 bg-emerald-50 rounded-xl mb-2">
                            <p class="text-xs font-black text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-emerald-600 font-bold">Orang Tua / Warga</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 py-6 pb-24 space-y-6">
        @yield('content')
    </main>

    <!-- Bottom Nav (Mobile) -->
    <nav class="fixed bottom-0 inset-x-0 z-50 bg-white/90 backdrop-blur-xl border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
        <div class="max-w-2xl mx-auto flex items-center justify-around py-2">
            <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl {{ request()->routeIs('warga.dashboard') ? 'text-emerald-600' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>
            <a href="{{ route('warga.riwayat') }}" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl {{ request()->routeIs('warga.riwayat') ? 'text-emerald-600' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span class="text-[10px] font-bold">Riwayat</span>
            </a>
            <a href="{{ route('warga.badges') }}" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl {{ request()->routeIs('warga.badges') ? 'text-emerald-600' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <span class="text-[10px] font-bold">Lencana</span>
            </a>
        </div>
    </nav>

    <!-- Toast Notifications -->
    <div x-data="{ 
            show: {{ session('success') || session('error') ? 'true' : 'false' }}, 
            message: '{{ session('success') ?? session('error') }}',
            type: '{{ session('success') ? 'success' : 'error' }}'
         }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4000)"
         x-transition
         style="display: none;"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-[100]">
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl border"
             :class="type === 'success' ? 'bg-white border-emerald-100 text-emerald-600' : 'bg-white border-rose-100 text-rose-600'">
            <span class="text-sm font-bold" x-text="message"></span>
        </div>
    </div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW registration failed:', err));
            });
        }
    </script>
</body>
</html>
