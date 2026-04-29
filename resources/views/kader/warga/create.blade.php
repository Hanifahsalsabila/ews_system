<x-kader-layout>
    <div class="mb-8">
        <a href="{{ route('kader.warga.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-emerald-600 transition font-medium text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Warga
        </a>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Daftarkan Warga Baru</h1>
        <p class="text-slate-500 mt-1 font-medium">Lengkapi data identitas warga baru di bawah ini.</p>
    </div>

    <form action="{{ route('kader.warga.store') }}" method="POST" class="max-w-3xl space-y-8">
        @csrf
        
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    1
                </div>
                Data Identitas Utama
            </h3>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">NIK <span class="text-rose-500">*</span></label>
                    <input type="number" name="nik" value="{{ old('nik') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="16 Digit NIK">
                    @error('nik') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="Sesuai KTP/KK">
                    @error('nama') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Wilayah (RT/RW) <span class="text-rose-500">*</span></label>
                        <select name="region_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ strtoupper($region->type) }} {{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Telepon/WA (Opsional)</label>
                        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="08xx...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap (Opsional)</label>
                    <textarea name="alamat_lengkap" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">{{ old('alamat_lengkap') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm" x-data="{ cohort: '{{ old('cohort_type', 'balita') }}' }">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                    2
                </div>
                Kategori Layanan
            </h3>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Pilih Kategori Posyandu <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cohort_type" value="balita" x-model="cohort" class="sr-only">
                        <div :class="cohort === 'balita' ? 'border-teal-500 bg-teal-50' : 'border-slate-100 hover:border-teal-300 hover:bg-slate-50'" class="p-3 border-2 rounded-xl text-center transition">
                            <p :class="cohort === 'balita' ? 'text-teal-700' : 'text-slate-700'" class="font-bold text-sm">Balita</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cohort_type" value="ibu_hamil" x-model="cohort" class="sr-only">
                        <div :class="cohort === 'ibu_hamil' ? 'border-teal-500 bg-teal-50' : 'border-slate-100 hover:border-teal-300 hover:bg-slate-50'" class="p-3 border-2 rounded-xl text-center transition">
                            <p :class="cohort === 'ibu_hamil' ? 'text-teal-700' : 'text-slate-700'" class="font-bold text-sm">Ibu Hamil</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cohort_type" value="lansia" x-model="cohort" class="sr-only">
                        <div :class="cohort === 'lansia' ? 'border-teal-500 bg-teal-50' : 'border-slate-100 hover:border-teal-300 hover:bg-slate-50'" class="p-3 border-2 rounded-xl text-center transition">
                            <p :class="cohort === 'lansia' ? 'text-teal-700' : 'text-slate-700'" class="font-bold text-sm">Lansia</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="cohort_type" value="umum" x-model="cohort" class="sr-only">
                        <div :class="cohort === 'umum' ? 'border-teal-500 bg-teal-50' : 'border-slate-100 hover:border-teal-300 hover:bg-slate-50'" class="p-3 border-2 rounded-xl text-center transition">
                            <p :class="cohort === 'umum' ? 'text-teal-700' : 'text-slate-700'" class="font-bold text-sm">WUS / PUS</p>
                        </div>
                    </label>
                </div>
                @error('cohort_type') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Balita Specific Fields -->
            <div x-show="cohort === 'balita'" x-transition class="mt-6 pt-6 border-t border-slate-100 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lahir Anak <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" :required="cohort === 'balita'" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                        @error('tanggal_lahir') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select name="jenis_kelamin" :required="cohort === 'balita'" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                            <option value="">-- Pilih L/P --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                        @error('jenis_kelamin') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Ibu</label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Ayah</label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl transition shadow-xl shadow-emerald-500/30 text-lg w-full sm:w-auto">
                Simpan Data Warga
            </button>
        </div>
    </form>
</x-kader-layout>
