@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ 
    showModal: false, 
    editMode: false,
    allRegions: {{ \Illuminate\Support\Js::from($allRegions) }},
    formData: { id: '', name: '', name_number: '', type: '', dusun_id: '', parent_id: '' },
    openModal(region = null, createType = 'dusun', pId = '', dId = '') {
        if (region) {
            this.editMode = true;
            let d_id = '';
            let p_id_val = region.parent_id || '';
            let name_num = region.name;
            if (region.type === 'rt') {
                let rw = this.allRegions.find(r => r.id == region.parent_id);
                if (rw) d_id = rw.parent_id || '';
                name_num = name_num.replace(/^RT\s*/i, '');
            } else if (region.type === 'rw') {
                d_id = region.parent_id || '';
                name_num = name_num.replace(/^RW\s*/i, '');
            }
            this.formData = { 
                id: region.id, 
                name: region.name, 
                name_number: name_num,
                type: region.type, 
                dusun_id: d_id,
                parent_id: p_id_val
            };
        } else {
            this.editMode = false;
            this.formData = { 
                id: '', 
                name: '', 
                name_number: '',
                type: createType, 
                dusun_id: dId, 
                parent_id: pId 
            };
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
        <button @click="openModal(null, 'dusun')" class="inline-flex items-center justify-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-teal-200 transition-all active:scale-95 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Dusun
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

    <!-- Hierarchical Tree View -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6" x-data="{
        get dusuns() {
            return this.allRegions.filter(r => r.type === 'dusun');
        },
        getRws(dusunId) {
            return this.allRegions.filter(r => r.type === 'rw' && r.parent_id === dusunId);
        },
        getRts(rwId) {
            return this.allRegions.filter(r => r.type === 'rt' && r.parent_id === rwId);
        },
        openNodes: []
    }">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Hierarki Wilayah Administrasi
            </h3>
            <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full" x-text="allRegions.length + ' Total Wilayah'"></span>
        </div>

        <template x-if="dusuns.length === 0">
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h4 class="text-gray-500 font-bold">Belum Ada Data Wilayah</h4>
                <p class="text-gray-400 text-sm mt-1">Silakan tambah Dusun terlebih dahulu.</p>
            </div>
        </template>

        <div class="space-y-4">
            <template x-for="dusun in dusuns" :key="dusun.id">
                <div class="border border-gray-100 rounded-2xl overflow-hidden bg-slate-50/50 transition-all hover:border-teal-100">
                    <!-- Dusun Header -->
                    <div class="flex items-center justify-between p-4 bg-white border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-800" x-text="dusun.name"></h4>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Level: <span class="text-purple-600">Dusun</span> &bull; <span x-text="getRws(dusun.id).length + ' Rukun Warga'"></span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openModal(null, 'rw', dusun.id, dusun.id)" class="px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors flex items-center gap-1 tooltip" title="Tambah RW">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                RW
                            </button>
                            <button @click="openModal(dusun)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Edit Dusun">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <form :action="'{{ url('admin/regions') }}/' + dusun.id" method="POST" class="inline" onsubmit="return confirm('Hapus Dusun ini? PERHATIAN: Semua RW dan RT di bawahnya akan ikut terhapus!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors tooltip" title="Hapus Dusun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            <button @click="openNodes.includes(dusun.id) ? openNodes = openNodes.filter(id => id !== dusun.id) : openNodes.push(dusun.id)" class="ml-2 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-gray-500 hover:bg-slate-100 transition-colors">
                                <svg class="w-4 h-4 transform transition-transform duration-300" :class="openNodes.includes(dusun.id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- RW List under Dusun -->
                    <div x-show="openNodes.includes(dusun.id)" x-collapse>
                        <div class="p-4 pl-12 space-y-3 relative before:absolute before:left-9 before:top-0 before:bottom-6 before:w-px before:bg-gray-200">
                            <template x-for="rw in getRws(dusun.id)" :key="rw.id">
                                <div class="relative">
                                    <!-- RW Line connector -->
                                    <div class="absolute -left-3 top-6 w-4 h-px bg-gray-200"></div>
                                    
                                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm hover:border-blue-200 transition-colors group">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                                                    RW
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-gray-800" x-text="rw.name"></h5>
                                                    <p class="text-[10px] text-gray-400 font-bold mt-0.5"><span x-text="getRts(rw.id).length"></span> Rukun Tetangga (RT)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="openModal(null, 'rt', rw.id, dusun.id)" class="px-2 py-1 text-xs font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-md transition-colors flex items-center gap-1 tooltip" title="Tambah RT">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    RT
                                                </button>
                                                <div class="w-px h-4 bg-gray-200 mx-1"></div>
                                                <button @click="openModal(rw)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Edit RW">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form :action="'{{ url('admin/regions') }}/' + rw.id" method="POST" class="inline" onsubmit="return confirm('Hapus RW ini? Semua RT di bawahnya akan terhapus!')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                                <button @click="openNodes.includes(rw.id) ? openNodes = openNodes.filter(id => id !== rw.id) : openNodes.push(rw.id)" class="ml-1 w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center text-gray-500 hover:bg-slate-100 transition-colors">
                                                    <svg class="w-3.5 h-3.5 transform transition-transform duration-300" :class="openNodes.includes(rw.id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- RT List under RW -->
                                        <div x-show="openNodes.includes(rw.id)" x-collapse>
                                            <div class="mt-3 pt-3 border-t border-dashed border-gray-100 flex flex-wrap gap-2">
                                                <template x-for="rt in getRts(rw.id)" :key="rt.id">
                                                    <div class="inline-flex items-center gap-2 pl-2 pr-1 py-1 bg-amber-50 border border-amber-100 rounded-lg group/rt">
                                                        <span class="text-xs font-bold text-amber-700" x-text="rt.name"></span>
                                                        <div class="flex items-center opacity-0 group-hover/rt:opacity-100 transition-opacity bg-white/50 rounded-md">
                                                            <button @click="openModal(rt)" class="p-1 text-blue-600 hover:text-blue-800">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            </button>
                                                            <form :action="'{{ url('admin/regions') }}/' + rt.id" method="POST" class="inline" onsubmit="return confirm('Hapus RT ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-rose-600 hover:text-rose-800">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="getRts(rw.id).length === 0">
                                                    <span class="text-[10px] text-gray-400 italic py-1">Belum ada RT</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="getRws(dusun.id).length === 0">
                                <div class="text-sm text-gray-400 italic pl-2">Belum ada data RW di dusun ini.</div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition.opacity.duration.300ms>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showModal = false" x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            <div class="px-6 py-4 bg-teal-600 flex items-center justify-between shadow-lg">
                <h3 class="text-lg font-bold text-white" x-text="(editMode ? 'Edit ' : 'Tambah ') + (formData.type === 'dusun' ? 'Dusun' : (formData.type === 'rw' ? 'RW' : 'RT'))"></h3>
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
                        
                        <template x-if="formData.type === 'dusun'">
                            <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: Dusun Krajan" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-teal-500 focus:border-teal-500">
                        </template>

                        <template x-if="formData.type !== 'dusun'">
                            <div class="flex shadow-sm">
                                <span class="inline-flex items-center px-4 py-2.5 rounded-l-xl border border-r-0 border-gray-200 bg-gray-100 text-gray-600 sm:text-sm font-bold" x-text="formData.type === 'rw' ? 'RW' : 'RT'"></span>
                                <input type="hidden" name="name" :value="(formData.type === 'rw' ? 'RW ' : 'RT ') + formData.name_number">
                                <input type="text" x-model="formData.name_number" required placeholder="01" class="flex-1 block w-full rounded-none rounded-r-xl bg-slate-50 border-gray-200 px-4 py-2.5 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </template>
                    </div>

                    <template x-if="!editMode && formData.type !== 'dusun'">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex flex-col gap-1.5 shadow-inner">
                            <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Lokasi Induk (Otomatis)
                            </span>
                            <div class="flex items-center text-sm font-bold text-indigo-900 break-all">
                                <span x-text="allRegions.find(r => r.id == formData.dusun_id)?.name"></span>
                                <template x-if="formData.type === 'rt'">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 text-indigo-300 mx-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        <span x-text="allRegions.find(r => r.id == formData.parent_id)?.name"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="editMode">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Tipe Wilayah</label>
                        <select name="type" x-model="formData.type" @change="formData.dusun_id = ''; formData.parent_id = ''" :required="editMode" :disabled="!editMode" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="dusun">Dusun</option>
                            <option value="rw">RW</option>
                            <option value="rt">RT</option>
                        </select>
                    </div>
                    
                    <div x-show="editMode && (formData.type === 'rw' || formData.type === 'rt')">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Pilih Dusun Induk</label>
                        <select x-model="formData.dusun_id" :name="formData.type === 'rw' ? 'parent_id' : ''" @change="if(formData.type === 'rt') formData.parent_id = ''" :required="editMode && (formData.type === 'rw' || formData.type === 'rt')" :disabled="!editMode" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Dusun --</option>
                            <template x-for="r in allRegions.filter(r => r.type === 'dusun')" :key="r.id">
                                <option :value="r.id" x-text="r.name"></option>
                            </template>
                        </select>
                    </div>

                    <div x-show="editMode && formData.type === 'rt'">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Pilih RW Induk</label>
                        <select name="parent_id" x-model="formData.parent_id" :required="editMode && formData.type === 'rt'" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500 disabled:opacity-50" :disabled="!editMode || !formData.dusun_id">
                            <option value="">-- Pilih RW --</option>
                            <template x-for="r in allRegions.filter(r => r.type === 'rw' && r.parent_id == formData.dusun_id)" :key="r.id">
                                <option :value="r.id" x-text="r.name"></option>
                            </template>
                        </select>
                        <p x-show="formData.dusun_id && allRegions.filter(r => r.type === 'rw' && r.parent_id == formData.dusun_id).length === 0" class="text-xs text-rose-500 mt-1">
                            Belum ada RW di Dusun ini.
                        </p>
                    </div>

                    <!-- Hidden Inputs for Readonly Creates -->
                    <template x-if="!editMode">
                        <div>
                            <input type="hidden" name="type" :value="formData.type">
                            <!-- parent_id for RT comes from parent_id, for RW comes from dusun_id -->
                            <template x-if="formData.type === 'rw'">
                                <input type="hidden" name="parent_id" :value="formData.dusun_id">
                            </template>
                            <template x-if="formData.type === 'rt'">
                                <input type="hidden" name="parent_id" :value="formData.parent_id">
                            </template>
                        </div>
                    </template>
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
