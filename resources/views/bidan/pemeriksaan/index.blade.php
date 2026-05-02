<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Pemeriksaan Rutin') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Title -->
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Pemeriksaan & Kohort</h1>
            <p class="text-slate-500 mt-2 font-medium">Pilih kohort warga untuk melakukan pencatatan pemeriksaan rutin posyandu.</p>
        </div>

        @if(!$sesiAktif)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
            <div class="bg-amber-100 p-2 rounded-xl shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-amber-800">Tidak Ada Sesi Aktif</p>
                <p class="text-sm text-amber-700 mt-1">Anda tidak dapat menyimpan pemeriksaan. Hubungi Admin atau Kader untuk membuka sesi Posyandu terlebih dahulu.</p>
            </div>
        </div>
        @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
            <div class="bg-emerald-100 p-2 rounded-xl shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-emerald-800">Sesi Posyandu Aktif</p>
                <p class="text-sm text-emerald-700 mt-1">{{ $sesiAktif->nama ?? 'Sesi ' . $sesiAktif->tanggal }} — Anda dapat melakukan pencatatan pemeriksaan.</p>
            </div>
        </div>
        @endif

        <!-- Cohort Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ibu Hamil -->
            <a href="{{ route('bidan.pemeriksaan.ibu-hamil') }}" class="group block bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-rose-200 transition-all duration-300 overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-rose-100 to-pink-100 text-rose-500 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">Ibu Hamil</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Berat Badan, Tensi Darah, LILA, TFU, DJJ, Suplementasi Fe</p>
                    <div class="flex items-center gap-3 w-full">
                        <div class="flex-1 bg-rose-50 rounded-xl py-2 px-3 border border-rose-100">
                            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest">Terdaftar</p>
                            <p class="text-lg font-black text-rose-600">{{ $totalIbuHamil }}</p>
                        </div>
                        <div class="flex-1 bg-emerald-50 rounded-xl py-2 px-3 border border-emerald-100">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Diperiksa</p>
                            <p class="text-lg font-black text-emerald-600">{{ $sudahDiperiksaIbuHamil }}</p>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Balita -->
            <a href="{{ route('bidan.pemeriksaan.balita') }}" class="group block bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-sky-100 text-blue-500 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">Anak / Balita</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Tinggi & Berat Badan, Lingkar Kepala, Imunisasi, Vitamin A</p>
                    <div class="flex items-center gap-3 w-full">
                        <div class="flex-1 bg-blue-50 rounded-xl py-2 px-3 border border-blue-100">
                            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Terdaftar</p>
                            <p class="text-lg font-black text-blue-600">{{ $totalBalita }}</p>
                        </div>
                        <div class="flex-1 bg-emerald-50 rounded-xl py-2 px-3 border border-emerald-100">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Diperiksa</p>
                            <p class="text-lg font-black text-emerald-600">{{ $sudahDiperiksaBalita }}</p>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Lansia -->
            <a href="{{ route('bidan.pemeriksaan.lansia') }}" class="group block bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-amber-200 transition-all duration-300 overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 text-amber-500 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">Lansia</h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">Tensi Darah, Gula Darah, Kolesterol, Asam Urat, IMT</p>
                    <div class="flex items-center gap-3 w-full">
                        <div class="flex-1 bg-amber-50 rounded-xl py-2 px-3 border border-amber-100">
                            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest">Terdaftar</p>
                            <p class="text-lg font-black text-amber-600">{{ $totalLansia }}</p>
                        </div>
                        <div class="flex-1 bg-emerald-50 rounded-xl py-2 px-3 border border-emerald-100">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Diperiksa</p>
                            <p class="text-lg font-black text-emerald-600">{{ $sudahDiperiksaLansia }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-bidan-layout>
