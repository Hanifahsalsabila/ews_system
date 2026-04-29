@extends('layouts.admin')

@section('title', 'Master Vaksin & Vitamin')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false,
    formData: { id: '', kode_vaksin: '', nama_vaksin: '', jenis: '', keterangan: '' },
    openModal(vaksin = null) {
        if (vaksin) {
            this.editMode = true;
            this.formData = { ...vaksin };
        } else {
            this.editMode = false;
            this.formData = { id: '', kode_vaksin: '', nama_vaksin: '', jenis: '', keterangan: '' };
        }
        this.showModal = true;
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Master Data Vaksin & Vitamin</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola jenis imunisasi dasar, tambahan, dan suplemen (PMT).</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-pink-200 transition-all active:scale-95 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Data
        </button>
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Vaksin/Vitamin</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vaksins as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ $item->kode_vaksin }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $item->nama_vaksin }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->jenis === 'imunisasi')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">Imunisasi</span>
                            @elseif($item->jenis === 'vitamin')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-wider">Vitamin</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">PMT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->keterangan ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openModal({{ json_encode($item) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.vaksins.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data vaksin/vitamin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vaksins->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $vaksins->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showModal = false">
            <div class="px-6 py-4 bg-pink-600 flex items-center justify-between shadow-lg">
                <h3 class="text-lg font-bold text-white" x-text="editMode ? 'Edit Data' : 'Tambah Data'"></h3>
                <button type="button" @click="showModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/vaksins') }}/' + formData.id : '{{ route('admin.vaksins.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Kode Spesifik</label>
                        <input type="text" name="kode_vaksin" x-model="formData.kode_vaksin" required placeholder="Contoh: BCG-01" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pink-500 focus:border-pink-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Vaksin / Suplemen</label>
                        <input type="text" name="nama_vaksin" x-model="formData.nama_vaksin" required placeholder="Contoh: Vaksin BCG" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Jenis Intervensi</label>
                        <select name="jenis" x-model="formData.jenis" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pink-500 focus:border-pink-500">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="imunisasi">Imunisasi Dasar</option>
                            <option value="vitamin">Suplemen Vitamin</option>
                            <option value="pmt">Pemberian Makanan Tambahan (PMT)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Keterangan / Aturan</label>
                        <textarea name="keterangan" x-model="formData.keterangan" rows="2" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pink-500 focus:border-pink-500" placeholder="(Opsional)"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-pink-600 hover:bg-pink-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-pink-200 transition-all active:scale-95" x-text="editMode ? 'Simpan Perubahan' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
