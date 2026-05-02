<x-bidan-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('bidan.kunjungan.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight">Laporan Kunjungan Rumah</h2>
                <p class="text-sm font-medium text-slate-500 mt-1">{{ $kunjungan->warga->nama ?? '-' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Kunjungan --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">Detail Kunjungan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Nama Warga</p>
                        <p class="font-bold text-slate-700">{{ $kunjungan->warga->nama }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Tipe</p>
                        <p class="font-bold text-slate-700">{{ $kunjungan->tipe }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Prioritas</p>
                        <p class="font-bold text-slate-700 capitalize">{{ $kunjungan->prioritas }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Rencana Kunjungan</p>
                        <p class="font-bold text-slate-700">{{ $kunjungan->tanggal_rencana?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                </div>

                @if($kunjungan->catatan_sebelum)
                <div class="mt-4 bg-amber-50 rounded-xl p-4 border border-amber-100">
                    <p class="text-[10px] font-black text-amber-600 uppercase mb-1">Catatan EWS</p>
                    <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $kunjungan->catatan_sebelum }}</p>
                </div>
                @endif
            </div>

            {{-- Riwayat Pemeriksaan Warga --}}
            @if($kunjungan->warga->pemeriksaans->count() > 0)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">Riwayat Pemeriksaan</h3>
                <div class="space-y-2">
                    @foreach($kunjungan->warga->pemeriksaans->take(5) as $p)
                    <div class="flex items-center gap-4 py-2 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                        <p class="text-xs font-bold text-slate-600 w-24">{{ $p->created_at->translatedFormat('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500">BB: {{ $p->berat_badan ?? '-' }} kg</p>
                        <p class="text-xs text-slate-500">TB: {{ $p->tinggi_badan ?? '-' }} cm</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form Laporan --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">Form Laporan Follow-Up</h3>

                <form method="POST" action="{{ route('bidan.kunjungan.update', $kunjungan) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1.5">Tanggal Kunjungan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', now()->format('Y-m-d')) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @error('tanggal_kunjungan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1.5">Hasil Kunjungan / Temuan <span class="text-rose-500">*</span></label>
                        <textarea name="catatan_hasil" rows="4" required placeholder="Jelaskan kondisi yang ditemukan saat kunjungan rumah..."
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none">{{ old('catatan_hasil', $kunjungan->catatan_hasil) }}</textarea>
                        @error('catatan_hasil') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1.5">Rencana Tindak Lanjut</label>
                        <textarea name="tindak_lanjut" rows="3" placeholder="Apa langkah selanjutnya yang perlu dilakukan?"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none">{{ old('tindak_lanjut', $kunjungan->tindak_lanjut) }}</textarea>
                        @error('tindak_lanjut') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1.5">Status</label>
                        <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="selesai" {{ $kunjungan->status == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="ditunda" {{ $kunjungan->status == 'ditunda' ? 'selected' : '' }}>⏳ Ditunda</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="bg-emerald-500 text-white font-bold text-sm px-6 py-3 rounded-2xl hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/20">
                            Simpan Laporan
                        </button>
                        <a href="{{ route('bidan.kunjungan.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 px-4 py-3">Batal</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-bidan-layout>
