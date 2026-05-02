@extends('layouts.admin')

@section('title', 'Manajemen Warga')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false,
    cohortType: 'umum',
    formData: { 
        id: '', nik: '', nama: '', alamat_lengkap: '', nomor_telepon: '', region_id: '',
        jenis_kelamin: '', tanggal_lahir: '', nama_ayah: '', nama_ibu: '',
        hpht: '', hpl: '', kehamilan_ke: '',
        golongan_darah: '', riwayat_penyakit: ''
    },
    qrModal: false,
    importModal: false,
    qrData: { name: '', nik: '', code: '' },
    openModal(warga = null) {
        if (warga) {
            this.editMode = true;
            this.formData = { ...warga };
            if (warga.balita) this.cohortType = 'balita', Object.assign(this.formData, warga.balita);
            else if (warga.ibu_hamil) this.cohortType = 'ibu_hamil', Object.assign(this.formData, warga.ibu_hamil);
            else if (warga.lansia) this.cohortType = 'lansia', Object.assign(this.formData, warga.lansia);
            else this.cohortType = 'umum';
        } else {
            this.editMode = false;
            this.cohortType = 'umum';
            this.formData = { id: '', nik: '', nama: '', alamat_lengkap: '', nomor_telepon: '', region_id: '', jenis_kelamin: '', tanggal_lahir: '', nama_ayah: '', nama_ibu: '', hpht: '', hpl: '', kehamilan_ke: '', golongan_darah: '', riwayat_penyakit: '' };
        }
        this.showModal = true;
    },
    showQR(warga) {
        this.qrData = { name: warga.nama, nik: warga.nik, code: warga.nik };
        this.qrModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Warga</h2>
            <p class="text-gray-500 text-sm mt-1">Pendaftaran warga baru, manajemen kohort, dan pembuatan QR Profile.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="importModal = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-95 gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>
            <button @click="openModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95 gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Daftar Warga Baru
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3 text-emerald-700 shadow-sm">
        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-700 shadow-sm">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Warga</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Wilayah</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kohort</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Terdaftar</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($wargas as $warga)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold shrink-0">
                                    {{ substr($warga->nama, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $warga->nama }}</p>
                                    <p class="text-xs text-gray-400 font-medium">{{ $warga->nik }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $warga->region->dusun_name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 italic">RW {{ str_pad($warga->region->rw_name ?? 0, 2, '0', STR_PAD_LEFT) }} / RT {{ str_pad($warga->region->rt_name ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($warga->balita)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 italic">Balita</span>
                            @elseif($warga->ibuHamil)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100 italic">Ibu Hamil</span>
                            @elseif($warga->lansia)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 italic">Lansia</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-50 text-gray-400 border border-gray-100 italic">Umum</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $warga->nomor_telepon ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $warga->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $warga->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="showQR({{ json_encode($warga) }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors border border-transparent hover:border-emerald-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1l-3 3m2-3l3 3m-3-3v12m-9-4h10m-8 0l3-3m-3 3l3 3"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M7 8h10M7 16h10"></path>
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect>
                                    <path d="M7 7h2v2H7zM7 15h2v2H7zM15 7h2v2h-2z"></path>
                                    </svg>
                                </button>
                                <button @click="openModal({{ json_encode($warga->load(['balita', 'ibuHamil', 'lansia'])) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.wargas.destroy', $warga) }}" method="POST" onsubmit="return confirm('Hapus data warga dan seluruh riwayat kohortnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Data warga tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($wargas->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $wargas->links() }}
        </div>
        @endif
    </div>

    <!-- Registration Modal -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             @click.away="showModal = false">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-emerald-600 to-teal-700 flex items-center justify-between shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-white tracking-tight" x-text="editMode ? 'Edit Profil Warga' : 'Pendaftaran Warga Baru'"></h3>
                    <p class="text-emerald-100 text-sm mt-1 font-medium" x-text="editMode ? 'Perbarui data demografi dan kohort warga.' : 'Daftarkan warga baru ke dalam sistem E-Posyandu Enterprise.'"></p>
                </div>
                <button @click="showModal = false" class="relative z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 hover:rotate-90 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/wargas') }}/' + formData.id : '{{ route('admin.wargas.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex-1 overflow-y-auto p-8 bg-slate-50/50">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        
                        <!-- Left Column: Base Data -->
                        <div class="lg:col-span-5 space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest">Informasi Dasar</h4>
                                </div>
                                
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">NIK (16 Digit)</label>
                                        <div class="relative">
                                            <input type="text" name="nik" x-model="formData.nik" required maxlength="16" class="w-full bg-slate-50 border-gray-200 rounded-xl pl-11 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono placeholder-gray-300" placeholder="3201xxxxxxxxxxxx">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Lengkap</label>
                                        <input type="text" name="nama" x-model="formData.nama" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder-gray-300" placeholder="Nama sesuai KTP">
                                    </div>
                                    
                                <div x-data="{ 
                                    allRegions: {{ \Illuminate\Support\Js::from($regions) }},
                                    selectedDusun: '',
                                    selectedRw: '',
                                    
                                    get dusunList() {
                                        return this.allRegions.filter(r => r.type === 'dusun');
                                    },
                                    get rwList() {
                                        if(!this.selectedDusun) return [];
                                        return this.allRegions.filter(r => r.type === 'rw' && r.parent_id == this.selectedDusun);
                                    },
                                    get rtList() {
                                        if(!this.selectedRw) return [];
                                        return this.allRegions.filter(r => r.type === 'rt' && r.parent_id == this.selectedRw);
                                    },
                                    init() {
                                        // Initialize dropdowns for editMode
                                        this.$watch('showModal', value => {
                                            if(value && this.editMode && this.formData.region_id) {
                                                let currentRegion = this.allRegions.find(r => r.id == this.formData.region_id);
                                                if (currentRegion && currentRegion.type === 'rt') {
                                                    let rwRegion = this.allRegions.find(r => r.id == currentRegion.parent_id);
                                                    if (rwRegion) {
                                                        this.selectedDusun = rwRegion.parent_id;
                                                        // Wait for alpine reactivity
                                                        setTimeout(() => {
                                                            this.selectedRw = rwRegion.id;
                                                        }, 50);
                                                    }
                                                }
                                            } else if (value && !this.editMode) {
                                                this.selectedDusun = '';
                                                this.selectedRw = '';
                                                this.formData.region_id = '';
                                            }
                                        });
                                    }
                                }" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Dusun</label>
                                            <select x-model="selectedDusun" @change="selectedRw = ''; formData.region_id = ''" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[length:12px_12px] bg-[right_16px_center]">
                                                <option value="" disabled>-- Pilih Dusun --</option>
                                                <template x-for="r in dusunList" :key="r.id">
                                                    <option :value="r.id" x-text="r.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Rukun Warga (RW)</label>
                                            <select x-model="selectedRw" @change="formData.region_id = ''" :disabled="!selectedDusun" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none disabled:opacity-50 disabled:cursor-not-allowed bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[length:12px_12px] bg-[right_16px_center]">
                                                <option value="" disabled>-- Pilih RW --</option>
                                                <template x-for="r in rwList" :key="r.id">
                                                    <option :value="r.id" x-text="r.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Rukun Tetangga (RT) <span class="text-rose-500">*</span></label>
                                        <select name="region_id" x-model="formData.region_id" required :disabled="!selectedRw" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none disabled:opacity-50 disabled:cursor-not-allowed bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[length:12px_12px] bg-[right_16px_center]">
                                            <option value="" disabled>-- Pilih RT --</option>
                                            <template x-for="r in rtList" :key="r.id">
                                                <option :value="r.id" x-text="r.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">No. Telepon / WhatsApp</label>
                                        <div class="relative">
                                            <input type="text" name="nomor_telepon" x-model="formData.nomor_telepon" class="w-full bg-slate-50 border-gray-200 rounded-xl pl-11 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-mono placeholder-gray-300" placeholder="08xxxxxxxxxx">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Alamat Lengkap</label>
                                        <textarea name="alamat_lengkap" x-model="formData.alamat_lengkap" rows="2" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder-gray-300" placeholder="Jl. Contoh No. 123..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Type & Specific Cohort -->
                        <div class="lg:col-span-7 space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest">Klasifikasi & Kohort Khusus</h4>
                                </div>
                                
                                <div x-show="!editMode" class="mb-6">
                                    <label class="block text-xs font-bold text-gray-600 mb-3">Tipe Sasaran Kesehatan</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 group" 
                                               :class="cohortType == 'balita' ? 'bg-blue-50 border-blue-500 shadow-md shadow-blue-100' : 'border-gray-100 hover:border-blue-200 hover:bg-slate-50'">
                                            <input type="radio" name="cohort_type" value="balita" x-model="cohortType" class="sr-only">
                                            <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👶</div>
                                            <span class="text-xs font-bold" :class="cohortType == 'balita' ? 'text-blue-700' : 'text-gray-500'">Balita</span>
                                        </label>
                                        <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 group" 
                                               :class="cohortType == 'ibu_hamil' ? 'bg-rose-50 border-rose-500 shadow-md shadow-rose-100' : 'border-gray-100 hover:border-rose-200 hover:bg-slate-50'">
                                            <input type="radio" name="cohort_type" value="ibu_hamil" x-model="cohortType" class="sr-only">
                                            <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🤰</div>
                                            <span class="text-xs font-bold" :class="cohortType == 'ibu_hamil' ? 'text-rose-700' : 'text-gray-500'">Ibu Hamil</span>
                                        </label>
                                        <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 group" 
                                               :class="cohortType == 'lansia' ? 'bg-amber-50 border-amber-500 shadow-md shadow-amber-100' : 'border-gray-100 hover:border-amber-200 hover:bg-slate-50'">
                                            <input type="radio" name="cohort_type" value="lansia" x-model="cohortType" class="sr-only">
                                            <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🧓</div>
                                            <span class="text-xs font-bold" :class="cohortType == 'lansia' ? 'text-amber-700' : 'text-gray-500'">Lansia</span>
                                        </label>
                                        <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 group" 
                                               :class="cohortType == 'umum' ? 'bg-slate-100 border-slate-500 shadow-md shadow-slate-200' : 'border-gray-100 hover:border-slate-300 hover:bg-slate-50'">
                                            <input type="radio" name="cohort_type" value="umum" x-model="cohortType" class="sr-only">
                                            <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👤</div>
                                            <span class="text-xs font-bold" :class="cohortType == 'umum' ? 'text-slate-700' : 'text-gray-500'">Umum</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Dynamic Cohort Forms -->
                                <div class="relative min-h-[200px]">
                                    <!-- Umum Default State -->
                                    <div x-show="cohortType == 'umum'" x-transition.opacity.duration.300ms class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-slate-300 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <h5 class="text-sm font-bold text-slate-700 mb-1">Pendaftaran Warga Umum</h5>
                                        <p class="text-xs text-slate-500 max-w-sm">Warga umum tidak memiliki form kohort tambahan. Klik daftar untuk menyimpan data demografi dasar.</p>
                                    </div>

                                    <!-- Balita Fields -->
                                    <div x-show="cohortType == 'balita'" x-transition.opacity.duration.300ms class="absolute inset-0 space-y-5 bg-gradient-to-br from-blue-50 to-white p-6 rounded-2xl border border-blue-100 overflow-y-auto">
                                        <h5 class="text-sm font-black text-blue-800 flex items-center gap-2 mb-4">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Detail Data Balita
                                        </h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                                                <div class="flex gap-2">
                                                    <label class="flex-1 text-center py-2.5 px-3 bg-white border border-blue-200 rounded-xl cursor-pointer hover:bg-blue-50 transition-colors" :class="formData.jenis_kelamin == 'L' ? 'ring-2 ring-blue-500 bg-blue-50' : ''">
                                                        <input type="radio" name="jenis_kelamin" value="L" x-model="formData.jenis_kelamin" class="sr-only">
                                                        <span class="text-xs font-bold text-blue-900">Laki-laki</span>
                                                    </label>
                                                    <label class="flex-1 text-center py-2.5 px-3 bg-white border border-blue-200 rounded-xl cursor-pointer hover:bg-blue-50 transition-colors" :class="formData.jenis_kelamin == 'P' ? 'ring-2 ring-rose-400 bg-rose-50 border-rose-200' : ''">
                                                        <input type="radio" name="jenis_kelamin" value="P" x-model="formData.jenis_kelamin" class="sr-only">
                                                        <span class="text-xs font-bold" :class="formData.jenis_kelamin == 'P' ? 'text-rose-700' : 'text-blue-900'">Perempuan</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                                                <input type="date" name="tanggal_lahir" x-model="formData.tanggal_lahir" class="w-full bg-white border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-blue-900">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Nama Ayah</label>
                                                <input type="text" name="nama_ayah" x-model="formData.nama_ayah" placeholder="Contoh: Budi Santoso" class="w-full bg-white border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Nama Ibu</label>
                                                <input type="text" name="nama_ibu" x-model="formData.nama_ibu" placeholder="Contoh: Siti Aminah" class="w-full bg-white border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ibu Hamil Fields -->
                                    <div x-show="cohortType == 'ibu_hamil'" x-transition.opacity.duration.300ms class="absolute inset-0 space-y-5 bg-gradient-to-br from-rose-50 to-white p-6 rounded-2xl border border-rose-100 overflow-y-auto">
                                        <h5 class="text-sm font-black text-rose-800 flex items-center gap-2 mb-4">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Data Kandungan (Ibu Hamil)
                                        </h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1.5">HPHT (Hari Pertama Haid Terakhir)</label>
                                                <input type="date" name="hpht" x-model="formData.hpht" class="w-full bg-white border-rose-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-rose-900">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1.5">HPL (Hari Perkiraan Lahir)</label>
                                                <input type="date" name="hpl" x-model="formData.hpl" class="w-full bg-white border-rose-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-rose-900">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1.5">Kehamilan Ke- (Gravida)</label>
                                            <input type="number" min="1" name="kehamilan_ke" x-model="formData.kehamilan_ke" placeholder="1" class="w-1/2 bg-white border-rose-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                                        </div>
                                    </div>

                                    <!-- Lansia Fields -->
                                    <div x-show="cohortType == 'lansia'" x-transition.opacity.duration.300ms class="absolute inset-0 space-y-5 bg-gradient-to-br from-amber-50 to-white p-6 rounded-2xl border border-amber-100 overflow-y-auto">
                                        <h5 class="text-sm font-black text-amber-800 flex items-center gap-2 mb-4">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Rekam Medis Lansia
                                        </h5>
                                        <div>
                                            <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1.5">Golongan Darah</label>
                                            <select name="golongan_darah" x-model="formData.golongan_darah" class="w-1/2 bg-white border-amber-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-amber-900">
                                                <option value="">-- Pilih --</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="AB">AB</option>
                                                <option value="O">O</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1.5">Riwayat Penyakit Menahun</label>
                                            <textarea name="riwayat_penyakit" x-model="formData.riwayat_penyakit" rows="3" class="w-full bg-white border-amber-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all placeholder-amber-300" placeholder="Contoh: Hipertensi, Asam Urat, Diabetes Mellitus..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
                    <p class="text-xs font-bold text-gray-400 hidden md:block flex-1"><span class="text-emerald-500">*</span> Pastikan data NIK valid dan sesuai dengan KTP warga.</p>
                    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-xl transition-all">Batal</button>
                        <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="editMode ? 'Simpan Perubahan' : 'Daftarkan Warga'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Modal -->
    <div x-show="qrModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center" @click.away="qrModal = false">
            <div class="p-8">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1l-3 3m2-3l3 3m-3-3v12m-9-4h10m-8 0l3-3m-3 3l3 3"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M7 8h10M7 16h10"></path><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect><path d="M7 7h2v2H7zM7 15h2v2H7zM15 7h2v2h-2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-1" x-text="qrData.name"></h3>
                <p class="text-slate-500 text-sm font-medium mb-6" x-text="qrData.nik"></p>
                
                <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm inline-block qr-code-svg mb-6">
                    <!-- Dynamic QR via Alpine using a generic endpoint or generating via a route -->
                    <img :src="'/admin/wargas/qr/' + qrData.nik" alt="QR Code" class="w-48 h-48 mx-auto">
                </div>
                
                <button type="button" @click="qrModal = false" class="w-full px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition-all">Tutup</button>
            </div>
        </div>
    </div>
    <!-- Import Modal -->
    <div x-show="importModal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden" @click.away="importModal = false">
            <div class="px-8 py-6 bg-slate-900 text-white">
                <h3 class="text-xl font-bold">Import Data Masal</h3>
                <p class="text-slate-400 text-xs mt-1">Gunakan format Excel (.xlsx atau .csv) untuk mengimpor data warga sekaligus.</p>
            </div>
            <form action="{{ route('admin.wargas.import') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-emerald-400 transition-colors group cursor-pointer relative">
                    <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer">
                    <svg class="w-12 h-12 text-slate-300 mx-auto group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <p class="mt-4 text-sm font-bold text-slate-600">Klik atau seret file Excel di sini</p>
                    <p class="text-xs text-slate-400 mt-1">Maksimal file 2MB</p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 italic">
                    <p class="text-[10px] font-bold text-blue-800 uppercase tracking-widest mb-2">Petunjuk Format Kolom:</p>
                    <p class="text-xs text-blue-700 leading-relaxed mb-3">
                        Pastikan header baris pertama di Excel adalah: <br>
                        <span class="font-mono bg-white px-1 rounded shadow-sm text-blue-900">nik, nama_warga, alamat_lengkap, nomor_telepon, nama_wilayah</span>
                    </p>
                    <a href="{{ asset('Template_Data_Warga.xlsx') }}" download class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Template Excel
                    </a>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="importModal = false" class="px-6 py-2.5 text-sm font-bold text-slate-400 hover:text-slate-600">Batal</button>
                    <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .qr-code-svg svg { width: 100%; height: auto; }
</style>
@endsection
