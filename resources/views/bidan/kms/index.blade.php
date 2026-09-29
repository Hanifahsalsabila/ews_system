<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Deteksi Dini Stunting & KMS Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Pilih Balita -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <form action="{{ route('bidan.kms.index') }}" method="GET" class="flex gap-4 items-end">
                        <div class="w-full md:w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Anak / Balita</label>
                            <select name="balita_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm rounded-md" required>
                                <option value="">-- Pilih --</option>
                                @foreach($balitas as $b)
                                    <option value="{{ $b->warga_id }}" {{ request('balita_id') == $b->warga_id ? 'selected' : '' }}>
                                        {{ $b->warga->nik }} - {{ $b->warga->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                            Lihat KMS
                        </button>
                    </form>
                </div>
            </div>

            @if($selectedBalita)
                <!-- Peringatan Dini -->
                @php
                    $latestCheck = $riwayatPemeriksaan->last();
                    $isZonaMerah = false;
                    $stuntingStatuses = ['Pendek (Stunted)', 'Sangat Pendek (Severely Stunted)', 'Stunting'];
                    $bgmStatuses = ['Sangat Kurang (Severely Underweight)', 'Kurang', 'BGM'];
                    
                    if($latestCheck) {
                        if(in_array($latestCheck->status_stunting, $stuntingStatuses) || in_array($latestCheck->status_gizi, $bgmStatuses)) {
                            $isZonaMerah = true;
                            $peringatan = "Perhatian! Anak berada di Zona Merah (Stunting/Gizi Buruk/BGM). Segera tindaklanjuti.";
                        }
                    }
                @endphp

                @if($isZonaMerah)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Peringatan Deteksi Dini</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ $peringatan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Grafik Pertumbuhan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Pertumbuhan (Berat Badan)</h3>
                            <div x-data="kmsChart()" x-init="initChart()" class="relative h-64 w-full">
                                <canvas id="kmsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat N/T -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 overflow-x-auto">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Pengukuran (Tren Naik/Turun)</h3>
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Tanggal</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Umur</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">BB</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Z-Score</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Status</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">N/T</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php $prevBB = null; @endphp
                                    @foreach($riwayatPemeriksaan as $cek)
                                        @php
                                            $umurBln = intval(\Carbon\Carbon::parse($selectedBalita->tanggal_lahir)->diffInMonths($cek->created_at));
                                            $tren = '-';
                                            if($prevBB !== null) {
                                                if($cek->berat_badan > $prevBB) $tren = '<span class="text-green-600 font-bold">N</span> (Naik)';
                                                else if($cek->berat_badan < $prevBB) $tren = '<span class="text-red-600 font-bold">T</span> (Turun)';
                                                else $tren = '<span class="text-yellow-600 font-bold">T</span> (Tetap)';
                                            }
                                            $prevBB = $cek->berat_badan;
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2">{{ $cek->created_at->format('d M Y') }}</td>
                                            <td class="px-3 py-2">{{ $umurBln }} Bulan</td>
                                            <td class="px-3 py-2">{{ $cek->berat_badan }} kg</td>
                                            <td class="px-3 py-2">{{ is_null($cek->zscore_bb_u) ? 'N/A' : number_format((float)$cek->zscore_bb_u, 2) }}</td>
                                            <td class="px-3 py-2">
                                                @if(in_array($cek->status_stunting, $stuntingStatuses) || in_array($cek->status_gizi, $bgmStatuses))
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Zona Merah</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">{!! $tren !!}</td>
                                        </tr>
                                    @endforeach
                                    @if($riwayatPemeriksaan->isEmpty())
                                        <tr><td colspan="6" class="text-center py-4 text-gray-500">Belum ada riwayat pemeriksaan</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('kmsChart', () => ({
                            labels: @json($chartData['labels'] ?? []),
                            beratBadan: @json($chartData['beratBadan'] ?? []),
                            
                            initChart() {
                                if(this.labels.length === 0) return;
                                
                                const ctx = document.getElementById('kmsChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: this.labels,
                                        datasets: [
                                            {
                                                label: 'Berat Badan (kg)',
                                                data: this.beratBadan,
                                                borderColor: '#ec4899', // pink-500
                                                backgroundColor: 'rgba(236, 72, 153, 0.1)',
                                                tension: 0.3,
                                                fill: true,
                                                borderWidth: 2,
                                                pointBackgroundColor: '#be185d',
                                                pointRadius: 4
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { position: 'top' }
                                        },
                                        scales: {
                                            y: { beginAtZero: false }
                                        }
                                    }
                                });
                            }
                        }))
                    })
                </script>
                @endpush
            @endif

        </div>
    </div>
</x-bidan-layout>
