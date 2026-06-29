<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Posyandu | Platform Posyandu Digital Terpadu</title>
    <meta name="description" content="Sistem informasi posyandu digital terpadu untuk pemantauan tumbuh kembang balita, deteksi stunting, dan pelayanan kesehatan ibu & anak.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *{font-family:'Plus Jakarta Sans',sans-serif}
        [x-cloak]{display:none!important}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        @keyframes slideIn{from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:translateX(0)}}
        @keyframes pulse-glow{0%,100%{box-shadow:0 0 20px rgba(16,185,129,.2)}50%{box-shadow:0 0 40px rgba(16,185,129,.4)}}
        @keyframes count{from{opacity:0;transform:scale(.5)}to{opacity:1;transform:scale(1)}}
        .animate-float{animation:float 6s ease-in-out infinite}
        .animate-float-delay{animation:float 6s ease-in-out 2s infinite}
        .animate-fade-up{animation:fadeUp .8s ease-out forwards}
        .animate-slide-in{animation:slideIn .6s ease-out forwards}
        .delay-1{animation-delay:.2s;opacity:0}
        .delay-2{animation-delay:.4s;opacity:0}
        .delay-3{animation-delay:.6s;opacity:0}
        .delay-4{animation-delay:.8s;opacity:0}
        .gradient-text{background:linear-gradient(135deg,#059669,#0d9488,#0891b2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .glass{background:rgba(255,255,255,.7);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
        .card-hover{transition:all .4s cubic-bezier(.4,0,.2,1)}
        .card-hover:hover{transform:translateY(-8px);box-shadow:0 25px 50px -12px rgba(0,0,0,.15)}
        .hero-bg{background:linear-gradient(135deg,#ecfdf5 0%,#f0fdfa 30%,#f0f9ff 60%,#faf5ff 100%)}
        .blob{border-radius:30% 70% 70% 30%/30% 30% 70% 70%}
    </style>
</head>
<body class="antialiased bg-white text-slate-800 overflow-x-hidden" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 50">

<!-- Navbar -->
<nav class="fixed w-full z-50 transition-all duration-500" :class="scrolled ? 'glass border-b border-white/50 shadow-lg shadow-black/[.03] py-2' : 'bg-transparent py-4'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-shadow duration-300">P</div>
                <div>
                    <span class="font-black text-xl text-slate-900 tracking-tight block leading-none">E-Posyandu</span>
                    <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-[.2em]">Digital Terpadu</span>
                </div>
            </a>
            <div class="hidden md:flex items-center gap-2">
                <a href="#fitur" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 rounded-xl hover:bg-emerald-50 transition-all">Fitur</a>
                <a href="#alur" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 rounded-xl hover:bg-emerald-50 transition-all">Alur Kerja</a>
                <a href="#statistik" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 rounded-xl hover:bg-emerald-50 transition-all">Statistik</a>
                <div class="w-px h-6 bg-slate-200 mx-2"></div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all">Dashboard →</a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all">Masuk</a>
                @endauth
            </div>
            <button @click="mobileMenu = !mobileMenu" class="md:hidden w-10 h-10 rounded-xl bg-white/80 border border-slate-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition x-cloak class="md:hidden glass border-t border-white/50 mt-2 mx-4 rounded-2xl p-4 space-y-2 shadow-xl">
        <a href="#fitur" @click="mobileMenu=false" class="block px-4 py-3 text-sm font-bold text-slate-700 hover:bg-emerald-50 rounded-xl">Fitur Unggulan</a>
        <a href="#alur" @click="mobileMenu=false" class="block px-4 py-3 text-sm font-bold text-slate-700 hover:bg-emerald-50 rounded-xl">Alur Kerja</a>
        <a href="#statistik" @click="mobileMenu=false" class="block px-4 py-3 text-sm font-bold text-slate-700 hover:bg-emerald-50 rounded-xl">Statistik</a>
        <div class="border-t border-slate-100 pt-2">
            @auth
                <a href="{{ url('/dashboard') }}" class="block w-full text-center px-4 py-3 bg-emerald-500 text-white rounded-xl font-bold text-sm">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 bg-emerald-500 text-white rounded-xl font-bold text-sm">Masuk</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-bg relative min-h-screen flex items-center overflow-hidden pt-20">
    <div class="absolute top-20 left-10 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 right-10 w-80 h-80 bg-teal-200/40 rounded-full blur-3xl animate-float-delay"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-100/20 blob blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-full px-4 py-2 mb-6 animate-fade-up">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Sistem Posyandu Generasi Baru</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6 animate-fade-up delay-1">
                    Modernisasi<br>Pelayanan<br>
                    <span class="gradient-text">Posyandu Desa</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-500 font-medium leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0 animate-fade-up delay-2">
                    Pantau tumbuh kembang balita, deteksi stunting lebih dini, dan kelola data kesehatan secara akurat dengan platform digital terpadu.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start animate-fade-up delay-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold text-base shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all text-center" style="animation:pulse-glow 3s infinite">Buka Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold text-base shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all text-center" style="animation:pulse-glow 3s infinite">Masuk Sekarang</a>
                    @endauth
                    <a href="#fitur" class="px-8 py-4 bg-white border-2 border-slate-200 text-slate-700 rounded-2xl font-bold text-base hover:border-emerald-300 hover:bg-emerald-50 transition-all text-center">Jelajahi Fitur ↓</a>
                </div>
                <div class="flex items-center gap-8 mt-10 justify-center lg:justify-start animate-fade-up delay-4">
                    <div><p class="text-2xl font-black text-slate-900">4</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Role Akses</p></div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div><p class="text-2xl font-black text-slate-900">PWA</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Offline Ready</p></div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div><p class="text-2xl font-black text-slate-900">EWS</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Peringatan Dini</p></div>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/20 to-teal-400/20 rounded-[3rem] rotate-6 scale-95"></div>
                <img src="{{ asset('images/bg-posyandu.png') }}" alt="Posyandu Digital" class="relative rounded-[2.5rem] shadow-2xl shadow-emerald-900/10 border-4 border-white/80 animate-float w-full object-cover" style="animation-duration:8s">
                <div class="absolute -bottom-4 -left-4 glass rounded-2xl px-5 py-3 shadow-xl border border-white/50 animate-slide-in">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center"><span class="text-lg">📊</span></div>
                        <div><p class="text-xs font-black text-slate-800">Z-Score Otomatis</p><p class="text-[10px] text-emerald-600 font-bold">WHO Standard</p></div>
                    </div>
                </div>
                <div class="absolute -top-4 -right-4 glass rounded-2xl px-5 py-3 shadow-xl border border-white/50 animate-slide-in" style="animation-delay:.3s">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center"><span class="text-lg">🔔</span></div>
                        <div><p class="text-xs font-black text-slate-800">Deteksi Stunting</p><p class="text-[10px] text-rose-600 font-bold">Early Warning</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="fitur" class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-black text-emerald-600 uppercase tracking-[.25em] bg-emerald-50 px-4 py-2 rounded-full mb-4">Fitur Unggulan</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Solusi Lengkap untuk<br><span class="gradient-text">Setiap Peran</span></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $features = [
                ['icon'=>'🛡️','color'=>'emerald','title'=>'Admin Posyandu','desc'=>'Kontrol penuh atas master data, manajemen akun, audit trail, dan pemulihan data dengan Soft Deletes.','tags'=>['RBAC','Audit Trail','Trash Bin']],
                ['icon'=>'📈','color'=>'blue','title'=>'Bidan / Nakes','desc'=>'Dashboard analitik real-time, Early Warning System (EWS), validasi data, dan penjadwalan kunjungan rumah.','tags'=>['EWS','Validasi','Laporan SKDN']],
                ['icon'=>'📋','color'=>'amber','title'=>'Kader Posyandu','desc'=>'Entri data cepat sistem 5 meja, Z-Score otomatis, checklist layanan, dan dukungan mode offline PWA.','tags'=>['Offline','Z-Score','Gamifikasi']],
                ['icon'=>'👨‍👩‍👧','color'=>'purple','title'=>'Orang Tua','desc'=>'KMS digital mandiri, riwayat imunisasi, rekomendasi MPASI cerdas, dan lencana kehadiran.','tags'=>['KMS','MPASI','Badge']],
            ];
            @endphp
            @foreach($features as $i => $f)
            <div class="card-hover bg-gradient-to-br from-{{ $f['color'] }}-50/80 to-white p-7 rounded-3xl border border-{{ $f['color'] }}-100/50 group">
                <div class="w-14 h-14 bg-{{ $f['color'] }}-100 rounded-2xl flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">{{ $f['icon'] }}</div>
                <h3 class="text-lg font-black text-slate-800 mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed mb-4">{{ $f['desc'] }}</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($f['tags'] as $tag)
                    <span class="text-[10px] font-bold text-{{ $f['color'] }}-600 bg-{{ $f['color'] }}-50 px-2.5 py-1 rounded-lg border border-{{ $f['color'] }}-100">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Workflow Section -->
<section id="alur" class="py-24 bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20">
            <span class="inline-block text-xs font-black text-teal-600 uppercase tracking-[.25em] bg-teal-50 px-4 py-2 rounded-full mb-4 border border-teal-100">Alur Kerja</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-4">Sistem 5 Meja<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">Terintegrasi Digital</span></h2>
            <p class="text-slate-500 font-medium max-w-2xl mx-auto">Alur pelayanan posyandu yang sistematis, cepat, dan termonitor secara real-time dari meja pendaftaran hingga meja pelayanan kesehatan.</p>
        </div>

        <div class="relative">
            <!-- Connecting Line for Desktop -->
            <div class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-gradient-to-r from-emerald-100 via-teal-100 to-blue-100 -translate-y-1/2 z-0 rounded-full opacity-60"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-6 relative z-10">
                @php
                $steps = [
                    ['num'=>'1','title'=>'Pendaftaran','desc'=>'Scan QR / NIK warga untuk registrasi antrean cepat','icon'=>'📝','bg'=>'bg-emerald-50','text'=>'text-emerald-600','badge'=>'bg-emerald-500','border'=>'border-emerald-200','shadow'=>'hover:shadow-emerald-500/20'],
                    ['num'=>'2','title'=>'Penimbangan','desc'=>'Input data Berat & Tinggi Badan dengan validasi otomatis','icon'=>'⚖️','bg'=>'bg-blue-50','text'=>'text-blue-600','badge'=>'bg-blue-500','border'=>'border-blue-200','shadow'=>'hover:shadow-blue-500/20'],
                    ['num'=>'3','title'=>'Pencatatan','desc'=>'Kalkulasi Z-Score standar WHO dihitung secara real-time','icon'=>'📊','bg'=>'bg-amber-50','text'=>'text-amber-600','badge'=>'bg-amber-500','border'=>'border-amber-200','shadow'=>'hover:shadow-amber-500/20'],
                    ['num'=>'4','title'=>'Penyuluhan','desc'=>'Rekomendasi gizi MPASI & edukasi ibu berbasis algoritma','icon'=>'💡','bg'=>'bg-purple-50','text'=>'text-purple-600','badge'=>'bg-purple-500','border'=>'border-purple-200','shadow'=>'hover:shadow-purple-500/20'],
                    ['num'=>'5','title'=>'Pelayanan','desc'=>'Pemberian imunisasi, vitamin A, dan obat cacing balita','icon'=>'💉','bg'=>'bg-rose-50','text'=>'text-rose-600','badge'=>'bg-rose-500','border'=>'border-rose-200','shadow'=>'hover:shadow-rose-500/20'],
                ];
                @endphp
                @foreach($steps as $s)
                <div class="bg-white rounded-3xl p-6 border {{ $s['border'] }} shadow-xl shadow-slate-200/50 hover:-translate-y-2 {{ $s['shadow'] }} transition-all duration-300 group relative flex flex-col items-center text-center">
                    <!-- Step Number Badge -->
                    <div class="absolute -top-5 w-10 h-10 {{ $s['badge'] }} rounded-full flex items-center justify-center text-white font-black shadow-lg border-4 border-white z-20 group-hover:scale-110 transition-transform">
                        {{ $s['num'] }}
                    </div>
                    
                    <div class="w-16 h-16 {{ $s['bg'] }} {{ $s['text'] }} rounded-2xl flex items-center justify-center text-3xl mb-5 mt-2 group-hover:scale-110 group-hover:-rotate-3 transition-transform shadow-sm border border-slate-100">
                        {{ $s['icon'] }}
                    </div>
                    <h4 class="text-lg font-black text-slate-800 mb-2">{{ $s['title'] }}</h4>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Keunggulan / Inovasi Section -->
<section id="statistik" class="py-24 bg-slate-50 relative overflow-hidden">
    <!-- Soft Glowing orbs -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-200/40 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-teal-200/40 rounded-full blur-[100px] pointer-events-none"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-black text-emerald-600 uppercase tracking-[.25em] bg-emerald-100 px-4 py-2 rounded-full mb-4 border border-emerald-200">Teknologi Modern</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-4">Inovasi untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">Generasi Sehat</span></h2>
            <p class="text-slate-500 font-medium max-w-2xl mx-auto">Dirancang dengan arsitektur modern untuk memastikan kecepatan, keamanan, dan keandalan rekam medis dari posyandu ke tingkat nasional.</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Big Card 1 -->
            <div class="lg:col-span-2 bg-white border border-slate-200/60 rounded-3xl p-8 lg:p-10 relative overflow-hidden group hover:border-emerald-300 hover:shadow-2xl hover:shadow-emerald-900/5 transition-all duration-500 shadow-xl shadow-slate-200/50">
                <div class="absolute right-0 bottom-0 w-64 h-64 bg-emerald-100/50 rounded-full blur-3xl group-hover:bg-emerald-200/50 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-sm border border-emerald-200">🤖</div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Deteksi Cerdas (EWS) & Z-Score</h3>
                    <p class="text-slate-600 leading-relaxed max-w-lg">Sistem secara otomatis menghitung Z-Score berdasarkan standar WHO, menganalisis tren pertumbuhan balita, dan memunculkan peringatan dini (Early Warning System) jika terdeteksi risiko stunting.</p>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white border border-slate-200/60 rounded-3xl p-8 relative overflow-hidden group hover:border-blue-300 hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 shadow-xl shadow-slate-200/50">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100/50 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-5 border border-blue-200 shadow-sm">🔒</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Keamanan Enterprise</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Dilengkapi sistem Audit Trail dan Soft Deletes untuk mengamankan riwayat rekam medis secara presisi.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white border border-slate-200/60 rounded-3xl p-8 relative overflow-hidden group hover:border-purple-300 hover:shadow-2xl hover:shadow-purple-900/5 transition-all duration-500 shadow-xl shadow-slate-200/50">
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-purple-100/50 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-5 border border-purple-200 shadow-sm">🏆</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Gamifikasi & MPASI</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Lencana (Badge) kehadiran otomatis untuk warga, dan rekomendasi gizi MPASI cerdas yang dipersonalisasi.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="lg:col-span-2 bg-white border border-slate-200/60 rounded-3xl p-8 lg:p-10 relative overflow-hidden group hover:border-teal-300 hover:shadow-2xl hover:shadow-teal-900/5 transition-all duration-500 shadow-xl shadow-slate-200/50">
                <div class="absolute left-0 top-0 w-64 h-64 bg-teal-100/50 rounded-full blur-3xl group-hover:bg-teal-200/50 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-sm border border-teal-200">📱</div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Dukungan Offline (PWA)</h3>
                    <p class="text-slate-600 leading-relaxed max-w-xl">Berada di area susah sinyal? Aplikasi E-Posyandu dapat diinstal di smartphone layaknya aplikasi native dan tetap bisa digunakan untuk input pendataan layanan tanpa koneksi internet sama sekali.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 rounded-[3rem] p-12 sm:p-16 border border-emerald-100/50 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-200/30 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-teal-200/30 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <span class="text-5xl mb-6 block">🚀</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-4">Siap Memulai Transformasi<br>Posyandu Digital?</h2>
                <p class="text-slate-500 font-medium max-w-lg mx-auto mb-8">Bergabung sekarang dan rasakan kemudahan mengelola data kesehatan masyarakat dengan teknologi modern.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all">Buka Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all">Masuk Sekarang →</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 pt-20 pb-10 relative overflow-hidden border-t border-slate-800">
    <!-- Abstract subtle shapes -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8 mb-16">
            
            <!-- Brand Column -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-emerald-500/20">P</div>
                    <div>
                        <p class="font-black text-white tracking-tight text-xl">E-Posyandu</p>
                        <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">Digital Terpadu</p>
                    </div>
                </div>
                <p class="text-slate-400 leading-relaxed text-sm max-w-sm mb-6">
                    Sistem informasi manajemen Posyandu yang terintegrasi, dirancang khusus untuk mempermudah pencatatan, pemantauan gizi, dan deteksi dini stunting pada balita secara digital.
                </p>
                <div class="flex items-center gap-4">
                    <!-- Social icons -->
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.20 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Links Column 1 -->
            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-xs">Akses Modul</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Dashboard Warga</a></li>
                    <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Portal Kader</a></li>
                    <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Panel Bidan</a></li>
                    <li><a href="{{ route('admin.login') }}" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Sistem Administrator</a></li>
                </ul>
            </div>

            <!-- Links Column 2 -->
            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-xs">Pintasan</h4>
                <ul class="space-y-4">
                    <li><a href="#fitur" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Fitur Unggulan</a></li>
                    <li><a href="#alur" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Sistem 5 Meja</a></li>
                    <li><a href="#statistik" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Inovasi Teknologi</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors text-sm">Buku Panduan Kader</a></li>
                </ul>
            </div>

            <!-- Links Column 3 -->
            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-xs">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-slate-400 text-sm">Jl. Kesehatan No. 123, Desa Sejahtera, Kab. Maju</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-slate-400 text-sm">support@eposyandu.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="text-slate-400 text-sm">0800-123-4567</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 text-sm text-center md:text-left">
                &copy; {{ date('Y') }} E-Posyandu. Bersama Mewujudkan <span class="text-emerald-400 font-medium">Indonesia Bebas Stunting 🇮🇩</span>
            </p>
            <div class="flex items-center gap-6 text-sm">
                <a href="#" class="text-slate-500 hover:text-white transition-colors">Kebijakan Privasi</a>
                <a href="#" class="text-slate-500 hover:text-white transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
