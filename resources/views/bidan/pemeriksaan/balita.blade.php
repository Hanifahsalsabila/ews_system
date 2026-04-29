<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pemeriksaan Anak / Balita') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, selectedId: null, selectedName: '', selectedUmur: 0 }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(!$sesiAktif)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-sm text-red-700">Tidak ada sesi Posyandu yang sedang berjalan. Anda tidak dapat menyimpan pemeriksaan.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK / Nama Ortu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir / Umur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($balita as $anak)
                                @php
                                    $umurBulan = intval(\Carbon\Carbon::parse($anak->tanggal_lahir)->diffInMonths(now()));
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $anak->warga->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $anak->warga->nik }}<br>
                                        <span class="text-xs text-gray-400">{{ $anak->nama_ibu }} / {{ $anak->nama_ayah }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($anak->tanggal_lahir)->format('d M Y') }} <br>
                                        <span class="text-xs text-blue-500 font-bold">{{ $umurBulan }} Bulan</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $anak->warga->jenis_kelamin }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button 
                                            @click="openModal = true; selectedId = {{ $anak->warga->id }}; selectedName = '{{ addslashes($anak->warga->nama) }}'; selectedUmur = {{ $umurBulan }}"
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-md"
                                            {{ !$sesiAktif ? 'disabled' : '' }}
                                        >
                                            Input Pemeriksaan
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($balita->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data Anak/Balita terdaftar.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Form Input -->
        <div x-show="openModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openModal" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form action="{{ route('bidan.pemeriksaan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="warga_id" x-model="selectedId">
                        <input type="hidden" name="sesi_posyandu_id" value="{{ $sesiAktif ? $sesiAktif->id : '' }}">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Input Pemeriksaan: <span x-text="selectedName" class="font-bold text-blue-600"></span> (<span x-text="selectedUmur"></span> Bulan)</h3>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
                                    <input type="number" step="0.1" name="tinggi_badan" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                                    <input type="number" step="0.1" name="berat_badan" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lingkar Kepala (cm)</label>
                                    <input type="number" step="0.1" name="lingkar_kepala" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="mt-6 border-t pt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pemberian Imunisasi / Vaksin</label>
                                <p class="text-xs text-gray-500 mb-2">Checklist berdasarkan umur anak. (Akan disimpan sebagai Catatan Pemeriksaan)</p>
                                
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <label class="inline-flex items-center" x-show="selectedUmur <= 1">
                                        <input type="checkbox" name="imunisasi[]" value="HB0" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">HB 0 (0-7 Hari)</span>
                                    </label>
                                    <label class="inline-flex items-center" x-show="selectedUmur >= 1 && selectedUmur <= 3">
                                        <input type="checkbox" name="imunisasi[]" value="BCG, Polio 1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">BCG, Polio 1 (1 Bulan)</span>
                                    </label>
                                    <label class="inline-flex items-center" x-show="selectedUmur >= 2 && selectedUmur <= 4">
                                        <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 1, Polio 2" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">DPT-HB-Hib 1, Polio 2 (2 Bln)</span>
                                    </label>
                                    <label class="inline-flex items-center" x-show="selectedUmur >= 3 && selectedUmur <= 5">
                                        <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 2, Polio 3" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">DPT-HB-Hib 2, Polio 3 (3 Bln)</span>
                                    </label>
                                    <label class="inline-flex items-center" x-show="selectedUmur >= 4 && selectedUmur <= 6">
                                        <input type="checkbox" name="imunisasi[]" value="DPT-HB-Hib 3, Polio 4, IPV" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">DPT-HB-Hib 3, Polio 4, IPV (4 Bln)</span>
                                    </label>
                                    <label class="inline-flex items-center" x-show="selectedUmur >= 9 && selectedUmur <= 12">
                                        <input type="checkbox" name="imunisasi[]" value="Campak (MR)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">Campak/MR (9 Bulan)</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Keluhan / Catatan Lain</label>
                                <textarea name="keluhan" rows="2" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            <!-- Hidden input to gather array of imunisasi in controller -->
                            <input type="hidden" name="is_balita_form" value="1">
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan Data
                            </button>
                            <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-bidan-layout>
