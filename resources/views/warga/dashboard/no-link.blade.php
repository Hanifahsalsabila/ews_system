@extends('layouts.warga')

@section('title', 'Akun Belum Terhubung')

@section('content')
<div class="flex flex-col items-center justify-center text-center py-16">
    <div class="w-24 h-24 bg-amber-50 rounded-[2rem] flex items-center justify-center mb-6">
        <span class="text-5xl">🔗</span>
    </div>
    <h2 class="text-xl font-black text-slate-800 mb-2">Akun Belum Terhubung</h2>
    <p class="text-sm text-slate-400 font-medium max-w-xs leading-relaxed mb-6">
        Akun Anda belum terhubung ke data Warga/Balita. Silakan hubungi Kader atau Admin Posyandu terdekat untuk menautkan NIK Anda.
    </p>
    <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 max-w-xs">
        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Langkah selanjutnya:</p>
        <ol class="text-xs text-slate-600 font-medium text-left space-y-1.5">
            <li>1. Kunjungi Posyandu terdekat</li>
            <li>2. Minta Kader menautkan NIK Anda</li>
            <li>3. Setelah tertaut, buka kembali halaman ini</li>
        </ol>
    </div>
</div>
@endsection
