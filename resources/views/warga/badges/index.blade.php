@extends('layouts.warga')

@section('title', 'Lencana')

@section('content')

<div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-[2rem] p-6 text-white shadow-xl shadow-amber-500/20 relative overflow-hidden mb-2">
    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full"></div>
    <div class="relative z-10">
        <p class="text-amber-100 text-xs font-bold uppercase tracking-widest mb-1">Koleksi Lencana</p>
        <h2 class="text-2xl font-black tracking-tight">🏆 {{ count($badges) }} Lencana Diraih</h2>
        <p class="text-amber-100 text-xs font-medium mt-1">Rajin hadir untuk mendapatkan lebih banyak!</p>
    </div>
</div>

{{-- Earned Badges --}}
@if(count($badges) > 0)
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">✅ Sudah Didapatkan</h3>
    <div class="grid grid-cols-1 gap-3">
        @foreach($badges as $badge)
        <div class="flex items-center gap-4 p-4 bg-amber-50/50 rounded-2xl border border-amber-100/50">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
                <span class="text-3xl">{{ $badge['icon'] }}</span>
            </div>
            <div>
                <p class="text-sm font-black text-slate-800">{{ $badge['name'] }}</p>
                <p class="text-[10px] text-slate-400 font-medium">{{ $badge['desc'] }}</p>
                <p class="text-[9px] text-amber-500 font-bold mt-1">Diraih: {{ \Carbon\Carbon::parse($badge['earned_at'])->translatedFormat('d M Y') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Locked Badges --}}
<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-4">🔒 Belum Didapatkan</h3>
    <div class="grid grid-cols-1 gap-3">
        @foreach($allBadges as $badge)
            @if(!in_array($badge['name'], $earnedNames))
            <div class="flex items-center gap-4 p-4 bg-slate-50/50 rounded-2xl border border-slate-100/50 opacity-60">
                <div class="w-14 h-14 rounded-2xl bg-slate-200 flex items-center justify-center">
                    <span class="text-3xl grayscale">{{ $badge['icon'] }}</span>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-600">{{ $badge['name'] }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $badge['desc'] }}</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1">Syarat: {{ $badge['streak'] }} bulan berturut-turut</p>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>

@endsection
