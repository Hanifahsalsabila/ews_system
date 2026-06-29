<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Materi Penyuluhan') }}
        </h2>
    </x-slot>

<div class="max-w-7xl mx-auto mt-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Materi Penyuluhan</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola materi edukasi kesehatan untuk warga dan kader.</p>
        </div>
        <a href="{{ route('bidan.penyuluhan.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-sm rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Materi Baru
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Materi</th>
                        <th class="px-4 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider text-center">Status</th>
                        <th class="px-4 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider text-center">Tanggal</th>
                        <th class="px-4 py-4 font-bold text-gray-500 text-xs uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($penyuluhans as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($p->gambar)
                                    <img src="{{ Storage::url($p->gambar) }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate max-w-xs">{{ $p->judul }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5">{{ $p->excerpt }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-600 font-bold text-[11px] rounded-lg border border-blue-100">{{ $p->kategori_label }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="{{ route('bidan.penyuluhan.toggle-publish', $p) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-bold transition-colors {{ $p->is_published ? 'bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100' }}">
                                    {{ $p->is_published ? '✓ Published' : '◷ Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-4 text-center text-xs text-gray-500">
                            {{ $p->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('bidan.penyuluhan.edit', $p) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form method="POST" action="{{ route('bidan.penyuluhan.destroy', $p) }}" onsubmit="return confirm('Hapus materi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <p class="text-sm text-gray-400 font-bold">Belum ada materi penyuluhan.</p>
                            <a href="{{ route('bidan.penyuluhan.create') }}" class="text-emerald-500 font-bold text-sm mt-1 inline-block hover:underline">+ Buat materi pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penyuluhans->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $penyuluhans->links() }}
        </div>
        @endif
    </div>
</div>
</x-bidan-layout>
