<x-kader-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Buku Register Warga</h1>
            <p class="text-slate-500 mt-2 font-medium">Lihat daftar warga yang terdaftar di wilayah tugas Anda. Data bersifat Read-Only.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <!-- Search bar -->
        <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('kader.warga.index') }}" method="GET" class="relative max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK atau Nama Warga..." class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">NIK & Nama</th>
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Wilayah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wargas as $warga)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800">{{ $warga->nama }}</div>
                                <div class="text-sm text-slate-500 mt-1 font-mono">{{ $warga->nik }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($warga->balita)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-600 border border-teal-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div> Balita
                                    </span>
                                @elseif($warga->ibuHamil)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Ibu Hamil
                                    </span>
                                @elseif($warga->lansia)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div> Lansia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div> WUS/PUS
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-sm font-medium text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                                    Dusun {{ $warga->region->dusun_name ?? '-' }} (RW {{ str_pad($warga->region->rw_name ?? 0, 2, '0', STR_PAD_LEFT) }} / RT {{ str_pad($warga->region->rt_name ?? 0, 2, '0', STR_PAD_LEFT) }})
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500 font-medium">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                @if(request('search'))
                                    Tidak ada data warga yang cocok dengan pencarian.
                                @else
                                    Belum ada data warga terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($wargas->hasPages())
        <div class="p-4 sm:p-6 border-t border-slate-100">
            {{ $wargas->links() }}
        </div>
        @endif
    </div>
</x-kader-layout>
