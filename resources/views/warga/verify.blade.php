<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Warga - E-Posyandu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-sm w-full">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-200 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">E-Posyandu</h1>
            <p class="text-emerald-600 font-medium text-sm">Kartu Digital Warga</p>
        </div>

        <!-- ID Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden relative border border-slate-100">
            <!-- Card Header -->
            <div class="bg-emerald-600 px-6 py-6 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-white text-xs font-bold mb-4 backdrop-blur-sm border border-white/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        KARTU VALID
                    </div>
                    
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-emerald-600 text-3xl font-black mx-auto shadow-md border-4 border-emerald-500 mb-3">
                        {{ strtoupper(substr($warga->nama, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-bold text-white leading-tight">{{ $warga->nama }}</h2>
                    
                    <!-- Obfuscated NIK -->
                    <p class="text-emerald-100 text-sm mt-1 font-mono tracking-widest">
                        {{ substr($warga->nik, 0, 4) }} •••• •••• {{ substr($warga->nik, -4) }}
                    </p>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Klasifikasi</p>
                        @if($warga->balita)
                            <p class="text-sm font-bold text-blue-600 flex items-center gap-1.5"><span class="text-lg">👶</span> Balita</p>
                        @elseif($warga->ibuHamil)
                            <p class="text-sm font-bold text-rose-600 flex items-center gap-1.5"><span class="text-lg">🤰</span> Ibu Hamil</p>
                        @elseif($warga->lansia)
                            <p class="text-sm font-bold text-amber-600 flex items-center gap-1.5"><span class="text-lg">🧓</span> Lansia</p>
                        @else
                            <p class="text-sm font-bold text-slate-600 flex items-center gap-1.5"><span class="text-lg">👤</span> Umum</p>
                        @endif
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Terdaftar Sejak</p>
                        <p class="text-sm font-bold text-slate-700">{{ $warga->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Wilayah Posyandu</p>
                    <p class="text-sm font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Dusun {{ $warga->region->dusun_name ?? '-' }}<br><span class="text-xs text-slate-500 font-medium">RW {{ str_pad($warga->region->rw_name ?? 0, 2, '0', STR_PAD_LEFT) }} / RT {{ str_pad($warga->region->rt_name ?? 0, 2, '0', STR_PAD_LEFT) }}</span></span>
                    </p>
                </div>
            </div>
            
            <div class="bg-emerald-50 p-4 text-center border-t border-emerald-100">
                <p class="text-xs text-emerald-700 font-medium">Tunjukkan kartu digital ini kepada petugas Posyandu untuk mempermudah pendaftaran.</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-6 space-y-3">
            @auth
                <!-- Button for Kader/Admin to go to examination directly if they are logged in -->
                <a href="{{ route('admin.wargas.index') }}" class="block w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-center text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95">
                    Kembali ke Dashboard Kader
                </a>
            @else
                <a href="{{ route('login') }}" class="block w-full py-3.5 px-4 bg-white border border-slate-200 text-slate-700 text-center text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all">
                    Login sebagai Petugas
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
