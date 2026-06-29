@extends('layouts.warga')

@section('content')
    <!-- ===== HERO GREETING ===== -->
    <div class="animate-fade-in-up mb-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-emerald-600 mb-1">👋 Selamat datang,</p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight leading-tight">{{ auth()->user()->name }}</h2>
                <p class="text-slate-500 mt-2 text-sm sm:text-base font-medium max-w-lg">Pantau kesehatan keluarga Anda dan tunjukkan QR Code saat kunjungan Posyandu.</p>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-2xl border border-slate-200 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-sm font-bold text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- ===== QUICK STATS ===== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8 animate-fade-in-up stagger-1">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $wargas->count() }}</p>
            <p class="text-xs font-bold text-slate-500 mt-0.5">Anggota Keluarga</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $wargas->sum(fn($w) => $w->pemeriksaans->count()) }}</p>
            <p class="text-xs font-bold text-slate-500 mt-0.5">Total Kunjungan</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-800">{{ $wargas->filter(fn($w) => $w->balita)->count() }}</p>
            <p class="text-xs font-bold text-slate-500 mt-0.5">Balita</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            @php
                $lastVisit = $wargas->flatMap(fn($w) => $w->pemeriksaans)->sortByDesc('created_at')->first();
            @endphp
            <p class="text-sm font-black text-slate-800 leading-tight">{{ $lastVisit ? \Carbon\Carbon::parse($lastVisit->created_at)->diffForHumans() : '-' }}</p>
            <p class="text-xs font-bold text-slate-500 mt-0.5">Kunjungan Terakhir</p>
        </div>
    </div>

    <!-- ===== FAMILY MEMBERS SECTION ===== -->
    <div class="mb-4 animate-fade-in-up stagger-2">
        <h3 class="text-lg font-black text-slate-800 tracking-tight">Anggota Keluarga</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
        @forelse($wargas as $index => $warga)
            @php
                $lastPemeriksaan = $warga->pemeriksaans->last();
                $cohortColor = 'from-slate-500 to-slate-600';
                $cohortBg = 'bg-slate-50 text-slate-600 border-slate-200';
                $cohortLabel = 'Umum / WUS';
                $cohortIcon = '👤';
                $shadowClass = 'shadow-slate-500/20';
                if ($warga->balita) {
                    $cohortColor = 'from-teal-500 to-cyan-500';
                    $cohortBg = 'bg-teal-50 text-teal-700 border-teal-200';
                    $cohortLabel = 'Balita';
                    $cohortIcon = '👶';
                    $shadowClass = 'shadow-teal-500/20';
                } elseif ($warga->ibuHamil) {
                    $cohortColor = 'from-rose-500 to-pink-500';
                    $cohortBg = 'bg-rose-50 text-rose-700 border-rose-200';
                    $cohortLabel = 'Ibu Hamil';
                    $cohortIcon = '🤰';
                    $shadowClass = 'shadow-rose-500/20';
                } elseif ($warga->lansia) {
                    $cohortColor = 'from-amber-500 to-orange-500';
                    $cohortBg = 'bg-amber-50 text-amber-700 border-amber-200';
                    $cohortLabel = 'Lansia';
                    $cohortIcon = '👴';
                    $shadowClass = 'shadow-amber-500/20';
                }
            @endphp

            <div class="animate-fade-in-up stagger-{{ $index + 2 }} card-hover bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ qrOpen: false }">
                <!-- Card Gradient Header -->
                <div class="bg-gradient-to-r {{ $cohortColor }} p-5 sm:p-6 relative overflow-hidden">
                    <!-- Decorative circles -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div class="absolute right-8 -bottom-6 w-16 h-16 bg-white/5 rounded-full"></div>
                    
                    <div class="relative z-10 flex items-start justify-between">
                        <div>
                            <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ $cohortIcon }} {{ $cohortLabel }}</p>
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $warga->nama }}</h3>
                            <p class="text-white/70 text-xs font-mono mt-1">NIK: {{ $warga->nik }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 sm:p-6">

                    <!-- ===== QR CODE SECTION (Inline) ===== -->
                    <div class="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200/70 p-5 sm:p-6 mb-5">
                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <!-- QR Code -->
                            <div class="flex-shrink-0">
                                <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 qr-container">
                                    @php
                                        // Use a short signed token: base64(NIK):hmac_signature
                                        // This produces ~50 chars instead of 200+ from Crypt::encryptString
                                        $signature = hash_hmac('sha256', $warga->nik, config('app.key'));
                                        $token = base64_encode($warga->nik) . ':' . substr($signature, 0, 16);
                                    @endphp
                                    <div class="w-[160px] h-[160px] sm:w-[180px] sm:h-[180px] flex items-center justify-center">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(1)->errorCorrection('H')->generate($token) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- QR Info -->
                            <div class="text-center sm:text-left flex-1">
                                <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"></rect><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"></rect><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"></rect><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14h3v3m4 0v3h-3m0-7h3v1m-7 3v3h1"></path></svg>
                                    </div>
                                    <h4 class="font-black text-slate-800 text-sm">Kartu Digital Posyandu</h4>
                                </div>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">Tunjukkan QR Code ini ke Kader di <strong class="text-slate-700">Meja 1 (Pendaftaran)</strong> saat kunjungan Posyandu untuk proses absensi cepat.</p>
                                <div class="mt-3 flex items-center justify-center sm:justify-start gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Terenkripsi & Aman
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="flex flex-wrap gap-2 mb-5">
                        @if($lastPemeriksaan)
                            @if($lastPemeriksaan->berat_badan)
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                                <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 font-bold">BB Terakhir</p>
                                    <p class="text-sm font-black text-slate-800">{{ $lastPemeriksaan->berat_badan }} kg</p>
                                </div>
                            </div>
                            @endif
                            @if($lastPemeriksaan->tinggi_badan)
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                                <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 font-bold">TB Terakhir</p>
                                    <p class="text-sm font-black text-slate-800">{{ $lastPemeriksaan->tinggi_badan }} cm</p>
                                </div>
                            </div>
                            @endif
                        @else
                            <p class="text-xs text-slate-400 font-semibold italic">Belum ada riwayat pemeriksaan.</p>
                        @endif
                    </div>

                    <!-- Kunjungan Terakhir -->
                    @if($lastPemeriksaan)
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">Kunjungan terakhir: {{ \Carbon\Carbon::parse($lastPemeriksaan->created_at)->translatedFormat('d F Y') }}</span>
                    </div>
                    @endif

                    <!-- Action Button -->
                    <a href="{{ route('warga.kms', $warga) }}" class="flex items-center justify-center w-full py-3 bg-gradient-to-r {{ $cohortColor }} hover:opacity-90 text-white text-sm font-bold rounded-2xl transition-all active:scale-[0.98] gap-2 shadow-lg {{ $shadowClass }}">
                        @if($warga->balita)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Buka KMS Digital
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Lihat Riwayat Pemeriksaan
                        @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <style>
                    .qr-container svg {
                        width: 100% !important;
                        height: 100% !important;
                        display: block;
                    }
                </style>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-10 sm:p-16 border border-dashed border-slate-200 text-center animate-fade-in-up">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800">Belum Ada Data Keluarga</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">Akun Anda belum terhubung dengan data warga manapun. Silakan hubungi Kader Posyandu setempat untuk didaftarkan.</p>
            </div>
        @endforelse
    </div>
@endsection
