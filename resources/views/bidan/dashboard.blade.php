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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
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

                <!-- Card Lansia -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sasaran</span>
                        </div>
                        <div class="text-3xl font-black text-slate-800 mb-1">{{ $totalLansia }}</div>
                        <div class="text-sm font-medium text-slate-500">Total Lansia</div>
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

            @if(($incompleteAlertsCount ?? 0) > 0)
                <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900">Pemberitahuan Data Antropometri Belum Lengkap</h4>
                            <p class="text-xs text-amber-700 mt-0.5">Terdapat <span class="font-bold underline">{{ $incompleteAlertsCount }} pemeriksaan balita</span> dengan indikator tidak lengkap/gagal hitung. Balita ini tidak diklasifikasikan sebagai normal dan memerlukan pengukuran ulang oleh Kader/Bidan.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Priority Alert Matrix (EWS) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="p-6 lg:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Priority Alert Decision Matrix (EWS)
                        </h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Daftar balita berisiko yang memerlukan tindakan klinis atau rujukan segera berdasarkan Z-Score WHO.</p>
                    </div>

                    <!-- Filter Tabs: Lifecycle Alert EWS -->
                    <div class="flex items-center gap-1.5 bg-slate-200/60 p-1.5 rounded-2xl text-xs font-bold">
                        <a href="{{ route('bidan.dashboard', ['alert_filter' => 'active']) }}" class="px-3.5 py-1.5 rounded-xl transition-all {{ ($alertFilter ?? 'active') === 'active' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Aktif ({{ $activeAlertsCount ?? 0 }})
                        </a>
                        <a href="{{ route('bidan.dashboard', ['alert_filter' => 'resolved']) }}" class="px-3.5 py-1.5 rounded-xl transition-all {{ ($alertFilter ?? 'active') === 'resolved' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Selesai ({{ $resolvedAlertsCount ?? 0 }})
                        </a>
                        <a href="{{ route('bidan.dashboard', ['alert_filter' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl transition-all {{ ($alertFilter ?? 'active') === 'all' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Semua
                        </a>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-100">
                                    <th class="px-6 py-4">Nama Balita</th>
                                    <th class="px-6 py-4">Kategori & Prioritas</th>
                                    <th class="px-6 py-4">Status Z-Score (WHO)</th>
                                    <th class="px-6 py-4">Status Siklus</th>
                                    <th class="px-6 py-4 w-1/4">Rekomendasi / Pesan EWS</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100/80">
                                @forelse($ewsAlerts as $alert)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800">{{ $alert->pemeriksaan->warga->nama }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">NIK: {{ $alert->pemeriksaan->warga->nik }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1.5">
                                                @if($alert->ews_status == 'bahaya')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span> Bahaya (Merah)
                                                    </span>
                                                @elseif($alert->ews_status == 'risiko_tinggi')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Risiko Tinggi (Oranye)
                                                    </span>
                                                @elseif($alert->ews_status == 'data_tidak_lengkap')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Data Tidak Lengkap
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Waspada (Kuning)
                                                    </span>
                                                @endif

                                                <div class="flex items-center gap-1">
                                                    <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                                        Skor: {{ number_format((float) ($alert->priority_score ?? 0), 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-bold text-slate-700 space-y-0.5">
                                                <div>WAZ (BB/U): <span class="{{ ($alert->zscore_bb_u ?? 0) < -2 ? 'text-rose-600' : 'text-slate-800' }}">{{ is_null($alert->zscore_bb_u) ? 'N/A' : number_format((float)$alert->zscore_bb_u, 2) }}</span></div>
                                                <div>HAZ (TB/U): <span class="{{ ($alert->zscore_tb_u ?? 0) < -2 ? 'text-rose-600' : 'text-slate-800' }}">{{ is_null($alert->zscore_tb_u) ? 'N/A' : number_format((float)$alert->zscore_tb_u, 2) }}</span></div>
                                                <div>WHZ (BB/TB): <span class="{{ ($alert->zscore_bb_tb ?? 0) < -2 ? 'text-rose-600' : 'text-slate-800' }}">{{ is_null($alert->zscore_bb_tb) ? 'N/A' : number_format((float)$alert->zscore_bb_tb, 2) }}</span></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(($alert->alert_status ?? 'pending') === 'resolved')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    ✓ Ditangani
                                                </span>
                                            @elseif(($alert->alert_status ?? 'pending') === 'in_progress')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                    ⏳ Proses
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    ● Menunggu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ $alert->ews_message }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('bidan.pemeriksaan.tindak-lanjut', $alert->pemeriksaan->id) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white transition-all bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-sm">
                                                {{ ($alert->alert_status ?? 'pending') === 'resolved' ? 'Lihat Tindakan' : 'Tindak Lanjut' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-slate-800">Tidak ada Peringatan EWS</h3>
                                            <p class="text-sm text-slate-500 mt-1">Tidak ada data balita yang memerlukan penanganan darurat pada kategori ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
