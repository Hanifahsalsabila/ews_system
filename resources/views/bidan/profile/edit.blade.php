<x-bidan-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Pengaturan Profil') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola informasi akun dan keamanan sandi Anda</p>
            </div> 
        </div> 
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
                @csrf
                @method('patch')
                
                <!-- Update Profile Information -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-black text-slate-800">Informasi Profil</h3>
                        <p class="text-sm text-slate-500">Perbarui nama lengkap dan alamat email akun Anda.</p>
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
</x-bidan-layout>
