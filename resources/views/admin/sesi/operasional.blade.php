@extends('layouts.admin')

@section('title', 'Operasional 5 Meja - ' . $sesi->nama_sesi)

@section('content')
<div class="mx-auto max-w-7xl h-[calc(100vh-120px)] flex flex-col" x-data="operasionalData()" x-init="init()">
    <!-- Top Nav / Info -->
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.sesi-posyandus.index') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight">{{ $sesi->nama_sesi }}</h2>
                <p class="text-xs text-gray-400 font-bold tracking-widest uppercase">{{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d F Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-full border border-emerald-100">SESI AKTIF</span>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-hidden">
        
        <!-- Left Column: Search & Scan (Meja 1) -->
        <div class="lg:col-span-4 flex flex-col gap-6 overflow-y-auto pr-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px]">1</span>
                    Meja 1: Pendaftaran
                </h3>

                <!-- QR Scan Trigger -->
                <button @click="scanning = !scanning" :class="scanning ? 'bg-rose-50 border-rose-100 text-rose-600' : 'bg-emerald-50 border-emerald-100 text-emerald-600'" class="w-full py-4 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 mb-6">
                    <svg x-show="!scanning" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m-3 3l3 3m2-3l3 3m-3-3v12m-9-4h10m-8 0l3-3m-3 3l3 3"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M7 8h10M7 16h10"></path><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect><path d="M7 7h2v2H7zM7 15h2v2H7zM15 7h2v2h-2z"></path></svg>
                    <svg x-show="scanning" class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span class="text-xs font-black uppercase tracking-widest" x-text="scanning ? 'Hentikan Scanner' : 'Pindai QR Profile'"></span>
                </button>

                <!-- HTML5 QR Code Scanner Container -->
                <div x-show="scanning" id="reader" class="rounded-2xl overflow-hidden border border-gray-200 mb-6 bg-black aspect-square"></div>

                <!-- Manual Search -->
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Cari NIK atau Nama..." class="w-full bg-slate-50 border-gray-100 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-emerald-500/20 focus:border-emerald-500 font-bold transition-all" @input.debounce.300ms="fetchWarga()">
                    <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Search Results (Placeholder / Dynamic) -->
                <div class="mt-4 space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                    <template x-for="warga in searchResults" :key="warga.id">
                        <button @click="selectWarga(warga)" class="w-full p-4 bg-slate-50 hover:bg-emerald-50 rounded-2xl flex items-center gap-3 transition-colors text-left border border-transparent hover:border-emerald-100 group">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-emerald-600 font-black shadow-sm group-hover:bg-emerald-600 group-hover:text-white transition-all" x-text="warga.nama.charAt(0)"></div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-gray-800 truncate" x-text="warga.nama"></p>
                                <p class="text-[10px] text-gray-400 font-mono" x-text="warga.nik"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Statistik Kehadiran</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-600 uppercase">Hadir</p>
                        <p class="text-2xl font-black text-emerald-700">{{ $sesi->absensis->count() }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                        <p class="text-xs font-bold text-blue-600 uppercase">Diperiksa</p>
                        <p class="text-2xl font-black text-blue-700">{{ $sesi->pemeriksaans->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Measurement & Record (Meja 2-5) -->
        <div class="lg:col-span-8 flex flex-col gap-6 overflow-hidden">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex-1 flex flex-col overflow-hidden relative">
                
                <!-- Steps Toolbar -->
                <div class="flex items-center px-6 py-4 border-b border-gray-50 bg-gray-50/30">
                    <div class="flex items-center gap-2">
                        <template x-for="i in [1, 2, 3, 4, 5]">
                            <div class="flex items-center">
                                <div :class="step >= i ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-400'" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all" x-text="i"></div>
                                <div x-show="i < 5" :class="step > i ? 'bg-emerald-600' : 'bg-gray-200'" class="w-4 h-0.5 transition-all"></div>
                            </div>
                        </template>
                    </div>
                    <div class="ml-auto">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="'Meja ' + step"></span>
                    </div>
                </div>

                <!-- Active Area -->
                <div class="flex-1 overflow-y-auto p-8 flex flex-col">
                    <template x-if="!selectedWarga">
                        <div class="flex-1 flex flex-col items-center justify-center text-center opacity-40">
                            <svg class="w-24 h-24 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <h4 class="text-xl font-bold text-gray-400 uppercase tracking-tight">Siap Melayani</h4>
                            <p class="max-w-xs mx-auto text-sm">Pindai QR atau cari NIK warga untuk memulai proses pemeriksaan.</p>
                        </div>
                    </template>

                    <template x-if="selectedWarga">
                        <div class="space-y-8 animate-fade-in">
                            <!-- Warga Header Info -->
                            <div class="flex items-center justify-between p-6 bg-slate-900 rounded-3xl text-white shadow-xl shadow-slate-200">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-500 flex items-center justify-center font-black text-2xl" x-text="selectedWarga.nama.charAt(0)"></div>
                                    <div>
                                        <h3 class="text-xl font-black uppercase tracking-tight" x-text="selectedWarga.nama"></h3>
                                        <p class="text-sm text-emerald-400 font-mono" x-text="selectedWarga.nik"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tipe Sasaran</p>
                                    <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold border border-white/20" x-text="getWargaTypeLabel(selectedWarga)"></span>
                                </div>
                            </div>

                            <!-- Meja 2 & 3: Antropometri Form -->
                            <div x-show="step >= 2 && step <= 3" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest border-b border-emerald-50 pb-2">Meja 2: Penimbangan</h4>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Berat Badan (Kg)</label>
                                        <div class="relative">
                                            <input type="number" step="0.01" x-model="measurements.berat_badan" class="w-full text-4xl font-black bg-slate-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-center tracking-tighter" placeholder="00.00">
                                            <span class="absolute right-4 bottom-4 text-xs font-bold text-gray-300">KG</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest border-b border-blue-50 pb-2">Meja 3: Pengukuran</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tinggi Badan (Cm)</label>
                                            <input type="number" step="0.1" x-model="measurements.tinggi_badan" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-lg font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Lingkar Kepala</label>
                                            <input type="number" step="0.1" x-model="measurements.lingkar_kepala" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-lg font-bold">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">LILA (Lingkar Lengan Atas)</label>
                                            <input type="number" step="0.1" x-model="measurements.lingkar_lengan" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-lg font-bold">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Meja 4: Analitik WHO Z-Score -->
                            <div x-show="step >= 4" class="p-8 bg-slate-50 rounded-3xl border border-slate-200 border-dashed">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">Meja 4: Smart Analytics (WHO Child Growth)</h4>
                                    <span class="text-[10px] font-bold text-slate-500">Live Prediction</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">BB/U (Berat/Umur)</p>
                                        <div class="text-xl font-black text-emerald-600">Sangat Baik</div>
                                        <p class="text-[10px] font-mono text-gray-400 mt-1">Z-Score: +0.2</p>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">TB/U (Tinggi/Umur)</p>
                                        <div class="text-xl font-black text-emerald-600">Normal</div>
                                        <p class="text-[10px] font-mono text-gray-400 mt-1">Z-Score: -0.5</p>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">BB/TB (Gizi)</p>
                                        <div class="text-xl font-black text-rose-500 italic">Gizi Kurang</div>
                                        <p class="text-[10px] font-mono text-gray-400 mt-1">Z-Score: -2.1</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions Footer -->
                            <div class="flex items-center justify-between pt-8 border-t border-gray-100 mt-auto">
                                <button @click="reset()" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600">Batalkan Sesi</button>
                                <div class="flex gap-2">
                                    <button x-show="step > 2" @click="step--" class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl active:scale-95 transition-all">Kembali</button>
                                    <button x-show="step < 5" @click="step++" class="px-8 py-2.5 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 active:scale-95 transition-all">Lanjutkan</button>
                                    <button x-show="step == 5" @click="submitPemeriksaan()" class="px-8 py-2.5 bg-slate-900 text-white font-black rounded-xl shadow-xl shadow-slate-200 active:scale-95 transition-all">Selesai & Validasi</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function operasionalData() {
        return {
            step: 1,
            search: '',
            scanning: false,
            selectedWarga: null,
            searchResults: [],
            html5QrCode: null,
            measurements: { berat_badan: '', tinggi_badan: '', lingkar_kepala: '', lingkar_lengan: '', tensi_darah: '', keluhan: '', tindakan: '', catatan: '' },
            
            init() {
                this.$watch('scanning', value => {
                    if (value) this.startScanner();
                    else this.stopScanner();
                });
            },

            async startScanner() {
                this.html5QrCode = new Html5Qrcode('reader');
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                try {
                    await this.html5QrCode.start({ facingMode: 'environment' }, config, (decodedText) => {
                        this.search = decodedText;
                        this.fetchWarga(true); // Auto-select if found via scan
                        this.scanning = false;
                    });
                } catch (err) {
                    console.error('Scanner failed:', err);
                    this.scanning = false;
                }
            },

            async stopScanner() {
                if (this.html5QrCode) {
                    await this.html5QrCode.stop();
                    this.html5QrCode = null;
                }
            },

            async fetchWarga(autoSelect = false) {
                if (this.search.length < 3) {
                    this.searchResults = [];
                    return;
                }
                const response = await fetch(`{{ url('admin/wargas') }}?search=${this.search}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                this.searchResults = data;
                
                if (autoSelect && data.length > 0) {
                    this.selectWarga(data[0]);
                }
            },

            selectWarga(warga) {
                this.selectedWarga = warga;
                this.step = 2;
                this.searchResults = [];
                this.search = '';
            },

            getWargaTypeLabel(warga) {
                if (warga.balita) return 'Balita';
                if (warga.ibu_hamil) return 'Ibu Hamil';
                if (warga.lansia) return 'Lansia';
                return 'Umum';
            },

            async submitPemeriksaan() {
                const response = await fetch(`{{ route('admin.sesi-posyandus.store-operasional', $sesi) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        warga_id: this.selectedWarga.id,
                        ...this.measurements
                    })
                });
                
                const result = await response.json();
                if (response.ok) {
                    alert('Data Telah Divalidasi dan Disimpan!');
                    this.reset();
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            },

            reset() {
                this.selectedWarga = null;
                this.step = 1;
                this.measurements = { berat_badan: '', tinggi_badan: '', lingkar_kepala: '', lingkar_lengan: '', tensi_darah: '', keluhan: '', tindakan: '', catatan: '' };
                this.stopScanner();
                this.scanning = false;
            }
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
