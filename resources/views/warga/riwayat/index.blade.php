@extends('layouts.warga')

@section('title', 'Riwayat Pemeriksaan')

@section('content')

@if($wargas->count() > 1)
<div class="mb-6 overflow-x-auto pb-2">
    <div class="flex space-x-3">
        @foreach($wargas as $w)
        <a href="{{ route('warga.riwayat', ['warga_id' => $w->id]) }}" 
           class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $selectedWarga->id === $w->id ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-white text-slate-500 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200' }}">
            👧👦 {{ $w->nama }}
        </a>
        @endforeach
    </div>
</div>
@endif

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
        @if($pemeriksaans->count() > 0)
        <!-- Timeline -->
        <div class="relative">
            <!-- Timeline line -->
            <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-emerald-300 via-teal-200 to-transparent rounded-full"></div>

            <div class="space-y-6">
                @foreach($pemeriksaans as $index => $pemeriksaan)
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
                    <div class="flex-1 pb-1">
                        <div class="bg-{{ $index === 0 ? 'gradient-to-r from-emerald-50 to-teal-50 border-emerald-100' : 'slate-50 border-slate-100' }} rounded-2xl p-4 border">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs font-black {{ $index === 0 ? 'text-emerald-700' : 'text-slate-600' }}">
                                    {{ \Carbon\Carbon::parse($pemeriksaan->created_at)->translatedFormat('d M Y') }}
                                </span>
                                @if($index === 0)
                                    <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Terbaru</span>
                                @endif
                            </div>

                            @if($selectedWarga->balita)
                                @php
                                    $ageDays = \Carbon\Carbon::parse($selectedWarga->balita->tanggal_lahir)->diffInDays(\Carbon\Carbon::parse($pemeriksaan->created_at));
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

                            @if($pemeriksaan->status_gizi)
                            <div class="mt-2.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                    {{ $pemeriksaan->status_gizi == 'normal' ? 'bg-emerald-100 text-emerald-700' : ($pemeriksaan->status_gizi == 'kurang' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $pemeriksaan->status_gizi == 'normal' ? '✅' : ($pemeriksaan->status_gizi == 'kurang' ? '⚠️' : '🔴') }}
                                    Gizi {{ ucfirst($pemeriksaan->status_gizi) }}
                                </span>
                            </div>
                            @endif

                            @if($pemeriksaan->tindakan || $pemeriksaan->vitamin_a || $pemeriksaan->obat_cacing || $pemeriksaan->imunisasi)
                            <div class="mt-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($pemeriksaan->tindakan)
                                        <span class="inline-block px-2.5 py-1 bg-white border border-slate-200 text-xs font-bold text-slate-600 rounded-lg">{{ $pemeriksaan->tindakan }}</span>
                                    @endif
                                    @if($pemeriksaan->vitamin_a)
                                        <span class="inline-block px-2.5 py-1 bg-red-50 border border-red-200 text-xs font-bold text-red-600 rounded-lg">Vitamin A</span>
                                    @endif
                                    @if($pemeriksaan->obat_cacing)
                                        <span class="inline-block px-2.5 py-1 bg-orange-50 border border-orange-200 text-xs font-bold text-orange-600 rounded-lg">Obat Cacing</span>
                                    @endif
                                </div>
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

@endsection
