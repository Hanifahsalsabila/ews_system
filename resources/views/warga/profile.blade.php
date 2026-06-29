@extends('layouts.warga')

@section('content')
    <!-- ===== HEADER ===== -->
    <div class="animate-fade-in-up mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Profil & Keamanan</h2>
        <p class="text-slate-500 mt-1 text-sm font-medium">Kelola informasi akun dan perbarui password Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">

        <!-- LEFT: Account Info Card -->
        <div class="lg:col-span-1 animate-fade-in-up stagger-1">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <!-- Header with gradient -->
                <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-6 sm:p-8 text-center relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-28 h-28 bg-white/10 rounded-full"></div>
                    <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-white/5 rounded-full"></div>
                    <div class="relative z-10">
                        <img class="w-20 h-20 rounded-3xl mx-auto shadow-xl ring-4 ring-white/30 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=10b981&bold=true&size=160" alt="Avatar">
                        <h3 class="text-xl font-black text-white mt-4">{{ auth()->user()->name }}</h3>
                        <p class="text-emerald-100 text-xs font-bold mt-1">Orang Tua / Warga</p>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-3 bg-slate-50 rounded-2xl p-3.5 border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Username</p>
                            <p class="text-sm font-black text-slate-800 truncate">{{ auth()->user()->username ?? auth()->user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 rounded-2xl p-3.5 border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</p>
                            <p class="text-sm font-black text-slate-800 truncate">{{ auth()->user()->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 rounded-2xl p-3.5 border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terdaftar Sejak</p>
                            <p class="text-sm font-black text-slate-800">{{ auth()->user()->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Change Password Form -->
        <div class="lg:col-span-2 animate-fade-in-up stagger-2">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="font-black text-slate-800 flex items-center gap-2 text-base sm:text-lg">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        Ubah Password
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 ml-10">Ganti password default Anda agar akun lebih aman.</p>
                </div>

                <form method="post" action="{{ route('warga.profile.password') }}" class="p-5 sm:p-8">
                    @csrf
                    @method('put')

                    @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 rounded-2xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-bold text-rose-700">Terjadi Kesalahan</span>
                        </div>
                        @foreach($errors->all() as $error)
                            <p class="text-xs text-rose-600 ml-6">• {{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    <div class="space-y-5">
                        <div>
                            <label for="current_password" class="block text-sm font-bold text-slate-700 mb-2">Password Saat Ini</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                </div>
                                <input id="current_password" name="current_password" type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium text-sm transition-all" autocomplete="current-password" placeholder="Masukkan password lama" required>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input id="password" name="password" type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium text-sm transition-all" autocomplete="new-password" placeholder="Masukkan password baru" required>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium text-sm transition-all" autocomplete="new-password" placeholder="Ulangi password baru" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Password Baru
                        </button>
                        <a href="{{ route('warga.dashboard') }}" class="px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition-all text-center active:scale-[0.98]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Security Tips -->
            <div class="mt-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-3xl p-5 sm:p-6 border border-amber-100 animate-fade-in-up stagger-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-amber-800 text-sm">Tips Keamanan</h4>
                        <ul class="mt-2 space-y-1.5 text-xs text-amber-700 font-medium">
                            <li class="flex items-center gap-2"><span class="text-amber-500">•</span> Gunakan minimal 8 karakter dengan huruf besar, kecil, dan angka</li>
                            <li class="flex items-center gap-2"><span class="text-amber-500">•</span> Jangan bagikan password Anda kepada siapapun</li>
                            <li class="flex items-center gap-2"><span class="text-amber-500">•</span> Segera ganti password default (<code class="bg-amber-100 px-1.5 py-0.5 rounded text-[10px] font-mono font-bold">posyandu123</code>)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
