<x-kader-layout>
    <div class="mb-8">
        <a href="{{ route('kader.warga.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-emerald-600 transition font-medium text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Warga
        </a>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Edit Data Warga</h1>
        <p class="text-slate-500 mt-1 font-medium">Perbarui informasi data diri warga.</p>
    </div>

    <form action="{{ route('kader.warga.update', $warga->id) }}" method="POST" class="max-w-3xl space-y-8">
        @csrf
        @method('PUT')
        
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
                    <input type="number" name="nik" value="{{ old('nik', $warga->nik) }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="16 Digit NIK">
                    @error('nik') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $warga->nama) }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="Sesuai KTP/KK">
                    @error('nama') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Wilayah (RT/RW) <span class="text-rose-500">*</span></label>
                        <select name="region_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $warga->region_id) == $region->id ? 'selected' : '' }}>{{ strtoupper($region->type) }} {{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Telepon/WA (Opsional)</label>
                        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $warga->nomor_telepon) }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium" placeholder="08xx...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap (Opsional)</label>
                    <textarea name="alamat_lengkap" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">{{ old('alamat_lengkap', $warga->alamat_lengkap) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                    2
                </div>
                Data Kategori Khusus ({{ ucfirst(str_replace('_', ' ', $cohort_type)) }})
            </h3>

            @if($cohort_type === 'balita')
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lahir Anak</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga->balita->tanggal_lahir ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                            <option value="L" {{ old('jenis_kelamin', $warga->balita->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin', $warga->balita->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Ibu</label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $warga->balita->nama_ibu ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Ayah</label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $warga->balita->nama_ayah ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                </div>
            </div>
            @elseif($cohort_type === 'ibu_hamil')
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">HPHT (Hari Pertama Haid Terakhir)</label>
                        <input type="date" name="hpht" value="{{ old('hpht', $warga->ibuHamil->hpht ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">HPL (Hari Perkiraan Lahir)</label>
                        <input type="date" name="hpl" value="{{ old('hpl', $warga->ibuHamil->hpl ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kehamilan Ke</label>
                    <input type="number" name="kehamilan_ke" value="{{ old('kehamilan_ke', $warga->ibuHamil->kehamilan_ke ?? 1) }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                </div>
            </div>
            @elseif($cohort_type === 'lansia')
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Golongan Darah</label>
                        <select name="golongan_darah" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                            <option value="">-- Pilih --</option>
                            <option value="A" {{ old('golongan_darah', $warga->lansia->golongan_darah ?? '') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('golongan_darah', $warga->lansia->golongan_darah ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('golongan_darah', $warga->lansia->golongan_darah ?? '') == 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('golongan_darah', $warga->lansia->golongan_darah ?? '') == 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Riwayat Penyakit Khusus</label>
                        <input type="text" name="riwayat_penyakit" value="{{ old('riwayat_penyakit', $warga->lansia->riwayat_penyakit ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium" placeholder="Hipertensi, Diabetes, dll">
                    </div>
                </div>
            </div>
            @else
            <p class="text-slate-500 text-sm">Tidak ada kolom tambahan untuk kategori ini.</p>
            @endif
        </div>

        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('kader.warga.index') }}" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-4 px-8 rounded-2xl transition w-full sm:w-auto text-center">Batal</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl transition shadow-xl shadow-emerald-500/30 text-lg w-full sm:w-auto">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-kader-layout>
