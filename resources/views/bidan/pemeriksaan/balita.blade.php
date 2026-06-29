<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pemeriksaan Anak / Balita</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, selectedId: null, selectedName: '', selectedUmur: 0, riwayat: [] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('bidan.pemeriksaan.index') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 font-bold hover:text-blue-700 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Kohort Anak / Balita</h1>
                <p class="text-slate-500 mt-1 font-medium">Catat hasil pengukuran antropometri dan pemberian imunisasi.</p>
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
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Anak</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Orang Tua</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Umur</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">JK</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($balita as $anak)
                        @php
                            $umurBulan = intval(\Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diffInMonths(now()));
                            $isDone = $sudahDiperiksa->contains($anak->warga_id);
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition {{ $isDone ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $anak->warga->nama }}</p>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $anak->warga->nik }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600">{{ $anak->nama_ibu ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $anak->nama_ayah ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-50 text-blue-600 rounded-lg font-black text-sm border border-blue-100">{{ $umurBulan }} Bln</span>
                                <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm {{ $anak->warga->jenis_kelamin == 'L' ? 'bg-sky-50 text-sky-600 border border-sky-100' : 'bg-pink-50 text-pink-600 border border-pink-100' }}">{{ $anak->warga->jenis_kelamin }}</span>
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
                                    @click="openModal = true; selectedId = {{ $anak->warga->id }}; selectedName = '{{ addslashes($anak->warga->nama) }}'; selectedUmur = {{ $umurBulan }}; riwayat = {{ json_encode($anak->warga->pemeriksaans->map(fn($p) => ['tanggal' => $p->created_at->format('d/m/Y'), 'bb' => $p->berat_badan, 'tb' => $p->tinggi_badan, 'lk' => $p->lingkar_kepala])) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all {{ ($isDone || !$sesiAktif) ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-200' }}"
                                    {{ $isDone || !$sesiAktif ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Input
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data Balita terdaftar.</td></tr>
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
                    <input type="hidden" name="tipe_kohort" value="balita">

                    <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-sky-600 text-white rounded-t-3xl">
                        <h3 class="text-xl font-black">Input Pemeriksaan Balita</h3>
                        <p class="text-blue-100 text-sm mt-1">Pasien: <span x-text="selectedName" class="font-bold text-white"></span> (<span x-text="selectedUmur"></span> Bulan)</p>
                    </div>

                    <!-- Riwayat Mini -->
                    <div x-show="riwayat.length > 0" class="px-8 py-4 bg-blue-50 border-b border-blue-100">
                        <p class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2">Riwayat 3 Pemeriksaan Terakhir</p>
                        <div class="flex gap-3">
                            <template x-for="(r, i) in riwayat" :key="i">
                                <div class="flex-1 bg-white p-3 rounded-xl border border-blue-100 text-center">
                                    <p class="text-[10px] font-bold text-slate-400" x-text="r.tanggal"></p>
                                    <p class="text-sm font-black text-slate-700 mt-1" x-text="(r.bb || '-') + ' kg'"></p>
                                    <p class="text-xs text-slate-500" x-text="'TB: ' + (r.tb || '-') + ' cm'"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="px-8 py-6 space-y-6">
                        <!-- Antropometri -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="tinggi_badan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="berat_badan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Lingkar Kepala (cm)</label>
                                <input type="number" step="0.1" name="lingkar_kepala" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Pemberian -->
                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Pemberian Suplemen & PMT</p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-blue-200 transition">
                                    <input type="checkbox" name="vitamin_a" value="1" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-bold text-slate-700">💊 Vitamin A</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-blue-200 transition">
                                    <input type="checkbox" name="obat_cacing" value="1" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-bold text-slate-700">💊 Obat Cacing</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-blue-200 transition" x-show="selectedUmur <= 6">
                                    <input type="checkbox" name="asi_eksklusif" value="1" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-bold text-slate-700">🍼 ASI Eksklusif</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-blue-200 transition">
                                    <input type="checkbox" name="pmt" value="1" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-bold text-slate-700">🥣 PMT (Makanan Tambahan)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Imunisasi -->
                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Imunisasi (Sesuai Umur)</p>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur <= 1">
                                    <input type="checkbox" name="imunisasi[]" value="HB0" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">HB 0 (0-7 Hari)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur >= 1 && selectedUmur <= 3">
                                    <input type="checkbox" name="imunisasi[]" value="BCG, Polio 1" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">BCG, Polio 1 (1 Bulan)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur >= 2 && selectedUmur <= 4">
                                    <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 1, Polio 2" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">DPT-HB-Hib 1, Polio 2 (2 Bln)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur >= 3 && selectedUmur <= 5">
                                    <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 2, Polio 3" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">DPT-HB-Hib 2, Polio 3 (3 Bln)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur >= 4 && selectedUmur <= 6">
                                    <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 3, Polio 4, IPV" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">DPT-HB-Hib 3, Polio 4, IPV (4 Bln)</span>
                                </label>
                                <label class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="selectedUmur >= 9 && selectedUmur <= 12">
                                    <input type="checkbox" name="imunisasi[]" value="Campak (MR)" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="font-medium text-slate-700">Campak/MR (9 Bulan)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keluhan / Catatan</label>
                            <textarea name="keluhan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="openModal = false" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-100 transition">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Simpan Pemeriksaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-bidan-layout>
