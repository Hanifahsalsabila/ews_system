@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="mx-auto max-w-7xl space-y-12 pb-20">
    
    <!-- Top Header & Welcome -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight">Selamat Datang, Admin</h2>
            <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-8 h-px bg-emerald-500"></span>
                E-Posyandu Enterprise Dashboard
            </p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
            <div class="px-4 py-2 bg-emerald-50 rounded-xl">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Sesi Hari Ini</p>
                <p class="text-xs font-bold text-slate-700">{{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <button class="p-3 bg-slate-900 text-white rounded-xl shadow-lg shadow-slate-200 hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
    </div>

    <!-- Stunning Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Card: Total Warga -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Warga</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($metrics['total_warga']) }}</h3>
                <div class="mt-4 flex items-center gap-2 text-emerald-600">
                    <span class="text-[10px] font-black py-1 px-2 bg-emerald-50 rounded-lg">+12% Bulan Ini</span>
                </div>
            </div>
        </div>

        <!-- Card: Balita -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Balita</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($metrics['total_balita']) }}</h3>
                <div class="mt-4 flex items-center gap-2 text-blue-600">
                    <span class="text-[10px] font-black py-1 px-2 bg-blue-50 rounded-lg">Target SIP-2</span>
                </div>
            </div>
        </div>

        <!-- Card: Ibu Hamil -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-rose-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ibu Hamil</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($metrics['total_ibu_hamil']) }}</h3>
                <div class="mt-4 flex items-center gap-2 text-rose-600">
                    <span class="text-[10px] font-black py-1 px-2 bg-rose-50 rounded-lg">Prioritas Monitor</span>
                </div>
            </div>
        </div>

        <!-- Card: Aktif -->
        <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800"></div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sesi Aktif</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">{{ $metrics['sesi_aktif'] }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-1 text-[10px] font-bold text-emerald-500 ring-1 ring-inset ring-emerald-500/20">OPERASIONAL DIGITAL</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.vaksins.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 flex items-center justify-between hover:border-pink-500 hover:shadow-lg hover:shadow-pink-500/10 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-pink-50 rounded-2xl flex items-center justify-center text-pink-500 group-hover:bg-pink-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800">Master Vaksin/Vitamin</h4>
                    <p class="text-xs text-slate-400 font-medium">Kelola Imunisasi</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-300 group-hover:text-pink-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <a href="{{ route('admin.inventaris.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 flex items-center justify-between hover:border-amber-500 hover:shadow-lg hover:shadow-amber-500/10 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800">Inventaris Alat</h4>
                    <p class="text-xs text-slate-400 font-medium">Monitoring Aset</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-300 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <a href="{{ route('admin.sesi-posyandus.index') }}" class="group bg-white rounded-3xl p-6 border border-gray-100 flex items-center justify-between hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/10 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800">Sesi Posyandu</h4>
                    <p class="text-xs text-slate-400 font-medium">Buka Jadwal Baru</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <!-- Visual Dashboard: Charts & Audit -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Distribution Chart -->
        <div class="lg:col-span-2 bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm flex flex-col h-[550px]">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Cakupan Kohort Enterprise</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Distribusi Sasaran Posyandu Tahun Ini</p>
                </div>
                <div class="p-1 px-3 bg-slate-50 rounded-xl border border-gray-100 text-[10px] font-black text-slate-400">UPDATE REAL-TIME</div>
            </div>
            <div class="flex-1 flex items-center justify-center relative scale-110" x-data="kohortChart({{ json_encode($chartData) }})">
                <canvas id="kohortChartMain" class="w-full h-full"></canvas>
                
                <!-- Center Info Overlay -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total</p>
                    <p class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($metrics['total_warga']) }}</p>
                </div>
            </div>
        </div>

        <!-- Smart Audit Timeline -->
        <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Log Aktivitas</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Enterprise Audit Trail</p>
                </div>
            </div>
            
            <div class="flex-1 relative overflow-hidden">
                <div class="absolute left-5 top-2 bottom-6 w-0.5 bg-slate-50"></div>
                
                <div class="space-y-8 relative">
                    @forelse($recent_activities as $audit)
                    <div class="relative pl-12 group">
                        <!-- Indicator -->
                        <div class="absolute left-0 top-1 w-10 h-10 rounded-2xl bg-white border border-slate-50 shadow-sm flex items-center justify-center group-hover:bg-slate-900 transition-all duration-300">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 group-hover:bg-white"></div>
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $audit->user->name ?? 'System' }}</span>
                                <span class="text-[10px] font-bold py-0.5 px-2 bg-slate-100 text-slate-500 rounded-lg uppercase">{{ $audit->event }}</span>
                            </div>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed italic">
                                Mengelola data pada modul {{ class_basename($audit->auditable_type) }}
                            </p>
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-2 block">{{ $audit->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic">Antrean log masih kosong.</p>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('admin.audits.index') }}" class="mt-8 py-4 bg-slate-50 border border-slate-100 text-slate-600 rounded-2xl text-center text-xs font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">
                Lihat Seluruh Log Audit
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kohortChart', (chartData) => ({
            data: chartData,
            colors: ['#10b981', '#3b82f6', '#f59e0b', '#f43f5e'],
            init() {
                const ctx = document.getElementById('kohortChartMain').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: this.data.labels,
                        datasets: [{
                            data: this.data.data,
                            backgroundColor: this.colors,
                            hoverOffset: 30,
                            borderRadius: 20,
                            borderWidth: 8,
                            borderColor: '#fff',
                            spacing: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '80%',
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    font: { family: 'Inter', weight: 'bold', size: 10 },
                                    padding: 30,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 15,
                                bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                                displayColors: true,
                                cornerRadius: 15,
                                caretakerSize: 10
                            }
                        }
                    }
                });
            }
        }))
    });
</script>
@endsection
