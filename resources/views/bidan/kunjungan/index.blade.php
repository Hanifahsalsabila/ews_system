<x-bidan-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Kunjungan Rumah & Intervensi') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Early Warning System — Peringatan Dini Gizi</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Statistik Ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Dijadwalkan</p>
                    <p class="text-3xl font-black text-blue-600">{{ $stats['dijadwalkan'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Selesai</p>
                    <p class="text-3xl font-black text-emerald-600">{{ $stats['selesai'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-rose-200">
                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">Prioritas Kritis</p>
                    <p class="text-3xl font-black text-rose-600">{{ $stats['kritis'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-amber-200">
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-1">Prioritas Tinggi</p>
                    <p class="text-3xl font-black text-amber-600">{{ $stats['tinggi'] }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-wrap items-center gap-3">
                <form method="GET" class="flex flex-wrap gap-3 items-center w-full">
                    <select name="status" class="text-sm font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditunda" {{ request('status') == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                    <select name="prioritas" class="text-sm font-bold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Semua Prioritas</option>
                        <option value="kritis" {{ request('prioritas') == 'kritis' ? 'selected' : '' }}>🔴 Kritis</option>
                        <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>🟠 Tinggi</option>
                        <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>🟡 Sedang</option>
                        <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>🟢 Rendah</option>
                    </select>
                    <button type="submit" class="bg-emerald-500 text-white text-sm font-bold px-5 py-2 rounded-xl hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-500/20">Filter</button>
                    <a href="{{ route('bidan.kunjungan.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 px-3 py-2">Reset</a>
                </form>
            </div>

            {{-- Tabel Kunjungan --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Warga / Balita</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Prioritas</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rencana</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Petugas</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kunjungans as $k)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $k->warga->nama ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $k->warga->region->nama ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $tipeLabels = ['2t' => '2T (BB Turun)', 'stunting' => 'Stunting', 'gizi_buruk' => 'Gizi Buruk', 'kek' => 'KEK', 'lainnya' => 'Lainnya'];
                                    @endphp
                                    <span class="text-xs font-bold text-slate-600">{{ $tipeLabels[$k->tipe] ?? $k->tipe }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $pColor = match($k->prioritas) {
                                            'kritis' => 'bg-rose-50 text-rose-600 border-rose-200',
                                            'tinggi' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'sedang' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            default => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                        };
                                    @endphp
                                    <span class="text-[10px] font-black px-2.5 py-1 rounded-lg border {{ $pColor }} uppercase">{{ $k->prioritas }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">{{ $k->tanggal_rencana ? $k->tanggal_rencana->translatedFormat('d M Y') : '-' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $sColor = match($k->status) {
                                            'selesai' => 'bg-emerald-50 text-emerald-600',
                                            'dijadwalkan' => 'bg-blue-50 text-blue-600',
                                            'ditunda' => 'bg-amber-50 text-amber-600',
                                            default => 'bg-slate-100 text-slate-500',
                                        };
                                    @endphp
                                    <span class="text-[10px] font-black px-2.5 py-1 rounded-lg {{ $sColor }} uppercase">{{ $k->status }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">{{ $k->bidan->name ?? 'Belum diklaim' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if(!$k->bidan_id)
                                        <form method="POST" action="{{ route('bidan.kunjungan.claim', $k) }}">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition-colors shadow-sm">Klaim</button>
                                        </form>
                                        @endif
                                        @if($k->status !== 'selesai')
                                        <a href="{{ route('bidan.kunjungan.edit', $k) }}" class="text-[10px] font-bold bg-blue-500 text-white px-3 py-1.5 rounded-lg hover:bg-blue-600 transition-colors shadow-sm">Lapor</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-3xl mb-2">✅</p>
                                    <p class="text-sm text-slate-400 font-bold">Tidak ada kunjungan rumah yang terjadwal. Sistem EWS belum mendeteksi anomali.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-50">
                    {{ $kunjungans->links() }}
                </div>
            </div>

            {{-- Catatan EWS --}}
            @if($kunjungans->where('catatan_sebelum', '!=', null)->count() > 0)
            <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100">
                <h4 class="text-sm font-black text-rose-700 uppercase tracking-tight mb-3">⚠️ Peringatan Dini Otomatis (EWS)</h4>
                <div class="space-y-3">
                    @foreach($kunjungans->where('catatan_sebelum', '!=', null)->take(5) as $alert)
                    <div class="bg-white rounded-xl p-4 border border-rose-100/50">
                        <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $alert->catatan_sebelum }}</p>
                        <p class="text-[10px] text-rose-400 font-bold mt-2">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-bidan-layout>
