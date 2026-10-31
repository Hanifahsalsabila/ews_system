<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tindak Lanjut EWS</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('bidan.dashboard') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 font-bold hover:text-blue-700 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Tindak Lanjut EWS</h1>
                <p class="text-slate-500 mt-1 font-medium">Lengkapi tindakan medis atau catatan rujukan untuk balita berisiko.</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-medium">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-slate-800 to-slate-700 text-white">
                <h3 class="text-xl font-black">Informasi Pemeriksaan</h3>
                <p class="text-slate-300 text-sm mt-1">Pasien: <span class="font-bold text-white">{{ $pemeriksaan->warga->nama }}</span> (NIK: {{ $pemeriksaan->warga->nik }})</p>
            </div>

            <div class="p-8">
                <!-- Data Read-Only -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Antropometri Terakhir</p>
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 space-y-3">
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-sm font-medium text-slate-600">Tanggal Pemeriksaan</span>
                                <span class="text-sm font-bold text-slate-800">{{ $pemeriksaan->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-sm font-medium text-slate-600">Berat Badan (BB)</span>
                                <span class="text-sm font-bold text-slate-800">{{ $pemeriksaan->berat_badan ?? '-' }} kg</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-sm font-medium text-slate-600">Panjang/Tinggi Badan (PB/TB)</span>
                                <span class="text-sm font-bold text-slate-800">{{ $pemeriksaan->tinggi_badan ?? '-' }} cm ({{ $pemeriksaan->posisi_ukur }})</span>
                            </div>
                        </div>
                    </div>

                    @if($pemeriksaan->kalkulasiZscore)
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Hasil Z-Score (EWS)</p>
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5 space-y-3">
                            <div class="flex justify-between items-center border-b border-rose-200/50 pb-2">
                                <span class="text-sm font-medium text-rose-700">Z-Score BB/U</span>
                                <span class="text-sm font-bold {{ $pemeriksaan->kalkulasiZscore->zscore_bb_u < -2 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $pemeriksaan->kalkulasiZscore->zscore_bb_u !== null ? number_format($pemeriksaan->kalkulasiZscore->zscore_bb_u, 2) : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-rose-200/50 pb-2">
                                <span class="text-sm font-medium text-rose-700">Z-Score TB/U</span>
                                <span class="text-sm font-bold {{ $pemeriksaan->kalkulasiZscore->zscore_tb_u < -2 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $pemeriksaan->kalkulasiZscore->zscore_tb_u !== null ? number_format($pemeriksaan->kalkulasiZscore->zscore_tb_u, 2) : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-rose-200/50 pb-2">
                                <span class="text-sm font-medium text-rose-700">Z-Score BB/TB</span>
                                <span class="text-sm font-bold {{ $pemeriksaan->kalkulasiZscore->zscore_bb_tb < -2 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $pemeriksaan->kalkulasiZscore->zscore_bb_tb !== null ? number_format($pemeriksaan->kalkulasiZscore->zscore_bb_tb, 2) : '-' }}</span>
                            </div>
                            <div class="pt-2 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex px-3 py-1 bg-rose-200 text-rose-800 text-xs font-bold rounded-lg uppercase">{{ $pemeriksaan->kalkulasiZscore->ews_status }}</span>
                                    <span class="text-xs font-black text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-200">
                                        Skor Prioritas: {{ number_format((float) ($pemeriksaan->kalkulasiZscore->priority_score ?? 0), 1) }} / 100
                                    </span>
                                </div>
                                <p class="text-xs text-rose-600 font-medium leading-relaxed">{{ $pemeriksaan->kalkulasiZscore->ews_message }}</p>
                                @if(!empty($pemeriksaan->kalkulasiZscore->priority_details['summary']))
                                    <div class="p-2.5 rounded-xl bg-white/80 border border-rose-200/60 text-[11px] text-slate-600 font-medium">
                                        <span class="font-bold text-slate-700">Faktor Risiko Klinis:</span> {{ $pemeriksaan->kalkulasiZscore->priority_details['summary'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Editable Form -->
                <form action="{{ route('bidan.pemeriksaan.tindak-lanjut.update', $pemeriksaan->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tindakan Medis / Rujukan</label>
                            <textarea name="tindakan" rows="3" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: Dirujuk ke Puskesmas, Pemberian PMT Pemulihan, dll.">{{ old('tindakan', $pemeriksaan->tindakan) }}</textarea>
                            @error('tindakan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Tambahan</label>
                            <textarea name="catatan" rows="2" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Kondisi klinis tambahan (jika ada)">{{ old('catatan', $pemeriksaan->catatan) }}</textarea>
                            @error('catatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('bidan.dashboard') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-100 transition">Batal</a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Simpan Tindak Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-bidan-layout>
