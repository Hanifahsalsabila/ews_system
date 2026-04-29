<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Executive Dashboard (Pimpinan)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Balita -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 scale-100 hover:scale-105 transition-transform duration-300">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Balita</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $totalBalita }}</div>
                    </div>
                </div>
                
                <!-- Card 2: Total Ibu Hamil -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-pink-500 scale-100 hover:scale-105 transition-transform duration-300">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Ibu Hamil</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $totalIbuHamil }}</div>
                    </div>
                </div>

                <!-- Card 3: Kehadiran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-emerald-500 scale-100 hover:scale-105 transition-transform duration-300">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Kehadiran Bulan Ini</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $persentaseKehadiran }}%</div>
                    </div>
                </div>

                <!-- Card 4: Stunting/BGM -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-rose-500 scale-100 hover:scale-105 transition-transform duration-300">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Stunting / BGM</div>
                        <div class="text-3xl font-bold text-rose-600">{{ $angkaStunting }} <span class="text-sm text-gray-500 font-normal">kasus</span></div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Kehadiran vs Stunting (6 Bulan Terakhir)</h3>
                    
                    <div x-data="dashboardChart()" x-init="initChart()" class="relative h-96 w-full">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Gunakan Chart.js via NPM bundler jika di Vite, tapi untuk kemudahan injeksi Alpine ini kita panggil CDN atau asumsikan window.Chart karena Vite -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardChart', () => ({
                labels: @json($chartLabels),
                attendance: @json($attendanceData),
                stunting: @json($stuntingData),
                
                initChart() {
                    const ctx = document.getElementById('mainChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels.reverse(),
                            datasets: [
                                {
                                    label: 'Total Kehadiran',
                                    data: this.attendance.reverse(),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 2
                                },
                                {
                                    label: 'Kasus Stunting/BGM',
                                    data: this.stunting.reverse(),
                                    borderColor: '#e11d48',
                                    backgroundColor: 'rgba(225, 29, 72, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            family: "'Inter', sans-serif",
                                            size: 13
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
