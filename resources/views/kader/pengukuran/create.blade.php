<x-kader-layout>
    <div class="mb-8">
        <a href="{{ route('kader.pengukuran.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-teal-600 transition font-medium text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Antrean
        </a>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Input Pengukuran</h1>
        <p class="text-slate-500 mt-1 font-medium">Sesi: {{ $activeSession->nama_sesi }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Data Warga Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-teal-50 border border-teal-100 rounded-3xl p-6 sticky top-24 shadow-sm">
                <div class="w-16 h-16 bg-teal-600 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-4 shadow-lg shadow-teal-600/30">
                    {{ substr($warga->nama, 0, 1) }}
                </div>
                <h3 class="font-black text-xl text-slate-800 mb-1">{{ $warga->nama }}</h3>
                <p class="text-slate-500 font-mono text-sm mb-4">{{ $warga->nik }}</p>

                <div class="space-y-3 pt-4 border-t border-teal-200/50">
                    @if($warga->balita)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teal-700 font-bold">Kategori</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-bold">Balita</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teal-700 font-bold">Tgl Lahir</span>
                            <span class="text-slate-700 font-medium">{{ $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teal-700 font-bold">Umur</span>
                            <span class="text-slate-700 font-medium">{{ $warga->umur_formatted }}</span>
                        </div>
                    @elseif($warga->ibuHamil)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teal-700 font-bold">Kategori</span>
                            <span class="bg-pink-100 text-pink-800 px-2 py-0.5 rounded font-bold">Ibu Hamil</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teal-700 font-bold">Kategori</span>
                            <span class="bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-bold">Umum / Lansia</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Input -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                <form action="{{ route('kader.pengukuran.store', $warga->id) }}" method="POST">
                    @csrf
                    
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        </div>
                        Hasil Ukur Hari Ini
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Berat Badan -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Berat Badan</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="berat_badan" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-4 pr-12 py-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-lg" placeholder="0.0">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">kg</span>
                            </div>
                        </div>

                        <!-- Tinggi Badan -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tinggi/Panjang Badan</label>
                            <div class="relative">
                                <input type="number" step="0.1" name="tinggi_badan" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-4 pr-12 py-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-lg" placeholder="0.0">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">cm</span>
                            </div>
                        </div>

                        @if($warga->balita || $warga->ibuHamil)
                            <!-- Lingkar Kepala -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Lingkar Kepala</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="lingkar_kepala" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-4 pr-12 py-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-lg" placeholder="0.0">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">cm</span>
                                </div>
                            </div>

                            <!-- Lingkar Lengan (LILA) -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Lingkar Lengan (LILA)</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="lingkar_lengan" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-4 pr-12 py-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-lg" placeholder="0.0">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">cm</span>
                                </div>
                            </div>
                        @endif

                        @if($warga->ibuHamil || !($warga->balita))
                            <!-- Tensi Darah -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tensi Darah (Sistolik/Diastolik)</label>
                                <div class="relative">
                                    <input type="text" name="tensi_darah" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl pl-4 pr-16 py-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-bold text-lg" placeholder="120/80">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">mmHg</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 mb-6">
                            <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-medium text-blue-800">Setelah disimpan, data ini akan diteruskan ke antrean Bidan untuk divalidasi dan diberikan tindakan medis (Meja 5).</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl transition shadow-xl shadow-teal-500/30 text-lg">
                            Simpan & Teruskan ke Bidan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-kader-layout>
