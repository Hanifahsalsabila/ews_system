@extends('layouts.warga')

@section('title', $penyuluhan->judul)

@section('content')

<!-- Back Button -->
<a href="{{ route('warga.edukasi.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    Kembali ke Edukasi
</a>

<!-- Article Card -->
<article class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    @if($penyuluhan->gambar)
        <div class="w-full bg-slate-50 flex justify-center py-6 sm:py-8 border-b border-slate-100">
            <img src="{{ Storage::url($penyuluhan->gambar) }}" alt="{{ $penyuluhan->judul }}" class="max-h-[450px] sm:max-h-[600px] lg:max-h-[800px] w-auto object-contain rounded-xl shadow-md">
        </div>
    @endif

    <div class="p-6 sm:p-8">
        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="px-3 py-1 bg-blue-50 text-blue-600 font-bold text-xs rounded-lg border border-blue-100">{{ $penyuluhan->kategori_label }}</span>
            <span class="text-xs text-slate-400 font-medium">{{ $penyuluhan->published_at?->translatedFormat('d F Y') ?? $penyuluhan->created_at->translatedFormat('d F Y') }}</span>
        </div>

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight leading-tight mb-6">{{ $penyuluhan->judul }}</h1>

        <!-- Author -->
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($penyuluhan->penulis->name ?? 'Bidan') }}&background=10b981&color=fff&bold=true&size=60" class="w-10 h-10 rounded-xl shadow-sm" alt="">
            <div>
                <p class="text-sm font-bold text-slate-800">{{ $penyuluhan->penulis->name ?? 'Bidan' }}</p>
                <p class="text-xs text-emerald-600 font-medium">Puskesmas</p>
            </div>
        </div>

        <!-- Content -->
        @if($penyuluhan->konten)
        <div class="prose prose-sm prose-slate max-w-none leading-relaxed text-slate-700 whitespace-pre-line">
            {{ $penyuluhan->konten }}
        </div>
        @else
        <p class="text-sm text-slate-400 italic">Materi ini hanya berupa gambar/poster tanpa teks tambahan.</p>
        @endif
    </div>
</article>

<!-- Related Articles -->
@if($related->count() > 0)
<div class="mt-8">
    <h3 class="text-lg font-black text-slate-800 mb-4">Materi Terkait</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($related as $r)
        <a href="{{ route('warga.edukasi.show', $r) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all group">
            @if($r->gambar)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ Storage::url($r->gambar) }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @else
                <div class="aspect-video bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            @endif
            <div class="p-4">
                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-bold text-[10px] rounded-md border border-blue-100">{{ $r->kategori_label }}</span>
                <h4 class="text-sm font-bold text-slate-800 mt-2 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $r->judul }}</h4>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection
