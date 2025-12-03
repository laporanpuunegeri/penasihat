<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        // Mendapatkan URL semasa
        $currentUrl = $request->path(); 

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // Jika pengguna sudah log masuk DAN cuba mengakses halaman /register (LALUAN TERHAD), 
                // benarkan mereka untuk teruskan (TIDAK REDIRECT)
                if ($currentUrl == 'register') {
                    // Benarkan pengguna (CC/EO) yang sudah login untuk kekal di halaman /register
                    return $next($request); 
                }
                
                // Untuk semua laluan 'guest' yang lain, redirect ke HOME (dashboard)
                return redirect(RouteServiceProvider::HOME); 
            }
        }

        return $next($request);
    }
}