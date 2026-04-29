<x-bidan-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Dashboard Utama') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Sistem Informasi Posyandu Terpadu</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            


            <!-- Metrics Cards -->
            <h3 class="text-lg font-black text-slate-800 tracking-tight mt-8 mb-4">Ringkasan Statistik</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sasaran</span>
                        </div>
                        <div class="text-3xl font-black text-slate-800 mb-1">{{ $totalBalita }}</div>
                        <div class="text-sm font-medium text-slate-500">Total Anak & Balita</div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sasaran</span>
                        </div>
                        <div class="text-3xl font-black text-slate-800 mb-1">{{ $totalIbuHamil }}</div>
                        <div class="text-sm font-medium text-slate-500">Total Ibu Hamil</div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">Bulan Ini</span>
                        </div>
                        <div class="text-3xl font-black text-slate-800 mb-1">{{ $persentaseKehadiran }}%</div>
                        <div class="text-sm font-medium text-slate-500">Cakupan Kehadiran</div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full">Alert</span>
                        </div>
                        <div class="text-3xl font-black text-rose-600 mb-1">{{ $angkaStunting }} <span class="text-base font-bold text-slate-400 tracking-normal">kasus</span></div>
                        <div class="text-sm font-medium text-slate-500">Stunting / BGM Baru</div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Analitik Perkembangan</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Tren Kehadiran vs Temuan Stunting (6 Bulan Terakhir)</p>
                        </div>
                    </div>
                    
                    <div x-data="dashboardChart()" x-init="initChart()" class="relative h-[350px] w-full">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardChart', () => ({
                labels: @json($chartLabels),
                attendance: @json($attendanceData),
                stunting: @json($stuntingData),
                
                initChart() {
                    const ctx = document.getElementById('mainChart').getContext('2d');
                    
                    // Create gradient for attendance
                    let gradientAtt = ctx.createLinearGradient(0, 0, 0, 400);
                    gradientAtt.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                    gradientAtt.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                    // Create gradient for stunting
                    let gradientStunt = ctx.createLinearGradient(0, 0, 0, 400);
                    gradientStunt.addColorStop(0, 'rgba(244, 63, 94, 0.4)');
                    gradientStunt.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels.reverse(),
                            datasets: [
                                {
                                    label: 'Kehadiran Warga',
                                    data: this.attendance.reverse(),
                                    borderColor: '#10b981', // emerald-500
                                    backgroundColor: gradientAtt,
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#10b981',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: 'Kasus Stunting/BGM',
                                    data: this.stunting.reverse(),
                                    borderColor: '#f43f5e', // rose-500
                                    backgroundColor: gradientStunt,
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#f43f5e',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: 20,
                                        font: {
                                            family: "'Inter', sans-serif",
                                            size: 13,
                                            weight: '600'
                                        },
                                        color: '#64748b'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 12,
                                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                                    bodyFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                                    cornerRadius: 12,
                                    displayColors: true,
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                        color: '#94a3b8'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    border: { display: false },
                                    grid: {
                                        color: '#f1f5f9',
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        precision: 0,
                                        font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                        color: '#94a3b8',
                                        padding: 10
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
</x-bidan-layout>
