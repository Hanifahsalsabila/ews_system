@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem (Audit)')

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Log Aktivitas (Audits)</h2>
            <p class="text-gray-500 text-sm mt-1">Rekaman jejak atas semua perubahan data pada sistem yang mendukung Auditable.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.audits.index') }}" class="flex items-center gap-3">
            <select name="category" onchange="this.form.submit()" class="border-gray-200 rounded-xl text-sm focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-slate-600 bg-slate-50 pr-10">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ class_basename($cat) }}
                    </option>
                @endforeach
            </select>
            @if(request('category'))
                <a href="{{ route('admin.audits.index') }}" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-4 py-3 font-bold text-gray-500 tracking-wider">Kejadian</th>
                        <th class="px-4 py-3 font-bold text-gray-500 tracking-wider">Aksi Oleh</th>
                        <th class="px-4 py-3 font-bold text-gray-500 tracking-wider">Model / ID</th>
                        <th class="px-4 py-3 font-bold text-gray-500 tracking-wider w-1/2">Pembaruan</th>
                        <th class="px-4 py-3 font-bold text-gray-500 tracking-wider text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($audits as $audit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-4 align-top">
                            @if($audit->event === 'created')
                                <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 font-bold text-xs uppercase">Created</span>
                            @elseif($audit->event === 'updated')
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 font-bold text-xs uppercase">Updated</span>
                            @elseif($audit->event === 'deleted')
                                <span class="px-2 py-1 rounded bg-rose-100 text-rose-700 font-bold text-xs uppercase">Deleted</span>
                            @else
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 font-bold text-xs uppercase">{{ $audit->event }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-top">
                            @if($audit->user)
                                <p class="font-bold text-gray-800">{{ $audit->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $audit->user->email }}</p>
                            @else
                                <p class="italic text-gray-400">System</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-top">
                            <p class="font-semibold text-slate-700">{{ class_basename($audit->auditable_type) }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">ID: {{ $audit->auditable_id }}</p>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if(!empty($audit->old_values))
                                <div class="bg-rose-50 border border-rose-100 rounded-lg p-2">
                                    <p class="text-[10px] font-bold text-rose-600 uppercase mb-1 border-b border-rose-100 pb-1">Nilai Lama</p>
                                    <ul class="text-xs font-mono text-gray-600 break-all space-y-1 mt-1">
                                        @foreach($audit->old_values as $key => $val)
                                            @if($key !== 'password' && $key !== 'remember_token')
                                            <li><span class="text-rose-500 font-semibold">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                
                                @if(!empty($audit->new_values))
                                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2">
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase mb-1 border-b border-emerald-100 pb-1">Nilai Baru</p>
                                    <ul class="text-xs font-mono text-gray-600 break-all space-y-1 mt-1">
                                        @foreach($audit->new_values as $key => $val)
                                            @if($key !== 'password' && $key !== 'remember_token')
                                            <li><span class="text-emerald-500 font-semibold">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 align-top text-right text-xs text-gray-500 tabular-nums">
                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada rekaman audit yang tersimpan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($audits->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $audits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
