<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Materi Penyuluhan') }}
        </h2>
    </x-slot>

<div class="max-w-3xl mx-auto mt-6">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('bidan.penyuluhan.index') }}" class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Buat Materi Baru</h2>
            <p class="text-gray-500 text-sm mt-0.5">Tulis materi edukasi kesehatan untuk warga.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('bidan.penyuluhan.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <!-- Judul -->
            <div>
                <label for="judul" class="block text-sm font-bold text-gray-700 mb-1.5">Judul Materi <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium" 
                    placeholder="Contoh: Pentingnya ASI Eksklusif untuk Bayi">
                @error('judul') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-bold text-gray-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" id="kategori" required
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                    @foreach($kategoris as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gambar -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Gambar / Poster (Opsional)</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors" x-data="{ fileName: '' }">
                    <input type="file" name="gambar" id="gambar" accept="image/*" class="hidden" @change="fileName = $event.target.files[0]?.name || ''">
                    <label for="gambar" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-bold text-gray-500" x-text="fileName || 'Klik untuk upload gambar'"></p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau WebP (maks. 2MB)</p>
                    </label>
                </div>
                @error('gambar') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Konten -->
            <div>
                <label for="konten" class="block text-sm font-bold text-gray-700 mb-1.5">Isi Materi</label>
                <textarea name="konten" id="konten" rows="12"
                    class="w-full border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium leading-relaxed" 
                    placeholder="Tulis isi materi penyuluhan di sini...">{{ old('konten') }}</textarea>
                @error('konten') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('bidan.penyuluhan.index') }}" class="px-5 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Batal</a>
            <div class="flex items-center gap-3">
                <button type="submit" name="is_published" value="0" class="px-5 py-3 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 transition-colors">
                    💾 Simpan Draft
                </button>
                <button type="submit" name="is_published" value="1" class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all">
                    🚀 Publish Sekarang
                </button>
            </div>
        </div>
    </form>
</div>
</x-bidan-layout>
