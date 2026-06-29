<x-kader-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Materi Penyuluhan') }}
        </h2>
    </x-slot>

<div class="max-w-3xl mx-auto mt-6">
    <!-- Back -->
    <a href="{{ route('kader.penyuluhan.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-emerald-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke Daftar Materi
    </a>

    <!-- Article -->
    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($penyuluhan->gambar)
            <div class="w-full bg-gray-50 flex justify-center py-6 sm:py-8 border-b border-gray-100">
                <img src="{{ Storage::url($penyuluhan->gambar) }}" alt="{{ $penyuluhan->judul }}" class="max-h-[450px] sm:max-h-[600px] lg:max-h-[800px] w-auto object-contain rounded-xl shadow-md">
            </div>
        @endif

        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 font-bold text-xs rounded-lg border border-emerald-100">{{ $penyuluhan->kategori_label }}</span>
                <span class="text-xs text-gray-400 font-medium">{{ $penyuluhan->published_at?->translatedFormat('d F Y') ?? $penyuluhan->created_at->translatedFormat('d F Y') }}</span>
            </div>

            <h1 class="text-2xl font-black text-gray-800 tracking-tight leading-tight mb-6">{{ $penyuluhan->judul }}</h1>

            <div class="flex items-center gap-3 pb-6 mb-6 border-b border-gray-100">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($penyuluhan->penulis->name ?? 'Bidan') }}&background=10b981&color=fff&bold=true&size=60" class="w-10 h-10 rounded-xl shadow-sm" alt="">
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $penyuluhan->penulis->name ?? 'Bidan' }}</p>
                    <p class="text-xs text-emerald-600 font-medium">Bidan Desa</p>
                </div>
            </div>

            @if($penyuluhan->konten)
            <div class="prose prose-sm prose-slate max-w-none leading-relaxed text-gray-700 whitespace-pre-line">
                {{ $penyuluhan->konten }}
            </div>
            @else
            <p class="text-sm text-gray-400 italic">Materi ini hanya berupa gambar/poster tanpa teks tambahan.</p>
            @endif
        </div>
    </article>
</div>
</x-kader-layout>
