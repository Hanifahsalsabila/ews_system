@extends('layouts.warga')

@section('title', 'Beranda')

@section('content')

{{-- Badge Pop-up Modal (Gamifikasi) --}}
@if($newBadge)
<div x-data="{ showBadge: true }" x-show="showBadge" x-transition class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-init="setTimeout(() => showBadge = false, 5000)">
    <div class="bg-white rounded-[2.5rem] p-8 mx-6 text-center shadow-2xl max-w-sm relative overflow-hidden badge-pop">
        <!-- Confetti Particles -->
        <div class="confetti-particle top-4 left-8 text-2xl" style="animation-delay: 0.1s">🎉</div>
        <div class="confetti-particle top-8 right-12 text-xl" style="animation-delay: 0.3s">✨</div>
        <div class="confetti-particle top-2 left-1/2 text-lg" style="animation-delay: 0.5s">🎊</div>
        
        <div class="text-6xl mb-4">{{ $newBadge['icon'] }}</div>
        <h3 class="text-2xl font-black text-slate-800 mb-2">Selamat! 🎉</h3>
        <p class="text-sm text-slate-500 font-medium mb-1">Anda mendapatkan lencana baru:</p>
        <p class="text-lg font-black text-emerald-600 mb-2">{{ $newBadge['name'] }}</p>
        <p class="text-xs text-slate-400">{{ $newBadge['desc'] }}</p>
        <button @click="showBadge = false" class="mt-6 w-full py-3 bg-emerald-500 text-white font-bold rounded-2xl hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/30">
            Terima Kasih! 🙏
        </button>
    </div>
</div>
@endif

{{-- Welcome Card --}}
<div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[2rem] p-6 text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden">
    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full"></div>
    <div class="absolute -right-2 -bottom-8 w-24 h-24 bg-white/5 rounded-full"></div>
    <div class="relative z-10">
        <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest mb-1">Assalamualaikum 👋</p>
        <h2 class="text-2xl font-black tracking-tight">{{ $warga->nama }}</h2>
        <div class="flex items-center gap-2 mt-3">
            <span class="text-[10px] font-bold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">{{ $warga->region->nama ?? '-' }}</span>
            @if($streakCount > 0)
            <span class="text-[10px] font-bold bg-amber-400/20 backdrop-blur-sm px-3 py-1 rounded-full text-amber-100">🔥 {{ $streakCount }} bulan berturut-turut</span>
            @endif
        </div>
    </div>
</div>

{{-- Status Gizi Card --}}
@if($warga->balita && $latestZScore)
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Status Gizi Anak</h3>
        <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $warga->balita->umur_bulan }} Bulan</span>
    </div>

    <div class="grid grid-cols-3 gap-3">
        {{-- BB/U --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-2xl p-4 text-center">
            <p class="text-[10px] font-black text-blue-400 uppercase tracking-wider mb-1">BB/U</p>
            <p class="text-lg font-black text-blue-600">{{ number_format($latestZScore->zscore_bb_u ?? 0, 1) }}</p>
            <p class="text-[9px] font-bold text-blue-400 mt-1">Z-Score</p>
        </div>
        {{-- TB/U --}}
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-4 text-center">
            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-wider mb-1">TB/U</p>
            <p class="text-lg font-black text-emerald-600">{{ number_format($latestZScore->zscore_tb_u ?? 0, 1) }}</p>
            <p class="text-[9px] font-bold text-emerald-400 mt-1">Z-Score</p>
        </div>
        {{-- BB/TB --}}
        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-2xl p-4 text-center">
            <p class="text-[10px] font-black text-amber-400 uppercase tracking-wider mb-1">BB/TB</p>
            <p class="text-lg font-black text-amber-600">{{ number_format($latestZScore->zscore_bb_tb ?? 0, 1) }}</p>
            <p class="text-[9px] font-bold text-amber-400 mt-1">Z-Score</p>
        </div>
    </div>

    {{-- Status Indicators --}}
    <div class="mt-4 flex items-center gap-2 flex-wrap">
        @php
            $statusStunting = strtolower($latestZScore->status_stunting ?? '');
            $statusGizi = strtolower($latestZScore->status_gizi ?? '');
            $stuntingColor = match(true) {
                str_contains($statusStunting, 'normal') => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                str_contains($statusStunting, 'pendek') => 'bg-amber-50 text-amber-600 border-amber-200',
                default => 'bg-rose-50 text-rose-600 border-rose-200',
            };
            $giziColor = match(true) {
                str_contains($statusGizi, 'baik') || str_contains($statusGizi, 'normal') => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                str_contains($statusGizi, 'kurang') => 'bg-amber-50 text-amber-600 border-amber-200',
                default => 'bg-rose-50 text-rose-600 border-rose-200',
            };
        @endphp
        <span class="text-[10px] font-black px-3 py-1.5 rounded-xl border {{ $stuntingColor }}">{{ $latestZScore->status_stunting ?? '-' }}</span>
        <span class="text-[10px] font-black px-3 py-1.5 rounded-xl border {{ $giziColor }}">{{ $latestZScore->status_gizi ?? '-' }}</span>
    </div>
</div>
@endif

{{-- KMS Chart (Kurva Pertumbuhan) --}}
@if($warga->balita && count($kmsData['labels']) > 0)
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100" x-data="kmsChart({{ json_encode($kmsData) }})">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Kurva KMS Digital</h3>
            <p class="text-[10px] text-slate-400 font-bold mt-0.5">Kartu Menuju Sehat</p>
        </div>
        <div class="flex gap-1">
            <button @click="mode = 'bb'" :class="mode === 'bb' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500'" class="text-[10px] font-bold px-3 py-1.5 rounded-xl transition-colors">Berat</button>
            <button @click="mode = 'tb'" :class="mode === 'tb' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'" class="text-[10px] font-bold px-3 py-1.5 rounded-xl transition-colors">Tinggi</button>
        </div>
    </div>
    <div class="h-56">
        <canvas id="kmsChartCanvas"></canvas>
    </div>
</div>
@endif

{{-- Riwayat Imunisasi / Tindakan --}}
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">Riwayat Layanan & Imunisasi</h3>
    
    @if($imunisasiLog->count() > 0)
    <div class="space-y-3">
        @foreach($imunisasiLog->take(5) as $log)
        <div class="flex items-start gap-3 p-3 bg-emerald-50/50 rounded-xl border border-emerald-100/50">
            <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-black shrink-0 shadow-sm">✓</div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-700 truncate">{{ $log->tindakan }}</p>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-6">
        <p class="text-3xl mb-2">💉</p>
        <p class="text-xs text-slate-400 font-bold">Belum ada riwayat tindakan/imunisasi tercatat.</p>
    </div>
    @endif

    {{-- Imunisasi Progress Bar --}}
    @if($warga->balita)
    <div class="mt-5 pt-5 border-t border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Kelengkapan Imunisasi Dasar</span>
            <span class="text-[10px] font-black text-emerald-600">{{ $imunisasiLog->count() }}/11</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5">
            <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-2.5 rounded-full transition-all duration-700" style="width: {{ min(100, ($imunisasiLog->count() / 11) * 100) }}%"></div>
        </div>
    </div>
    @endif
</div>

{{-- Lencana / Badges --}}
@if(count($badges) > 0)
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">🏆 Lencana Anda</h3>
    <div class="grid grid-cols-3 gap-3">
        @foreach($badges as $badge)
        <div class="text-center p-3 bg-amber-50/50 rounded-2xl border border-amber-100/50 hover:shadow-md transition-shadow">
            <p class="text-3xl mb-1">{{ $badge['icon'] }}</p>
            <p class="text-[10px] font-black text-slate-700">{{ $badge['name'] }}</p>
            <p class="text-[9px] text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($badge['earned_at'])->translatedFormat('d M Y') }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Rekomendasi MPASI --}}
@if($mpasi)
<div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-[2rem] p-6 shadow-sm border border-orange-100/50">
    <div class="flex items-center gap-2 mb-3">
        <span class="text-2xl">🍲</span>
        <div>
            <h3 class="text-sm font-black text-slate-800">Rekomendasi MPASI Cerdas</h3>
            <p class="text-[10px] text-amber-600 font-bold">{{ $mpasi['kategori'] }}</p>
        </div>
    </div>
    
    <p class="text-xs text-slate-600 font-medium mb-4 leading-relaxed">{{ $mpasi['pesan'] }}</p>

    @if(count($mpasi['resep']) > 0)
    <div class="space-y-2">
        @foreach($mpasi['resep'] as $resep)
        <div class="bg-white rounded-xl p-3 border border-orange-100/50">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-slate-700">{{ $resep['nama'] }}</p>
                <span class="text-[9px] font-black text-orange-500 bg-orange-50 px-2 py-0.5 rounded-lg">{{ $resep['kalori'] }}</span>
            </div>
            <p class="text-[10px] text-slate-400 font-medium">{{ $resep['bahan'] }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif

{{-- Riwayat Pemeriksaan Terakhir --}}
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">📋 Pemeriksaan Terakhir</h3>
    
    @forelse($pemeriksaans->take(5) as $p)
    <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $p->is_validated ? 'bg-emerald-50 text-emerald-500' : 'bg-amber-50 text-amber-500' }} flex items-center justify-center text-xs font-black shrink-0">
            {{ $p->is_validated ? '✓' : '⏳' }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d F Y') }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                BB: {{ $p->berat_badan ?? '-' }} kg · TB: {{ $p->tinggi_badan ?? '-' }} cm
            </p>
        </div>
        <span class="text-[9px] font-black px-2 py-1 rounded-lg {{ $p->is_validated ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
            {{ $p->is_validated ? 'Valid' : 'Pending' }}
        </span>
    </div>
    @empty
    <p class="text-xs text-slate-400 font-medium text-center py-6">Belum ada data pemeriksaan.</p>
    @endforelse
</div>

{{-- Chart.js Script --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kmsChart', (kmsData) => ({
        mode: 'bb',
        chart: null,
        data: kmsData,
        init() {
            this.renderChart();
            this.$watch('mode', () => this.renderChart());
        },
        renderChart() {
            const ctx = document.getElementById('kmsChartCanvas').getContext('2d');
            if (this.chart) this.chart.destroy();
            
            const isBB = this.mode === 'bb';
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.data.labels,
                    datasets: [{
                        label: isBB ? 'Berat Badan (kg)' : 'Tinggi Badan (cm)',
                        data: isBB ? this.data.bb : this.data.tb,
                        borderColor: isBB ? '#3b82f6' : '#10b981',
                        backgroundColor: isBB ? 'rgba(59,130,246,0.1)' : 'rgba(16,185,129,0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: isBB ? '#3b82f6' : '#10b981',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            bodyFont: { family: 'Plus Jakarta Sans', weight: 'bold', size: 13 },
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 9, weight: 'bold' }, color: '#94a3b8' }
                        },
                        y: {
                            grid: { color: 'rgba(0,0,0,0.03)' },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }
    }));
});
</script>
@endsection
