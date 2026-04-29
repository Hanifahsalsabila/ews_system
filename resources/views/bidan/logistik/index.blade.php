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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Nama Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Kode Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Jumlah Tersedia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-emerald-800 uppercase tracking-wider">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inventaris as $inv)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $inv->nama_barang }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $inv->kode_barang }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $inv->kategori }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-lg font-bold text-gray-900">{{ $inv->jumlah }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inv->kondisi == 'Baik' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $inv->kondisi }}
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
</x-bidan-layout>
