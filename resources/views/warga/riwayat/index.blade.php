@extends('layouts.warga')

@section('title', 'Riwayat Pemeriksaan')

@section('content')

<div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
    <h3 class="text-lg font-black text-slate-800 tracking-tight mb-1">Riwayat Pemeriksaan</h3>
    <p class="text-xs text-slate-400 font-bold mb-5">{{ $warga->nama }}</p>

    @forelse($pemeriksaans as $p)
    <div class="flex items-start gap-4 py-4 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
        <div class="w-11 h-11 rounded-xl {{ $p->is_validated ? 'bg-emerald-50 text-emerald-500' : 'bg-amber-50 text-amber-500' }} flex items-center justify-center text-sm font-black shrink-0">
            {{ $p->is_validated ? '✓' : '⏳' }}
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d F Y') }}</p>
                <span class="text-[9px] font-black px-2 py-0.5 rounded-lg {{ $p->is_validated ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                    {{ $p->is_validated ? 'Tervalidasi' : 'Menunggu' }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                @if($p->berat_badan)
                <div class="bg-blue-50/50 rounded-lg px-2.5 py-1.5">
                    <p class="text-[9px] font-bold text-blue-400">BB</p>
                    <p class="text-xs font-black text-blue-600">{{ $p->berat_badan }} kg</p>
                </div>
                @endif
                @if($p->tinggi_badan)
                <div class="bg-emerald-50/50 rounded-lg px-2.5 py-1.5">
                    <p class="text-[9px] font-bold text-emerald-400">TB</p>
                    <p class="text-xs font-black text-emerald-600">{{ $p->tinggi_badan }} cm</p>
                </div>
                @endif
                @if($p->lingkar_kepala)
                <div class="bg-purple-50/50 rounded-lg px-2.5 py-1.5">
                    <p class="text-[9px] font-bold text-purple-400">LK</p>
                    <p class="text-xs font-black text-purple-600">{{ $p->lingkar_kepala }} cm</p>
                </div>
                @endif
                @if($p->lingkar_lengan)
                <div class="bg-amber-50/50 rounded-lg px-2.5 py-1.5">
                    <p class="text-[9px] font-bold text-amber-400">LILA</p>
                    <p class="text-xs font-black text-amber-600">{{ $p->lingkar_lengan }} cm</p>
                </div>
                @endif
            </div>
            @if($p->tindakan)
            <div class="mt-2 bg-teal-50/50 rounded-lg px-2.5 py-1.5">
                <p class="text-[9px] font-bold text-teal-400">Tindakan</p>
                <p class="text-[10px] font-medium text-teal-700">{{ $p->tindakan }}</p>
            </div>
            @endif
            @if($p->catatan)
            <p class="text-[10px] text-slate-400 font-medium mt-2 italic">📝 {{ $p->catatan }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-10">
        <p class="text-4xl mb-3">📋</p>
        <p class="text-sm text-slate-400 font-bold">Belum ada riwayat pemeriksaan.</p>
    </div>
    @endforelse

    <div class="mt-4">
        {{ $pemeriksaans->links() }}
    </div>
</div>

@endsection
