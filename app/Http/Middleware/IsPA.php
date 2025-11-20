<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsPA
{
    public function handle(Request $request, Closure $next)
    {
        // Andaian: role disimpan sebagai 'PA'
        if (Auth::check() && Auth::user()->role === 'PA') {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Anda bukan PA.');
    }
}
