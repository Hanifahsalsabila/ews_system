<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifikasi & Pengingat Posyandu (WhatsApp Gateway)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($sesiMendatang)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Sesi Posyandu Mendatang</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Ada sesi Posyandu terjadwal pada <strong>{{ \Carbon\Carbon::parse($sesiMendatang->tanggal)->format('d F Y') }}</strong> ({{ $sesiMendatang->lokasi }}). Silakan kirim pengingat ke warga.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200" x-data="{ activeTab: 'balita' }">
                    
                    <div class="mb-4 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'balita'" :class="{'border-green-500 text-green-600': activeTab === 'balita', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'balita'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Orang Tua Balita
                            </button>
                            <button @click="activeTab = 'ibu_hamil'" :class="{'border-green-500 text-green-600': activeTab === 'ibu_hamil', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'ibu_hamil'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Ibu Hamil
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Orang Tua Balita -->
                    <div x-show="activeTab === 'balita'" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Warga / Ortu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kirim Pengingat Jadwal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($balitas as $balita)
                                    @php
                                        // Clean phone number
                                        $noHp = $balita->warga->nomor_telepon;
                                        if($noHp && substr($noHp, 0, 1) == '0') {
                                            $noHp = '62' . substr($noHp, 1);
                                        }
                                        $jadwalStr = $sesiMendatang ? \Carbon\Carbon::parse($sesiMendatang->tanggal)->format('d F Y') : 'dalam waktu dekat';
                                        $lokasiStr = $sesiMendatang ? $sesiMendatang->lokasi : 'Posyandu Terdekat';
                                        $pesanJadwal = "Halo Ibu " . $balita->nama_ibu . ", mengingatkan jadwal Posyandu untuk ananda " . $balita->warga->nama . " pada hari " . $jadwalStr . " di " . $lokasiStr . ". Mohon kehadirannya ya Bu. Terima kasih.";
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $balita->nama_ibu }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balita->warga->nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balita->warga->nomor_telepon ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($noHp)
                                                <a href="https://wa.me/{{ $noHp }}?text={{ urlencode($pesanJadwal) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                                    <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    Kirim WA
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">Tidak ada nomor</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tab Ibu Hamil -->
                    <div x-show="activeTab === 'ibu_hamil'" class="overflow-x-auto" style="display: none;">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Ibu Hamil</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehamilan Ke-</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor HP</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kirim Pengingat Jadwal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ibuHamils as $ibu)
                                    @php
                                        // Clean phone number
                                        $noHp = $ibu->warga->nomor_telepon;
                                        if($noHp && substr($noHp, 0, 1) == '0') {
                                            $noHp = '62' . substr($noHp, 1);
                                        }
                                        $jadwalStr = $sesiMendatang ? \Carbon\Carbon::parse($sesiMendatang->tanggal)->format('d F Y') : 'dalam waktu dekat';
                                        $lokasiStr = $sesiMendatang ? $sesiMendatang->lokasi : 'Posyandu Terdekat';
                                        $pesanJadwal = "Halo Ibu " . $ibu->warga->nama . ", mengingatkan jadwal Posyandu Ibu Hamil pada hari " . $jadwalStr . " di " . $lokasiStr . ". Mohon kehadirannya untuk periksa rutin ya Bu. Terima kasih.";
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ibu->warga->nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ibu->kehamilan_ke }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ibu->warga->nomor_telepon ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($noHp)
                                                <a href="https://wa.me/{{ $noHp }}?text={{ urlencode($pesanJadwal) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                                    <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    Kirim WA
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">Tidak ada nomor</span>
                                            @endif
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
