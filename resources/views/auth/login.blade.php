<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'E-Posyandu') }} | Login Petugas & Warga</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    body { font-family: 'Inter', sans-serif; }
    
    .bg-medical {
        background-image: url("{{ asset('images/bg-posyandu.png') }}");
        background-size: cover;
        background-position: center;
    }
    </style>
</head>
<body class="antialiased text-gray-800 bg-gray-50 flex min-h-screen">

    <!-- Left pane: Interactive Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative" x-data="loginForm()">
        
        <!-- Dynamic Back Button (Top Left) -->
        <a href="{{ url('/') }}" x-show="!selectedRole" class="group absolute top-6 left-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-bold text-slate-400 hover:text-emerald-600 bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition-all duration-300 shadow-sm hover:shadow-md z-10">
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Beranda</span>
        </a>

        <button type="button" @click="resetRole()" x-show="selectedRole" x-cloak class="group absolute top-6 left-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-bold text-slate-400 hover:text-emerald-600 bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition-all duration-300 shadow-sm hover:shadow-md z-10">
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Pilih Peran</span>
        </button>

        <div class="max-w-md w-full">

            <div class="mb-10 text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-500/30">
                        P
                    </div>
                    <span class="font-bold text-2xl text-gray-900 tracking-tight">E-Posyandu</span>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2" x-text="getHeadingText()">Selamat Datang</h2>
                <p class="text-gray-500" x-text="getSubText()">Silakan pilih porta akses sesuai dengan jabatan atau peran Anda.</p>
            </div>

            <!-- Session Status Error -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
            @if ($errors->any())
                <div class="mb-4 p-4 text-sm text-red-600 border border-red-200 bg-red-50 rounded-lg">
                    @if(str_contains($errors->first(), 'belum terhubung'))
                        {{ $errors->first() }}
                    @else
                        Autentikasi gagal. Silakan periksa kembali email dan kata sandi Anda.
                    @endif
                </div>
            @endif

            <!-- Role Selection (Step 1) -->
            <div x-show="!selectedRole" x-transition.opacity.duration.300ms class="grid grid-cols-1 gap-4">
                
                <!-- Pimpinan / Bidan -->
                <button @click="selectRole('executive', 'bidan@eposyandu.id')" type="button" class="group relative px-6 py-5 border-2 border-gray-100 rounded-2xl text-left hover:border-emerald-500 hover:bg-emerald-50 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Pimpinan / Bidan</h3>
                                <p class="text-sm text-gray-500 mt-1">Akses dashboard manajerial posyandu.</p>
                            </div>
                        </div>
                        <div class="text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </button>

                <!-- Kader -->
                <button @click="selectRole('kader', 'kader@eposyandu.id')" type="button" class="group relative px-6 py-5 border-2 border-gray-100 rounded-2xl text-left hover:border-blue-500 hover:bg-blue-50 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Kader Posyandu</h3>
                                <p class="text-sm text-gray-500 mt-1">Portal input data 5 meja & absensi warga.</p>
                            </div>
                        </div>
                        <div class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </button>

                <!-- Orang Tua -->
                <button @click="selectRole('warga', 'orangtua@eposyandu.id')" type="button" class="group relative px-6 py-5 border-2 border-gray-100 rounded-2xl text-left hover:border-purple-500 hover:bg-purple-50 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Orang Tua / Warga</h3>
                                <p class="text-sm text-gray-500 mt-1">Pantau grafik KMS balita secara mandiri.</p>
                            </div>
                        </div>
                        <div class="text-purple-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </button>

                <!-- Admin -->
                <a href="{{ route('admin.login') }}" class="group relative px-6 py-5 border-2 border-gray-100 rounded-2xl text-left hover:border-slate-800 hover:bg-slate-50 transition duration-300 block">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-800 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Super Admin / Admin</h3>
                                <p class="text-sm text-gray-500 mt-1">Portal otentikasi khusus administrator.</p>
                            </div>
                        </div>
                        <div class="text-slate-800 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Actual Login Form (Step 2) -->
            <div x-cloak x-show="selectedRole" x-transition.opacity.duration.300ms>

                <form method="POST" action="{{ route('login') }}" class="space-y-5 mt-6">
                    @csrf
                    
                    <div>
                        <label for="login_id" class="block font-medium text-sm text-gray-700" x-text="getLoginLabel()">Identitas Login</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg x-show="selectedRole !== 'warga'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <svg x-show="selectedRole === 'warga'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            </div>
                            <input id="login_id" x-model="loginIdInput" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 pl-12 pr-4 transition" :type="selectedRole === 'warga' ? 'text' : 'email'" name="login_id" required autofocus autocomplete="username" :placeholder="getLoginPlaceholder()" />
                        </div>
                    </div>

                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                        <div class="relative mt-1">
                            <input id="password" value="password" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 pl-4 pr-12 transition"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required autocomplete="current-password" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-600 focus:outline-none transition-colors">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Untuk demo aplikasi, sandi bawaan adalah 'password'.</p>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-emerald-600 hover:text-emerald-800 hover:underline transition" href="{{ route('password.request') }}">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>

                    <button type="submit" :class="getBtnColor()" class="w-full mt-4 text-white font-semibold py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 relative overflow-hidden group">
                        <span class="relative z-10">Mulai Masuk</span>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </button>
                    
                    <!-- Notice -->
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 mt-6 text-sm text-yellow-800">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p>Sistem ini menggunakan Single API Route untuk Login. Identitas Role Anda akan diverifikasi otomatis oleh basis data setelah login.</p>
                        </div>
                    </div>

                </form>
            </div>
            
        </div>
    </div>

    <!-- Right pane: Hero Image -->
    <div class="hidden lg:block lg:w-1/2 bg-medical relative">
        <div class="absolute inset-0 bg-emerald-900/20"></div>
        
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent"></div>
        
        
    </div>

    <!-- Alpine Widget -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                selectedRole: null,
                loginIdInput: '',
                
                selectRole(roleType, defaultLogin) {
                    this.selectedRole = roleType;
                    this.loginIdInput = defaultLogin;
                },
                
                resetRole() {
                    this.selectedRole = null;
                    this.loginIdInput = '';
                },
                
                getHeadingText() {
                    if (this.selectedRole === 'executive') return 'Masuk Executive';
                    if (this.selectedRole === 'kader') return 'Masuk Kader';
                    if (this.selectedRole === 'warga') return 'Masuk Orang Tua';
                    return 'Sistem Akses';
                },
                
                getSubText() {
                    if (this.selectedRole) return 'Silakan masukkan kredensial yang telah didaftarkan oleh Admin Desa.';
                    return 'Pilih entitas masuk untuk melihat antarmuka yang disesuaikan untuk Anda.';
                },

                getLoginLabel() {
                    if (this.selectedRole === 'warga') return 'No. WhatsApp / Username / NIK';
                    return 'Alamat Email';
                },

                getLoginPlaceholder() {
                    if (this.selectedRole === 'warga') return 'Contoh: 0812..., mbahsiti01, atau 3201...';
                    return 'email@eposyandu.id';
                },

                getBtnColor() {
                    if (this.selectedRole === 'kader') return 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30';
                    if (this.selectedRole === 'warga') return 'bg-purple-600 hover:bg-purple-700 shadow-purple-500/30';
                    return 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30';
                }
            }))
        })
    </script>
</body>
</html>
