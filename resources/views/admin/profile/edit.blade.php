@extends('layouts.admin')

@section('title', 'Pengaturan Profile Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Pengaturan Profile</h2>
        <p class="text-sm font-medium text-slate-500 mt-1">Kelola informasi akun Super Admin dan kata sandi Anda.</p>
    </div>

    <!-- Peringatan -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-2xl mb-8 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-blue-800 mb-1">Informasi Hak Akses</h4>
            <p class="text-xs text-blue-700">Sebagai Super Admin, perubahan profil Anda tidak akan memengaruhi data master lainnya. Harap gunakan email yang valid.</p>
        </div>
    </div>

    <div class="space-y-6">
            <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <!-- Update Profile Information -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-black text-slate-800">Informasi Akun</h3>
                        <p class="text-sm text-slate-500">Perbarui nama dan alamat email akun Anda.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-black text-slate-800">Ubah Password</h3>
                        <p class="text-sm text-slate-500">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Password Saat Ini</label>
                            <input type="password" name="current_password" autocomplete="current-password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                            <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-rose-500 font-medium text-xs" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                            <input type="password" name="password" autocomplete="new-password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 font-medium text-xs" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-500 font-medium text-xs" />
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 mt-6">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl transition-colors text-sm shadow-lg shadow-emerald-500/20">
                                Simpan Profil & Password
                            </button>
                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-600">Semua perubahan berhasil disimpan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>
@endsection
