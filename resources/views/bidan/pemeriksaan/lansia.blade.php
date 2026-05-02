<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pemeriksaan Lansia</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, selectedId: null, selectedName: '', selectedGolDar: '', selectedPenyakit: '', riwayat: [] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('bidan.pemeriksaan.index') }}" class="inline-flex items-center gap-1 text-sm text-amber-600 font-bold hover:text-amber-700 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Kohort Lansia</h1>
                <p class="text-slate-500 mt-1 font-medium">Catat hasil pemeriksaan kesehatan rutin untuk warga lanjut usia.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-medium">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-medium">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
        @endif

        @if(!$sesiAktif)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
            <div class="bg-amber-100 p-2 rounded-xl shrink-0"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
            <p class="text-sm text-amber-800 font-medium">Tidak ada sesi aktif. Pemeriksaan tidak dapat disimpan.</p>
        </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Lansia</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Gol. Darah</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Riwayat Penyakit</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($lansia as $org)
                        @php $isDone = $sudahDiperiksa->contains($org->warga_id); @endphp
                        <tr class="hover:bg-slate-50/50 transition {{ $isDone ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $org->warga->nama }}</p>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $org->warga->nik }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-10 h-10 bg-amber-50 text-amber-600 rounded-xl font-black text-sm border border-amber-100">{{ $org->golongan_darah ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 max-w-xs truncate">{{ $org->riwayat_penyakit ?? 'Tidak ada riwayat' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($isDone)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sudah</span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Belum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="openModal = true; selectedId = {{ $org->warga->id }}; selectedName = '{{ addslashes($org->warga->nama) }}'; selectedGolDar = '{{ $org->golongan_darah ?? '-' }}'; selectedPenyakit = '{{ addslashes($org->riwayat_penyakit ?? 'Tidak ada') }}'; riwayat = {{ json_encode($org->warga->pemeriksaans->map(fn($p) => ['tanggal' => $p->created_at->format('d/m/Y'), 'bb' => $p->berat_badan, 'tensi' => $p->tensi_darah, 'gula' => $p->gula_darah])) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $isDone ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-200' }}"
                                    {{ $isDone || !$sesiAktif ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Input
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data Lansia terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="openModal = false">
                <form action="{{ route('bidan.pemeriksaan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="warga_id" x-model="selectedId">
                    <input type="hidden" name="sesi_posyandu_id" value="{{ $sesiAktif?->id }}">
                    <input type="hidden" name="tipe_kohort" value="lansia">

                    <div class="px-8 py-6 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-t-3xl">
                        <h3 class="text-xl font-black">Input Pemeriksaan Lansia</h3>
                        <p class="text-amber-100 text-sm mt-1">Pasien: <span x-text="selectedName" class="font-bold text-white"></span></p>
                    </div>

                    <!-- Info Medis -->
                    <div class="px-8 py-4 bg-amber-50 border-b border-amber-100 flex gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest">Gol. Darah</p>
                            <p class="text-lg font-black text-amber-700" x-text="selectedGolDar"></p>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest">Riwayat Penyakit</p>
                            <p class="text-sm font-medium text-amber-700 truncate" x-text="selectedPenyakit"></p>
                        </div>
                    </div>

                    <!-- Riwayat Mini -->
                    <div x-show="riwayat.length > 0" class="px-8 py-4 bg-orange-50 border-b border-orange-100">
                        <p class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-2">Riwayat 3 Pemeriksaan Terakhir</p>
                        <div class="flex gap-3">
                            <template x-for="(r, i) in riwayat" :key="i">
                                <div class="flex-1 bg-white p-3 rounded-xl border border-orange-100 text-center">
                                    <p class="text-[10px] font-bold text-slate-400" x-text="r.tanggal"></p>
                                    <p class="text-sm font-black text-slate-700 mt-1" x-text="'Tensi: ' + (r.tensi || '-')"></p>
                                    <p class="text-xs text-slate-500" x-text="'Gula: ' + (r.gula || '-')"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="px-8 py-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="berat_badan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="tinggi_badan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tensi Darah (mmHg)</label>
                                <input type="text" name="tensi_darah" placeholder="120/80" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Gula Darah (mg/dL)</label>
                                <input type="text" name="gula_darah" placeholder="Contoh: 110" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kolesterol (mg/dL)</label>
                                <input type="text" name="kolesterol" placeholder="Contoh: 200" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Asam Urat (mg/dL)</label>
                                <input type="text" name="asam_urat" placeholder="Contoh: 6.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keluhan</label>
                            <textarea name="keluhan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tindakan / Terapi</label>
                            <textarea name="tindakan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
                        </div>
                    </div>

                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="openModal = false" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-100 transition">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-amber-600 text-white font-bold text-sm rounded-xl hover:bg-amber-700 shadow-lg shadow-amber-200 transition">Simpan Pemeriksaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-bidan-layout>
