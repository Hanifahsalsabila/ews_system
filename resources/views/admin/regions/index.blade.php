@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false,
    allRegions: {{ \Illuminate\Support\Js::from($allRegions) }},
    formData: { id: '', name: '', type: '', parent_name: '' },
    openModal(region = null) {
        if (region) {
            this.editMode = true;
            this.formData = { 
                id: region.id, 
                name: region.name, 
                type: region.type, 
                parent_name: region.parent ? region.parent.name : ''
            };
        } else {
            this.editMode = false;
            this.formData = { id: '', name: '', type: '', parent_name: '' };
        }
        this.showModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Master Data Wilayah</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola hierarki wilayah Dusun, RW, dan RT secara terstruktur.</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-teal-200 transition-all active:scale-95 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Wilayah
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

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Wilayah</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Induk Wilayah</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($regions as $region)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $region->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($region->type === 'dusun')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-wider">Dusun</span>
                            @elseif($region->type === 'rw')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">RW</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-wider">RT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $region->parent ? $region->parent->name : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openModal({{ json_encode($region) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.regions.destroy', $region) }}" method="POST" onsubmit="return confirm('Hapus wilayah ini? Jika ada child region, akan ikut terhapus.')">
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
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data wilayah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($regions->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $regions->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition.opacity.duration.300ms>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showModal = false" x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            <div class="px-6 py-4 bg-teal-600 flex items-center justify-between shadow-lg">
                <h3 class="text-lg font-bold text-white" x-text="editMode ? 'Edit Wilayah' : 'Tambah Wilayah'"></h3>
                <button type="button" @click="showModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/regions') }}/' + formData.id : '{{ route('admin.regions.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Wilayah</label>
                        <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: Dusun Krajan / RW 01" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Tipe Wilayah</label>
                        <select name="type" x-model="formData.type" @change="formData.parent_name = ''" required class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="dusun">Dusun</option>
                            <option value="rw">RW</option>
                            <option value="rt">RT</option>
                        </select>
                    </div>
                    <div x-show="formData.type !== 'dusun'">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Induk Wilayah</label>
                        <select name="parent_name" x-model="formData.parent_name" :required="formData.type === 'rw' || formData.type === 'rt'" :disabled="!formData.type" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Induk Wilayah --</option>
                            <template x-for="r in allRegions.filter(r => formData.type === 'rw' ? r.type === 'dusun' : (formData.type === 'rt' ? r.type === 'rw' : false))" :key="r.id">
                                <option :value="r.name" x-text="r.name"></option>
                            </template>
                        </select>
                        <p x-show="formData.type && formData.type !== 'dusun' && allRegions.filter(r => formData.type === 'rw' ? r.type === 'dusun' : (formData.type === 'rt' ? r.type === 'rw' : false)).length === 0" class="text-xs text-rose-500 mt-1">
                            Belum ada data induk wilayah untuk tipe ini.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-teal-200 transition-all active:scale-95" x-text="editMode ? 'Simpan Perubahan' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
