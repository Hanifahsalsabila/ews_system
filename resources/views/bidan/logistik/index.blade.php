<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pemantauan Logistik & Vaksin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Data Vaksin -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        Stok Vaksin (Imunisasi)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Nama Vaksin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Batch Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Stok Tersedia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Tanggal Kedaluwarsa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vaksins as $vaksin)
                                    @php
                                        $isExpiringSoon = \Carbon\Carbon::parse($vaksin->tanggal_kadaluarsa)->diffInDays(now()) < 30;
                                        $isExpired = \Carbon\Carbon::parse($vaksin->tanggal_kadaluarsa)->isPast();
                                        $isLowStock = $vaksin->stok < 10;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $vaksin->nama_vaksin }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $vaksin->batch_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-lg font-bold {{ $isLowStock ? 'text-red-600' : 'text-gray-900' }}">{{ $vaksin->stok }}</span> dosis
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $isExpired ? 'text-red-600 font-bold' : ($isExpiringSoon ? 'text-orange-500 font-bold' : 'text-gray-500') }}">
                                                {{ \Carbon\Carbon::parse($vaksin->tanggal_kadaluarsa)->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($isExpired)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Kedaluwarsa</span>
                                            @elseif($isExpiringSoon)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Hampir Kedaluwarsa</span>
                                            @elseif($isLowStock)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Stok Menipis</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aman</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Logistik Tambahan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Inventaris & Logistik Tambahan (Vit A, TTD, PMT)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-emerald-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Nama Alat/Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Kode Alat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Jumlah Tersedia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inventaris as $inv)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $inv->nama_alat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $inv->kode_alat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-lg font-bold text-gray-900">{{ $inv->jumlah }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inv->kondisi == 'baik' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $inv->kondisi)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals (Alpine.js required) -->
    <div x-data="{ modalVaksin: false, modalInventaris: false }" @keydown.escape.window="modalVaksin = false; modalInventaris = false">
        
        <!-- Button Controls (Float right or inside headers) -->
        <div class="fixed bottom-8 right-8 flex flex-col gap-3 z-50">
            <button @click="modalInventaris = true" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-full shadow-lg font-bold transition-transform transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Logistik
            </button>
            <button @click="modalVaksin = true" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-full shadow-lg font-bold transition-transform transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Vaksin
            </button>
        </div>

        <!-- Modal Tambah Vaksin -->
        <div x-show="modalVaksin" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalVaksin" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="modalVaksin" x-transition
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('bidan.logistik.vaksin.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6 border-b pb-3">Tambah Stok Vaksin</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Vaksin</label>
                                    <input type="text" name="nama_vaksin" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Batch Number</label>
                                    <input type="text" name="batch_number" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Stok (Dosis)</label>
                                        <input type="number" name="stok" min="0" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tgl Kedaluwarsa</label>
                                        <input type="date" name="tanggal_kadaluarsa" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                                Simpan Vaksin
                            </button>
                            <button type="button" @click="modalVaksin = false" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Inventaris -->
        <div x-show="modalInventaris" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalInventaris" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="modalInventaris" x-transition
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('bidan.logistik.inventaris.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6 border-b pb-3">Tambah Inventaris / Logistik</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Alat/Barang</label>
                                    <input type="text" name="nama_alat" required class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Kode Alat</label>
                                        <input type="text" name="kode_alat" required class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah</label>
                                        <input type="number" name="jumlah" min="0" required class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kondisi</label>
                                    <select name="kondisi" required class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="baik">Baik</option>
                                        <option value="rusak_ringan">Rusak Ringan</option>
                                        <option value="rusak_berat">Rusak Berat</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:w-auto sm:text-sm">
                                Simpan Barang
                            </button>
                            <button type="button" @click="modalInventaris = false" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-bidan-layout>
