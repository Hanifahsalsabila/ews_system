<x-kader-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Halo, Kader {{ explode(' ', auth()->user()->name)[0] }}!</h1>
        <p class="text-slate-500 mt-2 font-medium">Selamat bertugas. Silakan pilih menu di bawah ini untuk memulai operasional Posyandu.</p>
    </div>

    <!-- Active Session Alert -->
    @if($activeSession)
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-500/20 mb-10 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                    </span>
                    <span class="font-bold uppercase tracking-wider text-xs text-emerald-50">Sesi Sedang Berjalan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black mb-1">{{ $activeSession->nama_sesi }}</h2>
                <p class="text-emerald-100 font-medium">Sesi Aktif &bull; {{ \Carbon\Carbon::parse($activeSession->tanggal)->translatedFormat('l, d F Y') }}</p>
                
                <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:gap-8 items-start sm:items-end justify-between">
                    <div class="flex gap-4 w-full sm:w-auto">
                        <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/20 flex-1 sm:flex-none min-w-[120px]">
                            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Total Hadir</p>
                            <p class="text-3xl font-black">{{ $stats['hadir_hari_ini'] }} <span class="text-sm font-medium text-emerald-100">Orang</span></p>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/20 flex-1 sm:flex-none min-w-[120px]">
                            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Antrean Ukur</p>
                            <p class="text-3xl font-black">{{ $stats['menunggu_pengukuran'] }} <span class="text-sm font-medium text-emerald-100">Orang</span></p>
                        </div>
                    </div>
                    
                    <form action="{{ route('kader.sesi.toggle', $activeSession->id) }}" method="POST" class="w-full sm:w-auto mt-2 sm:mt-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengakhiri sesi posyandu hari ini?')" class="w-full sm:w-auto px-6 py-4 bg-white/10 hover:bg-rose-500 text-white font-bold rounded-2xl transition border border-white/20 hover:border-rose-500 backdrop-blur-md shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Akhiri Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white border border-slate-100 rounded-3xl p-8 sm:p-12 text-center mb-10 shadow-sm max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-teal-50 text-teal-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-2">Belum Ada Sesi Aktif Hari Ini</h2>
            <p class="text-slate-500 font-medium mb-8">Buka sesi posyandu baru untuk memulai pendaftaran kehadiran dan pengukuran warga hari ini.</p>
            
            <form action="{{ route('kader.sesi.store') }}" method="POST" class="max-w-md mx-auto text-left bg-slate-50 p-6 rounded-2xl border border-slate-100">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Sesi Posyandu <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_sesi" value="Posyandu {{ now()->translatedFormat('F Y') }}" required class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    @error('nama_sesi') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-teal-500/30 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Buka Sesi Sekarang
                </button>
            </form>
        </div>
    @endif

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Meja 1 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd"></path><path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z"></path></svg>
            </div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Meja 1: Absensi Kehadiran</h3>
                <p class="text-slate-500 font-medium mb-8">Catat kehadiran warga yang datang ke Posyandu menggunakan Scan QR Code atau cari berdasarkan NIK/Nama.</p>
                
                <a href="{{ route('kader.pendaftaran.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-500/20">
                    Mulai Scan Kehadiran
                </a>
            </div>
        </div>

        <!-- Meja 2 & 3 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-teal-500/10 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
            </div>

            <div class="relative z-10">
                <div class="w-14 h-14 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Meja 2 & 3: Pengukuran</h3>
                <p class="text-slate-500 font-medium mb-8">Lakukan penimbangan berat badan, tinggi badan, dan pengukuran lingkar kepala/lengan untuk warga yang telah hadir.</p>
                
                <a href="{{ route('kader.pengukuran.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition shadow-lg shadow-teal-500/20">
                    Input Hasil Ukur
                </a>
            </div>
        </div>

    </div>
</x-kader-layout>
