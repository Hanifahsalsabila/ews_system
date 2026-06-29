<x-kader-layout>
    <div x-data="{ 
    showModal: false, 
    editMode: false,
    cohortType: 'umum',
    formData: { 
        id: '', nik: '', nama: '', alamat_lengkap: '', nomor_telepon: '', region_id: '',
        jenis_kelamin: '', tanggal_lahir: '', nama_ayah: '', nama_ibu: '',
        hpht: '', hpl: '', kehamilan_ke: '',
        golongan_darah: '', riwayat_penyakit: ''
    },
    
    
    
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
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Buku Register Warga</h1>
            <p class="text-slate-500 mt-2 font-medium">Manajemen data demografi warga dan pendaftaran kohort Posyandu.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="openModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-teal-200 transition-all active:scale-95 gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Daftar Warga Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-600 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <!-- Search bar -->
        <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('kader.warga.index') }}" method="GET" class="relative max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK atau Nama Warga..." class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">NIK & Nama</th>
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Wilayah</th>
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wargas as $warga)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800">{{ $warga->nama }}</div>
                                <div class="text-sm text-slate-500 mt-1 font-mono">{{ $warga->nik }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($warga->balita)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-600 border border-teal-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div> Balita
                                    </span>
                                @elseif($warga->ibuHamil)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Ibu Hamil
                                    </span>
                                @elseif($warga->lansia)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div> Lansia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div> WUS/PUS
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-sm font-medium text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                                    Dusun {{ $warga->region->dusun_name ?? '-' }} (RW {{ str_pad($warga->region->rw_name ?? 0, 2, '0', STR_PAD_LEFT) }} / RT {{ str_pad($warga->region->rt_name ?? 0, 2, '0', STR_PAD_LEFT) }})
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$warga->user_id)
                                        <form action="{{ route('kader.warga.generate-account', $warga) }}" method="POST" onsubmit="return confirm('Buat akun login untuk warga ini?')">
                                            @csrf
                                            <button type="submit" title="Buatkan Akun Login" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" title="Akun Warga Aktif" class="p-2 text-teal-500 rounded-lg cursor-default border border-transparent">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </button>
                                    @endif
                                    <button @click="openModal({{ json_encode($warga->load(['balita', 'ibuHamil', 'lansia'])) }})" title="Edit Data" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('kader.warga.destroy', $warga) }}" method="POST" onsubmit="return confirm('Hapus data warga dan seluruh riwayat kohortnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Data" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500 font-medium">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                @if(request('search'))
                                    Tidak ada data warga yang cocok dengan pencarian.
                                @else
                                    Belum ada data warga terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($wargas->hasPages())
        <div class="p-4 sm:p-6 border-t border-slate-100">
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
            <div class="px-8 py-6 bg-gradient-to-r from-teal-600 to-teal-700 flex items-center justify-between shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-white tracking-tight" x-text="editMode ? 'Edit Profil Warga' : 'Pendaftaran Warga Baru'"></h3>
                    <p class="text-teal-100 text-sm mt-1 font-medium" x-text="editMode ? 'Perbarui data demografi dan kohort warga.' : 'Daftarkan warga baru ke dalam sistem E-Posyandu Enterprise.'"></p>
                </div>
                <button @click="showModal = false" class="relative z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 hover:rotate-90 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('kader/warga') }}/' + formData.id : '{{ route('kader.warga.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
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
                                                <svg class="w-4 h-4 text-teal-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
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
                    <p class="text-xs font-bold text-gray-400 hidden md:block flex-1"><span class="text-teal-500">*</span> Pastikan data NIK valid dan sesuai dengan KTP warga.</p>
                    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-xl transition-all">Batal</button>
                        <button type="submit" class="px-8 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-teal-200 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="editMode ? 'Simpan Perubahan' : 'Daftarkan Warga'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    </div>
</x-kader-layout>
