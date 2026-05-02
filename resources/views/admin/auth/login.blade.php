<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - E-Posyandu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-slate-100 min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    
    <!-- Abstract Background -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute top-[30%] -right-[15%] w-[40%] h-[40%] rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[60%] rounded-full bg-emerald-600/10 blur-3xl"></div>
    </div>
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-8 sm:p-12 relative z-10">
        
        <!-- Back to Home -->
        <a href="{{ url('/') }}" class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-300 mb-6 -ml-1">
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>

        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-100 text-emerald-600 shadow-sm shadow-emerald-600/10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight sm:text-4xl mb-2">Admin Portal</h2>
            <p class="text-slate-500 text-sm">Sistem Manajemen E-Posyandu</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-6 font-medium text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 p-4 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                        class="block w-full pl-10 px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none transition-colors sm:text-sm placeholder-slate-400" placeholder="admin@eposyandu.id">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full pl-10 px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none transition-colors sm:text-sm placeholder-slate-400" placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded cursor-pointer">
                    <label for="remember_me" class="block text-sm text-slate-600 cursor-pointer select-none">
                        Ingat Saya
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                            Lupa password?
                        </a>
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 hover:-translate-y-0.5">
                    Masuk ke Dashboard
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-center text-sm text-slate-500 relative z-10 flex flex-col items-center gap-2">
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center font-medium text-slate-600 hover:text-emerald-600 transition-colors bg-white/60 px-4 py-2 rounded-full border border-slate-200/60 backdrop-blur-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Login Publik
        </a>
        <p class="mt-2 text-xs opacity-70">&copy; <?php echo date('Y'); ?> E-Posyandu.</p>
    </div>
</body>
</html>
