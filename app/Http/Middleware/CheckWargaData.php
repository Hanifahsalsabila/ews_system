<?php

namespace App\Http\Middleware;

use App\Models\Warga;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWargaData
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('orang_tua')) {
            if (! Warga::where('user_id', auth()->id())->exists()) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda belum terhubung dengan data warga. Silakan hubungi Kader Posyandu untuk didaftarkan.',
                ]);
            }
        }

        return $next($request);
    }
}
