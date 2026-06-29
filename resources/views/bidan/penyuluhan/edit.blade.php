<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Materi Penyuluhan') }}
        </h2>
    </x-slot>

<div class="max-w-3xl mx-auto mt-6">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('bidan.penyuluhan.index') }}" class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Materi</h2>
            <p class="text-gray-500 text-sm mt-0.5">{{ $penyuluhan->judul }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('bidan.penyuluhan.update', $penyuluhan) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <!-- Judul -->
            <div>
                <label for="judul" class="block text-sm font-bold text-gray-700 mb-1.5">Judul Materi <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="judul" value="{{ old('judul', $penyuluhan->judul) }}" required
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                @error('judul') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-bold text-gray-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" id="kategori" required
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                    @foreach($kategoris as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori', $penyuluhan->kategori) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gambar -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Gambar / Poster</label>
                @if($penyuluhan->gambar)
                    <div class="mb-3 flex items-center gap-4 bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <img src="{{ Storage::url($penyuluhan->gambar) }}" alt="" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                        <div>
                            <p class="text-xs font-bold text-gray-600">Gambar saat ini</p>
                            <label class="flex items-center gap-2 mt-1.5 cursor-pointer">
                                <input type="checkbox" name="hapus_gambar" value="1" class="rounded border-gray-300 text-rose-500 focus:ring-rose-500">
                                <span class="text-xs font-bold text-rose-500">Hapus gambar ini</span>
                            </label>
                        </div>
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors" x-data="{ fileName: '' }">
                    <input type="file" name="gambar" id="gambar" accept="image/*" class="hidden" @change="fileName = $event.target.files[0]?.name || ''">
                    <label for="gambar" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-bold text-gray-500" x-text="fileName || 'Ganti gambar (klik untuk upload)'"></p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau WebP (maks. 2MB)</p>
                    </label>
                </div>
                @error('gambar') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Konten -->
            <div>
                <label for="konten" class="block text-sm font-bold text-gray-700 mb-1.5">Isi Materi</label>
                <textarea name="konten" id="konten" rows="12"
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium leading-relaxed">{{ old('konten', $penyuluhan->konten) }}</textarea>
                @error('konten') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('bidan.penyuluhan.index') }}" class="px-5 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>
</x-bidan-layout>
