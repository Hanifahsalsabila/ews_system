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

        /* Smooth entrance animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: .6; }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out both;
        }
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out both;
        }
        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.1s; }
        .stagger-3 { animation-delay: 0.15s; }
        .stagger-4 { animation-delay: 0.2s; }

        /* Glass effect */
        .glass {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .glass-heavy {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
        }

        /* Gradient text */
        .text-gradient {
            background: linear-gradient(135deg, #10b981 0%, #0d9488 50%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card hover lift */
        .card-hover {
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.1);
        }

        /* Bottom nav safe area */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 16px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased h-[100dvh] overflow-hidden">
    <!-- Full Screen App Container Wrapper -->
    <div class="w-full h-[100dvh] relative flex flex-col bg-slate-50">
    <!-- ===== HEADER ===== -->
    <header class="sticky top-0 z-50 glass-heavy border-b border-slate-200/60 shadow-sm shrink-0">
        <div class="px-5">
            <div class="h-16 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/25 group-hover:shadow-emerald-500/40 transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-base font-black text-slate-800 tracking-tight leading-none">E-Posyandu</h1>
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.2em]">Portal Keluarga</p>
                    </div>
                </a>



                <!-- User Profile -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100">
                        <img class="h-9 w-9 rounded-xl object-cover shadow-sm ring-2 ring-emerald-100" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=10b981&color=fff&bold=true&size=80" alt="Profile" />
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-black text-slate-800 truncate max-w-[120px]">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-emerald-600 font-bold">Warga</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 -translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95 -translate-y-2" style="display: none;" class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl shadow-slate-200/50 border border-slate-100 p-2 z-50">
                        <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl mb-2">
                            <p class="text-sm font-black text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-emerald-600 font-bold mt-0.5">Orang Tua / Warga</p>
                        </div>
                        <a href="{{ route('warga.profile.edit') }}" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition-all">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Pengaturan Akun
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="flex-1 px-5 py-6 pb-36 overflow-y-auto overflow-x-hidden">
        @yield('content')
    </main>

    <!-- ===== BOTTOM NAV (Mobile Only) ===== -->
    <nav class="absolute bottom-0 inset-x-0 z-50 glass-heavy border-t border-slate-200/60 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] rounded-b-none lg:rounded-b-3xl">
        <div class="flex items-center justify-center gap-2 sm:gap-8 md:gap-16 py-3 pb-safe">
            <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-1 px-3 sm:px-5 py-1.5 rounded-2xl transition-all {{ request()->routeIs('warga.dashboard') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-400 hover:text-emerald-600' }}">
                <svg class="w-7 h-7" fill="{{ request()->routeIs('warga.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('warga.dashboard') ? '0' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[11px] font-extrabold">Beranda</span>
            </a>
            <a href="{{ route('warga.riwayat') }}" class="flex flex-col items-center gap-1 px-3 sm:px-5 py-1.5 rounded-2xl transition-all {{ request()->routeIs('warga.riwayat') || request()->routeIs('warga.kms') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-400 hover:text-emerald-600' }}">
                <svg class="w-7 h-7" fill="{{ request()->routeIs('warga.riwayat') || request()->routeIs('warga.kms') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('warga.riwayat') || request()->routeIs('warga.kms') ? '0' : '2' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="text-[11px] font-extrabold">Riwayat</span>
            </a>
            <a href="{{ route('warga.edukasi.index') }}" class="flex flex-col items-center gap-1 px-3 sm:px-5 py-1.5 rounded-2xl transition-all {{ request()->routeIs('warga.edukasi.*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-400 hover:text-emerald-600' }}">
                <svg class="w-7 h-7" fill="{{ request()->routeIs('warga.edukasi.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('warga.edukasi.*') ? '0' : '2' }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[11px] font-extrabold">Edukasi</span>
            </a>
            <a href="{{ route('warga.profile.edit') }}" class="flex flex-col items-center gap-1 px-3 sm:px-5 py-1.5 rounded-2xl transition-all {{ request()->routeIs('warga.profile.*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-400 hover:text-emerald-600' }}">
                <svg class="w-7 h-7" fill="{{ request()->routeIs('warga.profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('warga.profile.*') ? '0' : '2' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[11px] font-extrabold">Profil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex flex-col items-center gap-1 px-3 sm:px-5 py-1.5 rounded-2xl text-slate-400 hover:text-rose-500 transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="text-[11px] font-extrabold">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- ===== TOAST NOTIFICATIONS ===== -->
    <div x-data="{ show: {{ session('success') || session('error') ? 'true' : 'false' }}, message: '{{ session('success') ?? session('error') }}', type: '{{ session('success') ? 'success' : 'error' }}' }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         style="display: none;"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-[100] w-[90vw] max-w-md">
        <div class="flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl border"
             :class="type === 'success' ? 'bg-white border-emerald-200 text-emerald-700' : 'bg-white border-rose-200 text-rose-700'">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                 :class="type === 'success' ? 'bg-emerald-100' : 'bg-rose-100'">
                <svg x-show="type === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <svg x-show="type === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <span class="text-sm font-bold" x-text="message"></span>
        </div>
    </div>

</body>
</html>
