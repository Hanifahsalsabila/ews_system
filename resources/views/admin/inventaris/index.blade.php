@extends('layouts.admin')

@section('title', 'Inventaris Alat Kesehatan')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false,
    formData: { id: '', kode_alat: '', nama_alat: '', kondisi: '', jumlah: 1 },
    openModal(item = null) {
        if (item) {
            this.editMode = true;
            this.formData = { ...item };
        } else {
            this.editMode = false;
            this.formData = { id: '', kode_alat: '', nama_alat: '', kondisi: 'baik', jumlah: 1 };
        }
        this.showModal = true;
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventaris Alat Posyandu</h2>
            <p class="text-gray-500 text-sm mt-1">Pencatatan aset alat ukur (antropometri), timbangan, dan sarana kesehatan.</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-200 transition-all active:scale-95 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Inventaris
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
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kode Aset</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Alat</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Jumlah</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status Kondisi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($inventaris as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $item->kode_alat }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $item->nama_alat }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full">{{ $item->jumlah }} Unit</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->kondisi === 'baik')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layak Pakai</span>
                            @elseif($item->kondisi === 'rusak_ringan')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Rusak Ringan</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rusak Berat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openModal({{ json_encode($item) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.inventaris.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus aset ini?')">
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data inventaris/alat ukur.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inventaris->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $inventaris->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showModal = false">
            <div class="px-6 py-4 bg-amber-500 flex items-center justify-between shadow-lg">
                <h3 class="text-lg font-bold text-white" x-text="editMode ? 'Edit Aset' : 'Registrasi Aset'"></h3>
                <button type="button" @click="showModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/inventaris') }}/' + formData.id : '{{ route('admin.inventaris.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Kode / Nomor Seri Alat</label>
                        <input type="text" name="kode_alat" x-model="formData.kode_alat" required placeholder="Contoh: INV-DACIN-01" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-amber-500 focus:border-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Alat</label>
                        <input type="text" name="nama_alat" x-model="formData.nama_alat" required placeholder="Timbangan Digital Bayi Merk X" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Jumlah (Unit)</label>
                            <input type="number" name="jumlah" x-model="formData.jumlah" required min="1" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Kondisi</label>
                            <select name="kondisi" x-model="formData.kondisi" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <option value="baik">Layak Pakai / Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-200 transition-all active:scale-95" x-text="editMode ? 'Simpan Perubahan' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
