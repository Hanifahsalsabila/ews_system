<x-kader-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Materi Penyuluhan') }}
        </h2>
    </x-slot>

<div class="max-w-7xl mx-auto mt-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📚 Materi Penyuluhan</h2>
            <p class="text-gray-500 text-sm mt-1">Materi edukasi kesehatan dari Bidan untuk dibagikan kepada warga.</p>
        </div>
        <!-- Category Filter -->
        <form method="GET" action="{{ route('kader.penyuluhan.index') }}" class="flex items-center gap-2">
            <select name="kategori" onchange="this.form.submit()" class="border-gray-200 rounded-xl text-sm focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-slate-600 bg-slate-50 pr-10">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $key => $label)
                    <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if(request('kategori'))
                <a href="{{ route('kader.penyuluhan.index') }}" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Articles Grid -->
    @if($penyuluhans->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <p class="text-sm text-gray-400 font-bold">Belum ada materi penyuluhan yang dipublikasikan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($penyuluhans as $p)
            <a href="{{ route('kader.penyuluhan.show', $p) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all group">
                @if($p->gambar)
                    <div class="aspect-video overflow-hidden">
                        <img src="{{ Storage::url($p->gambar) }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @else
                    <div class="aspect-video bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center">
                        <svg class="w-12 h-12 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 font-bold text-[10px] rounded-md border border-emerald-100">{{ $p->kategori_label }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">{{ $p->published_at?->diffForHumans() ?? $p->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800 leading-snug group-hover:text-emerald-600 transition-colors line-clamp-2">{{ $p->judul }}</h4>
                    @if($p->konten)
                        <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $p->excerpt }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-50">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-500">{{ $p->penulis->name ?? 'Bidan' }}</span>
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
</div>
</x-kader-layout>
