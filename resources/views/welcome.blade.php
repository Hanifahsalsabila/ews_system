<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Posyandu | Platform Posyandu Digital</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-hero-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(#22c55e 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-500/30">
                        P
                    </div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">E-Posyandu</span>
                </div>
                
                @if (Route::has('login'))
                    <div class="flex gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-medium text-emerald-600 hover:text-emerald-500 transition px-4 py-2">
                                Menuju Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="font-medium text-gray-600 hover:text-gray-900 px-4 py-2 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="font-medium bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-lg shadow-md shadow-emerald-500/30 transition transform hover:-translate-y-0.5">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="pt-32 pb-16 sm:pt-40 sm:pb-24 lg:pb-32 bg-hero-pattern overflow-hidden relative">
        <!-- Floating shapes -->
        <div class="absolute top-1/4 left-10 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
        <div class="absolute top-1/3 right-10 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight">
                Modernisasi Pelayanan <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">
                    Posyandu Desa Anda
                </span>
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-600 mx-auto mb-10">
                Tingkatkan efisiensi pendataan warga, pantau tumbuh kembang balita dengan akurat melalui KMS Digital, dan cegah stunting lebih dini dengan analitik E-Posyandu.
            </p>
            
            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-semibold shadow-xl shadow-gray-900/20 transition transform hover:-translate-y-1">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-semibold shadow-xl shadow-emerald-500/30 transition transform hover:-translate-y-1">
                        Masuk Petugas
                    </a>
                    <a href="#fitur" class="px-8 py-3.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-xl font-semibold shadow-sm transition">
                        Pelajari Fitur
                    </a>
                @endauth
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="fitur" class="py-20 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Modul Unggulan Posyandu Terpadu</h2>
                <div class="w-20 h-1.5 bg-emerald-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Fitur 1 -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-2xl hover:shadow-emerald-500/10 transition duration-300">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                        <!-- Icon -->
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Manajemen Kohort</h3>
                    <p class="text-gray-600 leading-relaxed">Penyimpanan sistematis untuk data pendudukan mulai dari Balita, Ibu Hamil, dan Lansia dengan pemetaan yang akurat.</p>
                </div>

                <!-- Fitur 2 -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-2xl hover:shadow-teal-500/10 transition duration-300">
                    <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sistem 5 Meja & Z-Score</h3>
                    <p class="text-gray-600 leading-relaxed">Eksekusi alur posyandu yang presisi. Secara otomatis menghitung indikasi stunting (Z-Score) langsung dari form pengukuran.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-2xl hover:shadow-purple-500/10 transition duration-300">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Executive Dashboard</h3>
                    <p class="text-gray-600 leading-relaxed">Analisa statistik kehadiran dan persentase BGM yang langsung dapat diobservasi oleh Bidan Desa dan Pimpinan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-12 text-center">
        <p class="text-gray-400 font-medium">
            &copy; {{ date('Y') }} E-Posyandu - Posyandu Digital. Dukung Indonesia Bebas Stunting.
        </p>
    </footer>
</body>
</html>
