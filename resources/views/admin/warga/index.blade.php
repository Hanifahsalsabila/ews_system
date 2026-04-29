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
                            <p class="text-sm text-gray-600">{{ $warga->region->dusun ?? '-' }}</p>
                            <p class="text-xs text-gray-400 italic">RW {{ $warga->region->rw ?? '00' }} / RT {{ $warga->region->rt ?? '00' }}</p>
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Data warga tidak ditemukan.</td>
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
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-cloak>
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all"
             @click.away="showModal = false">
            
            <div class="px-6 py-4 bg-emerald-600 flex items-center justify-between shadow-lg">
                <h3 class="text-lg font-bold text-white" x-text="editMode ? 'Edit Profil Warga' : 'Pendaftaran Warga Enterprise'"></h3>
                <button @click="showModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/wargas') }}/' + formData.id : '{{ route('admin.wargas.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[70vh] overflow-y-auto">
                    <!-- Base Data -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-widest border-b border-emerald-100 pb-2">Informasi Dasar</h4>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" x-model="formData.nik" required maxlength="16" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" x-model="formData.nama" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Pilih Wilayah (Region)</label>
                            <select name="region_id" x-model="formData.region_id" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="">-- Pilih Dusun/RT/RW --</option>
                                @foreach($regions as $r)
                                <option value="{{ $r->id }}">{{ $r->dusun }} (RW {{ $r->rw }} / RT {{ $r->rt }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">WhatsApp / No. Telepon</label>
                            <input type="text" name="nomor_telepon" x-model="formData.nomor_telepon" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="08xxxxxxxx">
                        </div>
                    </div>

                    <!-- Type & Specific Cohort -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-2">Klasifikasi Kohort</h4>
                        <div x-show="!editMode">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Tipe Sasaran</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" :class="cohortType == 'balita' ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-200' : ''">
                                    <input type="radio" name="cohort_type" value="balita" x-model="cohortType" class="text-emerald-600 focus:ring-0">
                                    <span class="text-xs font-bold text-gray-600">Balita</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" :class="cohortType == 'ibu_hamil' ? 'bg-rose-50 border-rose-200 ring-1 ring-rose-200' : ''">
                                    <input type="radio" name="cohort_type" value="ibu_hamil" x-model="cohortType" class="text-emerald-600 focus:ring-0">
                                    <span class="text-xs font-bold text-gray-600">Ibu Hamil</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" :class="cohortType == 'lansia' ? 'bg-amber-50 border-amber-200 ring-1 ring-amber-200' : ''">
                                    <input type="radio" name="cohort_type" value="lansia" x-model="cohortType" class="text-emerald-600 focus:ring-0">
                                    <span class="text-xs font-bold text-gray-600">Lansia</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" :class="cohortType == 'umum' ? 'bg-slate-50 border-slate-200 ring-1 ring-slate-200' : ''">
                                    <input type="radio" name="cohort_type" value="umum" x-model="cohortType" class="text-emerald-600 focus:ring-0">
                                    <span class="text-xs font-bold text-gray-600">Umum</span>
                                </label>
                            </div>
                        </div>

                        <!-- Balita Fields -->
                        <div x-show="cohortType == 'balita'" x-transition class="space-y-3 bg-blue-50/50 p-4 rounded-xl border border-blue-100 mt-2">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-800 uppercase">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" x-model="formData.jenis_kelamin" class="w-full bg-white border-blue-200 rounded-lg text-xs p-2">
                                        <option value="">-</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-800 uppercase">Tgl Lahir</label>
                                    <input type="date" name="tanggal_lahir" x-model="formData.tanggal_lahir" class="w-full bg-white border-blue-200 rounded-lg text-xs p-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-800 uppercase">Nama Ayah / Ibu</label>
                                <div class="flex gap-2">
                                    <input type="text" name="nama_ayah" x-model="formData.nama_ayah" placeholder="Ayah" class="w-1/2 bg-white border-blue-200 rounded-lg text-xs p-2">
                                    <input type="text" name="nama_ibu" x-model="formData.nama_ibu" placeholder="Ibu" class="w-1/2 bg-white border-blue-200 rounded-lg text-xs p-2">
                                </div>
                            </div>
                        </div>

                        <!-- Ibu Hamil Fields -->
                        <div x-show="cohortType == 'ibu_hamil'" x-transition class="space-y-3 bg-rose-50/50 p-4 rounded-xl border border-rose-100 mt-2">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-rose-800 uppercase">HPHT (Tgl Terakhir Haid)</label>
                                    <input type="date" name="hpht" x-model="formData.hpht" class="w-full bg-white border-rose-200 rounded-lg text-xs p-2">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-rose-800 uppercase">HPL (Perkiraan Lahir)</label>
                                    <input type="date" name="hpl" x-model="formData.hpl" class="w-full bg-white border-rose-200 rounded-lg text-xs p-2">
                                </div>
                            </div>
                        </div>

                        <!-- Lansia Fields -->
                        <div x-show="cohortType == 'lansia'" x-transition class="space-y-3 bg-amber-50/50 p-4 rounded-xl border border-amber-100 mt-2">
                            <div>
                                <label class="block text-[10px] font-bold text-amber-800 uppercase">Golongan Darah</label>
                                <input type="text" name="golongan_darah" x-model="formData.golongan_darah" placeholder="O+" class="w-full bg-white border-amber-200 rounded-lg text-xs p-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-amber-800 uppercase">Riwayat Penyakit</label>
                                <textarea name="riwayat_penyakit" x-model="formData.riwayat_penyakit" rows="2" class="w-full bg-white border-amber-200 rounded-lg text-xs p-2" placeholder="Hipertensi, Diabetes, dll"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batalkan</button>
                    <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95" x-text="editMode ? 'Perbarui Data' : 'Daftarkan Sekarang'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Modal ... (unchanged) -->

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
