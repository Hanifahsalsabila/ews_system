@extends('layouts.admin')

@section('title', 'Manajemen Sesi Posyandu')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false, 
    formData: { id: '', nama_sesi: '', tanggal: '{{ date('Y-m-d') }}' },
    openModal(sesi = null) {
        if (sesi) {
            this.editMode = true;
            this.formData = { ...sesi };
        } else {
            this.editMode = false;
            this.formData = { id: '', nama_sesi: '', tanggal: '{{ date('Y-m-d') }}' };
        }
        this.showModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Operasional 5 Meja</h2>
            <p class="text-gray-500 text-sm mt-1">Buka sesi posyandu untuk mulai melakukan pendaftaran dan pemeriksaan warga.</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-emerald-200 transition-all active:scale-95 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buka Sesi Posyandu
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3 text-emerald-700 shadow-sm">
        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Sessions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sessions as $sesi)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
            <!-- Status Badge -->
            <div class="absolute top-0 right-0">
                @if($sesi->status === 'aktif')
                    <div class="bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-bl-2xl shadow-sm animate-pulse">Running</div>
                @else
                    <div class="bg-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-bl-2xl">Completed</div>
                @endif
            </div>

            <div class="mb-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d F Y') }}</p>
                <h3 class="text-xl font-black text-gray-800 mt-1">{{ $sesi->nama_sesi }}</h3>
            </div>

            <div class="flex items-center gap-4 mb-6 text-sm text-gray-500">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-bold text-gray-700">{{ $sesi->absensis_count ?? 0 }}</span> Hadir
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="font-bold text-gray-700">{{ $sesi->pemeriksaans_count ?? 0 }}</span> Diperiksa
                </div>
            </div>

            <div class="mt-auto flex items-center justify-between gap-2 pt-4 border-t border-gray-50">
                <div class="flex gap-2">
                    <button @click="openModal({{ json_encode($sesi) }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <form action="{{ route('admin.sesi-posyandus.destroy', $sesi) }}" method="POST" onsubmit="return confirm('Hapus sesi ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                
                <form action="{{ route('admin.sesi-posyandus.toggle', $sesi) }}" method="POST">
                    @csrf @method('PATCH')
                    @if($sesi->status === 'aktif')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 text-xs font-bold rounded-xl hover:bg-rose-100 transition-colors border border-rose-100">
                            Selesaikan Sesi
                        </button>
                    @else
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-xl hover:bg-emerald-100 transition-colors border border-emerald-100">
                            Buka Kembali
                        </button>
                    @endif
                </form>
            </div>

            <!-- Dashboard Button -->
            @if($sesi->status === 'aktif')
            <a href="{{ route('admin.sesi-posyandus.operasional', $sesi) }}" class="mt-4 w-full py-2.5 bg-emerald-600 text-white text-center rounded-xl font-bold text-sm shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Masuk Meja Operasional
            </a>
            @endif
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="bg-white rounded-3xl p-10 shadow-sm border border-dashed border-gray-200 inline-block">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Sesi</h3>
                <p class="text-gray-400 text-sm max-w-xs mx-auto mt-1">Silakan buka sesi baru untuk memulai pelayanan posyandu.</p>
                <button @click="openModal()" class="mt-6 px-6 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-200">Buka Sesi Pertama</button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-cloak>
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
             @click.away="showModal = false">
            
            <div class="px-8 py-6 bg-slate-900 flex items-center justify-between">
                <h3 class="text-xl font-black text-white" x-text="editMode ? 'Edit Sesi' : 'Sesi Baru'"></h3>
                <button @click="showModal = false" class="text-white/50 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/sesi-posyandus') }}/' + formData.id : '{{ route('admin.sesi-posyandus.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Sesi / Kegiatan</label>
                        <input type="text" name="nama_sesi" x-model="formData.nama_sesi" required class="w-full bg-slate-50 border-gray-200 rounded-2xl px-5 py-3 text-sm focus:ring-emerald-500/20 focus:border-emerald-500 font-bold" placeholder="Contoh: Posyandu Balita & Ibu Hamil">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" x-model="formData.tanggal" required class="w-full bg-slate-50 border-gray-200 rounded-2xl px-5 py-3 text-sm focus:ring-emerald-500/20 focus:border-emerald-500 font-bold">
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 font-bold text-gray-500">Batal</button>
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white font-black rounded-2xl shadow-xl shadow-slate-200 transition-all active:scale-95" x-text="editMode ? 'Simpan Perubahan' : 'Buka Sesi Sekarang'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
