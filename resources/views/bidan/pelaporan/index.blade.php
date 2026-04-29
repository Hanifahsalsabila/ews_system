<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Otomatis & Visualisasi Cakupan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Visualisasi Cakupan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Visualisasi Cakupan Posyandu (Bulan Ini)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                            <div class="text-sm font-medium text-blue-500 uppercase tracking-wider mb-2">Target Layanan</div>
                            <div class="text-4xl font-bold text-blue-700">{{ $targetLayanan }}</div>
                            <div class="text-xs text-gray-500 mt-2">Total Balita + Ibu Hamil</div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                            <div class="text-sm font-medium text-purple-500 uppercase tracking-wider mb-2">Telah Diperiksa</div>
                            <div class="text-4xl font-bold text-purple-700">{{ $pemeriksaanBulanIni }}</div>
                            <div class="text-xs text-gray-500 mt-2">Warga yang hadir bulan ini</div>
                        </div>
                        
                        <div class="bg-emerald-50 rounded-xl p-6 border border-emerald-100 relative overflow-hidden">
                            <div class="text-sm font-medium text-emerald-500 uppercase tracking-wider mb-2 z-10 relative">Persentase Cakupan</div>
                            <div class="text-4xl font-bold text-emerald-700 z-10 relative">{{ $cakupanLayanan }}%</div>
                            
                            <!-- Progress Bar Background -->
                            <div class="absolute bottom-0 left-0 h-2 bg-emerald-200 w-full">
                                <div class="h-full bg-emerald-500" style="width: {{ $cakupanLayanan }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Export Laporan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Laporan Bulanan (Format SIP)
                    </h3>
                    
                    <form action="{{ route('bidan.pelaporan.export') }}" method="POST" class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Format Laporan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Format Laporan</label>
                                <select name="format_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <option value="1">Format 1: Catatan Ibu Hamil, Kelahiran, Kematian</option>
                                    <option value="2">Format 2: Registrasi Bayi dan Balita</option>
                                    <option value="3">Format 3: Register WUS dan PUS</option>
                                    <option value="4">Format 4: Register Ibu Hamil dan Nifas</option>
                                    <option value="5">Format 5: Data Posyandu</option>
                                    <option value="6">Format 6: KMS dan Balita</option>
                                </select>
                            </div>

                            <!-- Periode -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                    <select name="bulan" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        @for($i=1; $i<=12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ now()->month == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                    <select name="tahun" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        @for($i=now()->year; $i>=now()->year-2; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Tanda Tangan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala Desa (Opsional)</label>
                                <input type="text" name="kepala_desa" placeholder="Kosongkan jika tidak perlu" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ketua Kader (Opsional)</label>
                                <input type="text" name="ketua_kader" placeholder="Kosongkan jika tidak perlu" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                        </div>
                        
                        <div class="mt-6 flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Laporan Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-bidan-layout>
