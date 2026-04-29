@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<div class="mx-auto max-w-5xl space-y-8 pb-10">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pengaturan Profil</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Kelola data personal dan keamanan sandi</p>
        </div>
    </div>

    <!-- Update Profile Section -->
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-xl hover:shadow-emerald-500/5 transition-all">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <header class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 border-l-4 border-emerald-500 pl-3">
                    Informasi Profil
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Perbarui nama lengkap dan alamat email akun admin Anda.
                </p>
            </header>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('patch')

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1" for="name">Nama Lengkap</label>
                    <input id="name" name="name" type="text" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-emerald-500 focus:border-emerald-500 transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1" for="email">Alamat Email</label>
                    <input id="email" name="email" type="email" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-emerald-500 focus:border-emerald-500 transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl">
                            <p class="text-sm font-medium text-orange-800">
                                Email Anda belum diverifikasi.
                                <button form="send-verification" class="font-bold underline text-orange-600 hover:text-orange-900 rounded-md focus:outline-none ml-1">
                                    Klik di sini untuk mengirim ulang email verifikasi.
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-bold text-xs text-emerald-600 uppercase tracking-widest">
                                    Tautan verifikasi baru telah dikirimkan!
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95">
                        Simpan Profil
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                            Tersimpan.
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password Section -->
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-xl hover:shadow-amber-500/5 transition-all">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <header class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 border-l-4 border-amber-500 pl-3">
                    Ubah Kata Sandi
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Gunakan sandi acak dan panjang untuk menjaga keamanan level eksekutif Anda.
                </p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1" for="update_password_current_password">Sandi Anda Saat Ini</label>
                    <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-amber-500 focus:border-amber-500 transition-colors" autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-500 font-medium text-xs" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1" for="update_password_password">Sandi Baru</label>
                    <input id="update_password_password" name="password" type="password" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-amber-500 focus:border-amber-500 transition-colors" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-500 font-medium text-xs" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1" for="update_password_password_confirmation">Konfirmasi Sandi Baru</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-amber-500 focus:border-amber-500 transition-colors" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-500 font-medium text-xs" />
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-95">
                        Perbarui Sandi
                    </button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                            Sandi diganti.
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Section -->
    <div class="bg-white rounded-3xl p-8 border border-red-100 shadow-sm relative overflow-hidden group">
        <div class="relative z-10 max-w-xl">
            <header class="mb-6">
                <h2 class="text-xl font-bold text-rose-600 border-l-4 border-rose-600 pl-3">
                    Hapus Akun
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Setelah dihapus, seluruh data profil Anda akan sirna dan sistem akan mengeluarkan Anda selamanya.
                </p>
            </header>

            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="px-6 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-200 transition-all active:scale-95">
                Hapus Akun Permanen
            </button>

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
                    @csrf
                    @method('delete')

                    <h2 class="text-2xl font-black text-rose-600 tracking-tight">
                        Apakah Anda benar-benar yakin?
                    </h2>

                    <p class="mt-3 text-sm text-gray-500">
                        Peringatan: Hak akses Anda akan dicabut seketika. Silakan ketikkan sandi Anda untuk memverifikasi tindakan destruktif ini.
                    </p>

                    <div class="mt-6">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" class="w-full bg-slate-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-rose-500 focus:border-rose-500 transition-colors" placeholder="Ketik kata sandi..." />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 font-bold text-xs text-rose-600" />
                    </div>

                    <div class="mt-8 flex justify-end gap-3 shrink-0">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>

                        <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-200 transition-all active:scale-95">
                            Ya, Hapus Sekarang
                        </button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>
</div>
@endsection
