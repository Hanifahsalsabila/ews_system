@extends('layouts.warga')

@section('content')
    <!-- ===== BACK HEADER ===== -->
    <div class="animate-fade-in-up mb-6 sm:mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('warga.dashboard') }}" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all active:scale-95 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">{{ $warga->nama }}</h2>
                <p class="text-slate-500 text-xs sm:text-sm font-bold mt-0.5">Rekam Medis & Riwayat Kunjungan</p>
            </div>
        </div>
    </div>

    @if(isset($wargas) && $wargas->count() > 1)
    <div class="mb-6 overflow-x-auto pb-2 animate-fade-in-up stagger-1">
        <div class="flex space-x-3">
            @foreach($wargas as $w)
            <a href="{{ route('warga.riwayat', ['warga_id' => $w->id]) }}" 
               class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $warga->id === $w->id ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-white text-slate-500 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200' }}">
                👧👦 {{ $w->nama }}
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @php
        $cohortColor = 'from-slate-500 to-slate-600';
        $cohortLabel = 'Umum / WUS';
        $cohortIcon = '👤';
        if ($warga->balita) {
            $cohortColor = 'from-teal-500 to-cyan-500';
            $cohortLabel = 'Balita';
            $cohortIcon = '👶';
        } elseif ($warga->ibuHamil) {
            $cohortColor = 'from-rose-500 to-pink-500';
            $cohortLabel = 'Ibu Hamil';
            $cohortIcon = '🤰';
        } elseif ($warga->lansia) {
            $cohortColor = 'from-amber-500 to-orange-500';
            $cohortLabel = 'Lansia';
            $cohortIcon = '👴';
        }
        $lastPemeriksaan = $warga->pemeriksaans->last();
    @endphp

    <!-- ===== STATUS OVERVIEW CARDS ===== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8 animate-fade-in-up stagger-1">
        <!-- Kohort -->
        <div class="bg-gradient-to-br {{ $cohortColor }} rounded-2xl p-4 sm:p-5 text-white relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-white/10 rounded-full"></div>
            <p class="text-3xl mb-1">{{ $cohortIcon }}</p>
            <p class="text-sm font-black">{{ $cohortLabel }}</p>
            <p class="text-white/70 text-[10px] font-bold mt-0.5">Kategori</p>
        </div>
        <!-- Total Kunjungan -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $warga->pemeriksaans->count() }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-0.5">Total Kunjungan</p>
        </div>
        <!-- BB Terakhir -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $lastPemeriksaan->berat_badan ?? '-' }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-0.5">BB Terakhir (kg)</p>
        </div>
        <!-- TB Terakhir -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $lastPemeriksaan->tinggi_badan ?? '-' }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-0.5">TB Terakhir (cm)</p>
        </div>
    </div>

    <!-- ===== LAYOUT 2 COLUMNS ON DESKTOP ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8">

        <!-- LEFT: Chart (takes 3 cols on desktop) -->
        <div class="lg:col-span-3 space-y-6">
            @if($warga->balita && $warga->pemeriksaans->count() > 0)
            <!-- KMS Chart -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-sm animate-fade-in-up stagger-2">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-black text-slate-800 flex items-center gap-2 text-base sm:text-lg">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        </div>
                        Grafik Pertumbuhan (KMS)
                    </h3>
                </div>

                @php
                    $labels = [];
                    $bbData = [];
                    $tbData = [];
                    foreach($warga->pemeriksaans as $p) {
                        $ageDays = \Carbon\Carbon::parse($warga->balita->tanggal_lahir)->diffInDays(\Carbon\Carbon::parse($p->created_at));
                        $ageMonths = round($ageDays / 30.44);
                        $labels[] = $ageMonths . " bln";
                        $bbData[] = $p->berat_badan;
                        $tbData[] = $p->tinggi_badan;
                    }
                @endphp

                <div class="relative h-64 sm:h-80 w-full">
                    <canvas id="kmsChart"></canvas>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const ctx = document.getElementById('kmsChart').getContext('2d');
                        const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient1.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
                        gradient1.addColorStop(1, 'rgba(16, 185, 129, 0.01)');
                        const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient2.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                        gradient2.addColorStop(1, 'rgba(59, 130, 246, 0.01)');

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {!! json_encode($labels) !!},
                                datasets: [
                                    {
                                        label: 'Berat Badan (kg)',
                                        data: {!! json_encode($bbData) !!},
                                        borderColor: '#10b981',
                                        backgroundColor: gradient1,
                                        borderWidth: 3,
                                        tension: 0.4,
                                        fill: true,
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: '#10b981',
                                        pointBorderWidth: 2.5,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                        yAxisID: 'y'
                                    },
                                    {
                                        label: 'Tinggi Badan (cm)',
                                        data: {!! json_encode($tbData) !!},
                                        borderColor: '#3b82f6',
                                        backgroundColor: gradient2,
                                        borderWidth: 3,
                                        tension: 0.4,
                                        fill: true,
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: '#3b82f6',
                                        pointBorderWidth: 2.5,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { mode: 'index', intersect: false },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: { weight: 'bold', size: 11 } }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1e293b',
                                        titleFont: { weight: 'bold', size: 13 },
                                        bodyFont: { size: 12 },
                                        padding: 14,
                                        cornerRadius: 12,
                                        displayColors: true,
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        boxPadding: 4
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                                    },
                                    y: {
                                        type: 'linear', display: true, position: 'left',
                                        title: { display: true, text: 'Berat (kg)', font: { size: 11, weight: 'bold' }, color: '#10b981' },
                                        grid: { color: 'rgba(0,0,0,0.04)' },
                                        ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                                    },
                                    y1: {
                                        type: 'linear', display: true, position: 'right',
                                        title: { display: true, text: 'Tinggi (cm)', font: { size: 11, weight: 'bold' }, color: '#3b82f6' },
                                        grid: { drawOnChartArea: false },
                                        ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
            @elseif($warga->pemeriksaans->count() == 0)
            <div class="bg-white rounded-3xl p-10 border border-dashed border-slate-200 text-center animate-fade-in-up stagger-2">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <h4 class="font-black text-slate-800 text-lg">Belum Ada Data</h4>
                <p class="text-sm text-slate-500 mt-2">Data pemeriksaan akan muncul setelah Anda berkunjung ke Posyandu.</p>
            </div>
            @endif
        </div>

        <!-- RIGHT: Timeline (takes 2 cols on desktop) -->
        <div class="lg:col-span-2 animate-fade-in-up stagger-3">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="font-black text-slate-800 flex items-center gap-2 text-base">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        Riwayat Kunjungan
                    </h3>
                </div>

                <div class="p-5 sm:p-6">
                    @if($warga->pemeriksaans->count() > 0)
                    <!-- Timeline -->
                    <div class="relative">
                        <!-- Timeline line -->
                        <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-emerald-300 via-teal-200 to-transparent rounded-full"></div>

                        <div class="space-y-6">
                            @foreach($warga->pemeriksaans->sortByDesc('created_at') as $index => $pemeriksaan)
                            <div class="relative flex gap-4">
                                <!-- Timeline dot -->
                                <div class="flex-shrink-0 w-9 h-9 rounded-full border-[3px] border-white shadow-md flex items-center justify-center z-10 {{ $index === 0 ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : 'bg-slate-200' }}">
                                    @if($index === 0)
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 pb-1 {{ $index === 0 ? '' : '' }}">
                                    <div class="bg-{{ $index === 0 ? 'gradient-to-r from-emerald-50 to-teal-50 border-emerald-100' : 'slate-50 border-slate-100' }} rounded-2xl p-4 border">
                                        <div class="flex items-center justify-between mb-2.5">
                                            <span class="text-xs font-black {{ $index === 0 ? 'text-emerald-700' : 'text-slate-600' }}">
                                                {{ \Carbon\Carbon::parse($pemeriksaan->created_at)->translatedFormat('d M Y') }}
                                            </span>
                                            @if($index === 0)
                                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Terbaru</span>
                                            @endif
                                        </div>

                                        @if($warga->balita)
                                            @php
                                                $ageDays = \Carbon\Carbon::parse($warga->balita->tanggal_lahir)->diffInDays(\Carbon\Carbon::parse($pemeriksaan->created_at));
                                                $ageMonths = round($ageDays / 30.44);
                                            @endphp
                                            <p class="text-[10px] text-slate-500 font-bold mb-2">Umur: {{ $ageMonths }} Bulan</p>
                                        @endif

                                        <div class="grid grid-cols-2 gap-2">
                                            @if($pemeriksaan->berat_badan)
                                            <div class="bg-white rounded-xl p-2.5 border border-slate-100/80">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase">Berat</p>
                                                <p class="text-sm font-black text-slate-800">{{ $pemeriksaan->berat_badan }} <span class="text-[10px] font-bold text-slate-400">kg</span></p>
                                            </div>
                                            @endif
                                            @if($pemeriksaan->tinggi_badan)
                                            <div class="bg-white rounded-xl p-2.5 border border-slate-100/80">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase">Tinggi</p>
                                                <p class="text-sm font-black text-slate-800">{{ $pemeriksaan->tinggi_badan }} <span class="text-[10px] font-bold text-slate-400">cm</span></p>
                                            </div>
                                            @endif
                                            @if($pemeriksaan->lingkar_lengan)
                                            <div class="bg-white rounded-xl p-2.5 border border-slate-100/80">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase">LiLA</p>
                                                <p class="text-sm font-black text-slate-800">{{ $pemeriksaan->lingkar_lengan }} <span class="text-[10px] font-bold text-slate-400">cm</span></p>
                                            </div>
                                            @endif
                                            @if($pemeriksaan->tekanan_darah)
                                            <div class="bg-white rounded-xl p-2.5 border border-slate-100/80">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase">Tensi</p>
                                                <p class="text-sm font-black text-slate-800">{{ $pemeriksaan->tekanan_darah }}</p>
                                            </div>
                                            @endif
                                        </div>

                                        @php
                                            $badges = [];
                                            
                                            if ($pemeriksaan->kalkulasiZscore) {
                                                $statuses = [
                                                    'BB/U' => $pemeriksaan->kalkulasiZscore->status_gizi,
                                                    'TB/U' => $pemeriksaan->kalkulasiZscore->status_stunting,
                                                    'BB/TB' => $pemeriksaan->kalkulasiZscore->status_bb_tb,
                                                ];
                                                
                                                foreach ($statuses as $label => $status) {
                                                    if (!$status || $status === '-') continue;
                                                    
                                                    if ($status === 'out_of_reference_range') {
                                                        $badges[] = [
                                                            'display' => "$label: N/A",
                                                            'color' => 'bg-slate-100 text-slate-700',
                                                            'icon' => '❔'
                                                        ];
                                                    } else {
                                                        // Mapping based on exact string from WhoZscoreService
                                                        $isNormal = in_array($status, ['Normal', 'Gizi Baik (Normal)', 'Tinggi']);
                                                        $isKurang = in_array($status, ['Kurang (Underweight)', 'Pendek (Stunted)', 'Gizi Kurang (Wasted)', 'Berisiko Gizi Lebih']);
                                                        
                                                        $color = $isNormal ? 'bg-emerald-100 text-emerald-700' : ($isKurang ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700');
                                                        $icon = $isNormal ? '✅' : ($isKurang ? '⚠️' : '🔴');
                                                        
                                                        $badges[] = [
                                                            'display' => "$label: $status",
                                                            'color' => $color,
                                                            'icon' => $icon
                                                        ];
                                                    }
                                                }
                                            } else {
                                                // Legacy / Fallback
                                                $statusGizi = $pemeriksaan->status_gizi;
                                                if ($statusGizi) {
                                                    if ($statusGizi === 'out_of_reference_range') {
                                                        $badges[] = [
                                                            'display' => 'Di Luar Referensi (N/A)',
                                                            'color' => 'bg-slate-100 text-slate-700',
                                                            'icon' => '❔'
                                                        ];
                                                    } else {
                                                        $lowerStatus = strtolower($statusGizi);
                                                        $isNormal = in_array($lowerStatus, ['normal', 'gizi baik (normal)']);
                                                        $isKurang = in_array($lowerStatus, ['kurang', 'gizi kurang', 'kurang (underweight)']);
                                                        
                                                        $color = $isNormal ? 'bg-emerald-100 text-emerald-700' : ($isKurang ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700');
                                                        $icon = $isNormal ? '✅' : ($isKurang ? '⚠️' : '🔴');
                                                        
                                                        $badges[] = [
                                                            'display' => 'Gizi ' . ucfirst($statusGizi),
                                                            'color' => $color,
                                                            'icon' => $icon
                                                        ];
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if(count($badges) > 0)
                                        <div class="mt-2.5 flex flex-wrap gap-1.5">
                                            @foreach($badges as $badge)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $badge['color'] }}">
                                                {{ $badge['icon'] }} {{ $badge['display'] }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif

                                        @if($pemeriksaan->catatan)
                                        <p class="text-xs text-slate-500 mt-2.5 italic bg-white rounded-lg p-2 border border-slate-100">"{{ $pemeriksaan->catatan }}"</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada riwayat kunjungan.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
