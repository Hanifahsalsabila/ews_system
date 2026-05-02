<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pemeriksaan Kohort Ibu Hamil</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, selectedId: null, selectedName: '', riwayat: [] }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('bidan.pemeriksaan.index') }}" class="inline-flex items-center gap-1 text-sm text-rose-600 font-bold hover:text-rose-700 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Kohort Ibu Hamil</h1>
                <p class="text-slate-500 mt-1 font-medium">Catat hasil pemeriksaan antenatal care (ANC) ibu hamil.</p>
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

        <!-- Table -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Ibu Hamil</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kehamilan Ke-</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">HPHT / HPL</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($ibuHamil as $ibu)
                        @php $isDone = $sudahDiperiksa->contains($ibu->warga_id); @endphp
                        <tr class="hover:bg-slate-50/50 transition {{ $isDone ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $ibu->warga->nama }}</p>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $ibu->warga->nik }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-rose-50 text-rose-600 rounded-lg font-black text-sm border border-rose-100">{{ $ibu->kehamilan_ke }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700 font-medium">{{ $ibu->hpht ? \Carbon\Carbon::parse($ibu->hpht)->format('d M Y') : '-' }}</p>
                                <p class="text-xs text-rose-500 font-bold mt-0.5">HPL: {{ $ibu->hpl ? \Carbon\Carbon::parse($ibu->hpl)->format('d M Y') : '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($isDone)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sudah Diperiksa
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Belum Diperiksa
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="openModal = true; selectedId = {{ $ibu->warga->id }}; selectedName = '{{ addslashes($ibu->warga->nama) }}'; riwayat = {{ json_encode($ibu->warga->pemeriksaans->map(fn($p) => ['tanggal' => $p->created_at->format('d/m/Y'), 'bb' => $p->berat_badan, 'tensi' => $p->tensi_darah, 'tfu' => $p->tfu])) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $isDone ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-rose-600 text-white hover:bg-rose-700 shadow-lg shadow-rose-200' }}"
                                    {{ $isDone || !$sesiAktif ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Input
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data Ibu Hamil terdaftar.</td></tr>
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
                    <input type="hidden" name="tipe_kohort" value="ibu_hamil">

                    <!-- Modal Header -->
                    <div class="px-8 py-6 bg-gradient-to-r from-rose-600 to-pink-600 text-white rounded-t-3xl">
                        <h3 class="text-xl font-black">Input Pemeriksaan ANC</h3>
                        <p class="text-rose-100 text-sm mt-1">Pasien: <span x-text="selectedName" class="font-bold text-white"></span></p>
                    </div>

                    <!-- Riwayat Mini -->
                    <div x-show="riwayat.length > 0" class="px-8 py-4 bg-rose-50 border-b border-rose-100">
                        <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-2">Riwayat 3 Pemeriksaan Terakhir</p>
                        <div class="flex gap-3">
                            <template x-for="(r, i) in riwayat" :key="i">
                                <div class="flex-1 bg-white p-3 rounded-xl border border-rose-100 text-center">
                                    <p class="text-[10px] font-bold text-slate-400" x-text="r.tanggal"></p>
                                    <p class="text-sm font-black text-slate-700 mt-1" x-text="(r.bb || '-') + ' kg'"></p>
                                    <p class="text-xs text-slate-500" x-text="'Tensi: ' + (r.tensi || '-')"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <div class="px-8 py-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="berat_badan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="tinggi_badan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tensi Darah (mmHg)</label>
                                <input type="text" name="tensi_darah" placeholder="120/80" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">LILA (cm)</label>
                                <input type="number" step="0.1" name="lingkar_lengan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">TFU (cm)</label>
                                <input type="number" step="0.1" name="tfu" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">DJJ (bpm)</label>
                                <input type="number" name="djj" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        </div>

                        <!-- Suplementasi -->
                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Suplementasi & Imunisasi</p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-rose-200 transition">
                                    <input type="checkbox" name="suplementasi_fe" value="1" class="w-5 h-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span class="text-sm font-bold text-slate-700">Tablet Fe (Tambah Darah)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-rose-200 transition">
                                    <select name="status_tt" class="w-full bg-transparent border-0 text-sm font-bold text-slate-700 focus:ring-0 p-0">
                                        <option value="">Status TT: Belum</option>
                                        <option value="TT1">TT1</option>
                                        <option value="TT2">TT2</option>
                                        <option value="TT3">TT3</option>
                                        <option value="TT4">TT4</option>
                                        <option value="TT5">TT5</option>
                                    </select>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keluhan</label>
                            <textarea name="keluhan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tindakan / Terapi</label>
                            <textarea name="tindakan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="openModal = false" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-100 transition">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 text-white font-bold text-sm rounded-xl hover:bg-rose-700 shadow-lg shadow-rose-200 transition">Simpan Pemeriksaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-bidan-layout>
