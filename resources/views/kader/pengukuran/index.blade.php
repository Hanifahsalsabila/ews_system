<x-kader-layout>
    <div class="mb-8">
        <div class="flex items-center gap-2 text-teal-600 mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
            <span class="font-bold text-sm tracking-widest uppercase">Meja 2 & 3</span>
        </div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Antrean Pengukuran</h1>
        <p class="text-slate-500 mt-1 font-medium">Daftar warga yang sudah mendaftar hari ini dan belum dilakukan pengukuran.</p>
    </div>

    @if(!$activeSession)
        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-8 text-center">
            <h2 class="text-xl font-bold text-rose-800 mb-2">Tidak Ada Sesi Aktif</h2>
            <p class="text-rose-600 font-medium">Sesi posyandu belum dibuka atau sudah ditutup.</p>
        </div>
    @else
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-slate-800">Menunggu Diukur</h3>
                <span class="bg-teal-100 text-teal-700 py-1 px-3 rounded-lg text-xs font-bold tracking-widest">{{ $antrean->count() }} Orang</span>
            </div>

            @if($antrean->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-700 mb-1">Antrean Kosong</h4>
                    <p class="text-slate-500 font-medium">Semua warga yang hadir sudah diukur atau belum ada yang mendaftar di Meja 1.</p>
                    <a href="{{ route('kader.pendaftaran.index') }}" class="inline-block mt-4 text-teal-600 font-bold hover:text-teal-700 underline">Lihat Pendaftaran Meja 1</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($antrean as $absen)
                        <div class="border border-slate-200 rounded-2xl p-5 hover:border-teal-300 hover:shadow-lg hover:shadow-teal-500/10 transition-all group bg-slate-50 hover:bg-white relative">
                            <!-- Kategori Badge -->
                            <div class="absolute top-4 right-4">
                                @if($absen->warga->balita)
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Balita</span>
                                @elseif($absen->warga->ibuHamil)
                                    <span class="bg-pink-100 text-pink-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Bumil</span>
                                @else
                                    <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Umum</span>
                                @endif
                            </div>

                            <div class="flex items-start gap-4 mb-5">
                                <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center font-bold text-lg shrink-0">
                                    {{ substr($absen->warga->nama, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-lg leading-tight pr-10">{{ $absen->warga->nama }}</h4>
                                    <p class="text-xs text-slate-500 font-mono mt-1">{{ $absen->warga->nik }}</p>
                                </div>
                            </div>
                            
                            <a href="{{ route('kader.pengukuran.create', $absen->warga->id) }}" class="block w-full text-center bg-slate-800 group-hover:bg-teal-600 text-white font-bold py-2.5 rounded-xl transition">
                                Mulai Ukur
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== SUDAH DIUKUR HARI INI ===== --}}
        @if($sudahDiukur->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm mt-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-slate-800">✅ Sudah Diukur Hari Ini</h3>
                <span class="bg-emerald-100 text-emerald-700 py-1 px-3 rounded-lg text-xs font-bold tracking-widest">{{ $sudahDiukur->count() }} Orang</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sudahDiukur as $periksa)
                    <div class="border border-emerald-100 bg-emerald-50/30 rounded-2xl p-5 relative">
                        {{-- Kategori Badge --}}
                        <div class="absolute top-4 right-4">
                            @if($periksa->warga->balita)
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Balita</span>
                            @elseif($periksa->warga->ibuHamil)
                                <span class="bg-pink-100 text-pink-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Bumil</span>
                            @else
                                <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Umum</span>
                            @endif
                        </div>

                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg shrink-0">
                                ✓
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-lg leading-tight pr-10">{{ $periksa->warga->nama }}</h4>
                                <p class="text-xs text-slate-500 font-mono mt-1">{{ $periksa->warga->nik }}</p>
                            </div>
                        </div>

                        {{-- Measurement Results --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if($periksa->berat_badan)
                            <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold">BB</p>
                                <p class="text-sm font-black text-slate-700">{{ $periksa->berat_badan }} kg</p>
                            </div>
                            @endif
                            @if($periksa->tinggi_badan)
                            <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold">TB</p>
                                <p class="text-sm font-black text-slate-700">{{ $periksa->tinggi_badan }} cm</p>
                            </div>
                            @endif
                            @if($periksa->lingkar_kepala)
                            <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold">LK</p>
                                <p class="text-sm font-black text-slate-700">{{ $periksa->lingkar_kepala }} cm</p>
                            </div>
                            @endif
                            @if($periksa->lingkar_lengan)
                            <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold">LILA</p>
                                <p class="text-sm font-black text-slate-700">{{ $periksa->lingkar_lengan }} cm</p>
                            </div>
                            @endif
                            @if($periksa->tensi_darah)
                            <div class="bg-white rounded-xl px-3 py-2 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold">Tensi</p>
                                <p class="text-sm font-black text-slate-700">{{ $periksa->tensi_darah }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mt-3 text-xs text-slate-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $periksa->created_at->format('H:i') }} WIB
                            <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $periksa->is_validated ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} text-[10px] font-bold">
                                {{ $periksa->is_validated ? '✓ Tervalidasi' : '⏳ Menunggu Validasi Bidan' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif
</x-kader-layout>
