@extends('layouts.warga')

@section('title', 'Edukasi Kesehatan')

@section('content')

<!-- Header -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-blue-500/30">
            📚
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight leading-none">Edukasi Kesehatan</h3>
            <p class="text-sm text-slate-500 font-medium mt-1">Materi penyuluhan dari Bidan untuk keluarga Anda</p>
        </div>
    </div>
</div>

<!-- Category Filter -->
<div class="mb-6 overflow-x-auto pb-2">
    <div class="flex space-x-2">
        <a href="{{ route('warga.edukasi.index') }}" class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold transition-all {{ !request('kategori') ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'bg-white text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600' }}">
            Semua
        </a>
        @foreach($kategoris as $key => $label)
        <a href="{{ route('warga.edukasi.index', ['kategori' => $key]) }}" class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold transition-all {{ request('kategori') === $key ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'bg-white text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

<!-- Articles Grid -->
@if($penyuluhans->isEmpty())
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">📖</div>
        <h4 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Materi</h4>
        <p class="text-sm text-slate-500">Materi edukasi kesehatan akan ditampilkan di sini setelah dipublikasikan oleh Bidan.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($penyuluhans as $p)
        <a href="{{ route('warga.edukasi.show', $p) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-blue-900/5 transition-all group">
            @if($p->gambar)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ Storage::url($p->gambar) }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @else
                <div class="aspect-video bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            @endif
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-bold text-[10px] rounded-md border border-blue-100">{{ $p->kategori_label }}</span>
                    <span class="text-[10px] text-slate-400 font-medium">{{ $p->published_at?->diffForHumans() ?? $p->created_at->diffForHumans() }}</span>
                </div>
                <h4 class="text-sm font-black text-slate-800 leading-snug group-hover:text-blue-600 transition-colors line-clamp-2">{{ $p->judul }}</h4>
                @if($p->konten)
                    <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">{{ $p->excerpt }}</p>
                @endif
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-50">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500">{{ $p->penulis->name ?? 'Bidan' }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if($penyuluhans->hasPages())
    <div class="mt-6">
        {{ $penyuluhans->links() }}
    </div>
    @endif
@endif

@endsection
