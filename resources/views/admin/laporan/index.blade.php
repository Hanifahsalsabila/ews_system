@extends('layouts.admin')

@section('title', 'Pelaporan SIP Enterprise')

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Pusat Pelaporan SIP</h2>
            <p class="text-gray-500 text-sm mt-1 uppercase tracking-widest font-bold">Sistem Informasi Posyandu Terintegrasi (Format 1-6)</p>
        </div>
        <div class="flex gap-2">
            <button class="px-6 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-2xl shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export All (.xlsx)
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-emerald-600 rounded-3xl p-8 text-white shadow-xl shadow-emerald-100 flex items-center justify-between overflow-hidden relative group">
            <div class="relative z-10">
                <p class="text-xs font-black text-emerald-200 uppercase tracking-widest mb-2">Total Cakupan</p>
                <h3 class="text-4xl font-black tracking-tighter">{{ $stats['total_warga'] }}</h3>
                <p class="text-[10px] font-bold text-emerald-100 mt-2">Warga Terdaftar Tahun Ini</p>
            </div>
            <svg class="w-32 h-32 absolute -right-8 -bottom-8 text-white/10 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M20 8v6M23 11h-6"></path></svg>
        </div>
        <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl shadow-slate-200 flex items-center justify-between overflow-hidden relative group">
            <div class="relative z-10">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Pelayanan</p>
                <h3 class="text-4xl font-black tracking-tighter">{{ $stats['total_pemeriksaan'] }}</h3>
                <p class="text-[10px] font-bold text-slate-500 mt-2">Rekam Medik Tersimpan</p>
            </div>
            <svg class="w-32 h-32 absolute -right-8 -bottom-8 text-white/10 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <div class="bg-white rounded-3xl p-8 text-slate-800 shadow-sm border border-gray-100 flex items-center justify-between overflow-hidden relative group">
            <div class="relative z-10">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Sesi Operasional</p>
                <h3 class="text-4xl font-black tracking-tighter text-emerald-600">{{ $stats['total_sesi'] }}</h3>
                <p class="text-[10px] font-bold text-gray-500 mt-2">Sesi Terlaksana</p>
            </div>
            <svg class="w-32 h-32 absolute -right-8 -bottom-8 text-gray-100 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
    </div>

    <!-- SIP Grid -->
    <div x-data="{
        showModal: false,
        exportData: { format_id: 1, title: '', bulan: '{{ date('m') }}', tahun: '{{ date('Y') }}', kepala_desa: '', ketua_kader: '' },
        openExportModal(formatId, title) {
            this.exportData.format_id = formatId;
            this.exportData.title = title;
            this.showModal = true;
        }
    }">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
            @php
                $formats = [
                    ['id' => 1, 'title' => 'SIP 1: Ibu Hamil & Kematian', 'desc' => 'Catatan ibu hamil, kelahiran, dan kematian ibu/bayi.'],
                    ['id' => 2, 'title' => 'SIP 2: Registrasi Bayi/Balita', 'desc' => 'Daftar bayi/balita dan hasil penimbangan KMS.'],
                    ['id' => 3, 'title' => 'SIP 3: Register WUS & PUS', 'desc' => 'Data wanita usia subur dan pasangan usia subur.'],
                    ['id' => 4, 'title' => 'SIP 4: Register Ibu Hamil', 'desc' => 'Data detail kehamilan dan kontrol rutin bumil.'],
                    ['id' => 5, 'title' => 'SIP 5: Pelayanan Posyandu', 'desc' => 'Rekapitulasi pelayanan bulanan dan kehadiran.'],
                    ['id' => 6, 'title' => 'SIP 6: Data Operasional', 'desc' => 'Data petugas, pengunjung, dan operasional posyandu.'],
                ];
            @endphp

            @foreach($formats as $format)
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-300 group flex flex-col">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-900 font-black flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all text-sm">
                    {{ $format['id'] }}
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-2 leading-tight uppercase tracking-tight">{{ $format['title'] }}</h3>
                <p class="text-sm text-gray-500 mb-8 flex-1">{{ $format['desc'] }}</p>
                
                <div class="flex items-center gap-2">
                    <button type="button" @click="openExportModal({{ $format['id'] }}, '{{ $format['title'] }}')" class="flex-1 py-3 bg-slate-900 text-white text-center rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg shadow-slate-200">
                        Pilih Parameter & Unduh
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Export Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition.opacity.duration.300ms>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all" @click.away="showModal = false" x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                <div class="px-8 py-6 bg-emerald-600 flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-black text-white leading-none">Parameter Ekspor SIP</h3>
                        <p class="text-xs font-bold text-emerald-200 mt-1" x-text="exportData.title"></p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-white hover:text-emerald-200 transition-colors bg-white/10 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('admin.pelaporan.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="format_id" x-model="exportData.format_id">
                    
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Bulan</label>
                                <select name="bulan" x-model="exportData.bulan" required class="w-full bg-slate-50 border-gray-200 text-gray-700 rounded-xl px-4 py-3 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Tahun</label>
                                <select name="tahun" x-model="exportData.tahun" required class="w-full bg-slate-50 border-gray-200 text-gray-700 rounded-xl px-4 py-3 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Otorisasi / Pengesahan</p>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama Kepala Desa (Atau TTD Kiri)</label>
                                <input type="text" name="kepala_desa" x-model="exportData.kepala_desa" placeholder="Contoh: Budi Santoso" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama Ketua Kader (Atau TTD Kanan)</label>
                                <input type="text" name="ketua_kader" x-model="exportData.ketua_kader" placeholder="Contoh: Siti Aminah" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-5 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" @click="setTimeout(() => showModal = false, 500)" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Buat File Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
