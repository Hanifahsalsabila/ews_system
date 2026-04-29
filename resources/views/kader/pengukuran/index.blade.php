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
    @endif
</x-kader-layout>
