<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Ambil Email & Password
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // =========================================================
        // STEP 1: CUBA LOGIN SEBAGAI STAFF (GUARD: WEB)
        // =========================================================
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            
            $request->session()->regenerate();
            
            // 🔥 SEKATAN NEGERI DAH DIBUANG 🔥
            // Sekarang semua user (Web) boleh masuk terus ke Dashboard
            
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // =========================================================
        // STEP 2: CUBA LOGIN SEBAGAI AGENSI (GUARD: AGENSI)
        // =========================================================
        if (Auth::guard('agensi')->attempt($credentials, $remember)) {
            
            $user = Auth::guard('agensi')->user();
            
            // Logic Check Status Akaun (Pending/Aktif) KEKAL untuk Agensi
            if ($user->status !== 'aktif') {
                Auth::guard('agensi')->logout(); // Tendang keluar
                
                throw ValidationException::withMessages([
                    'email' => 'Akaun agensi anda masih dalam semakan (Pending) atau digantung.',
                ]);
            }

            $request->session()->regenerate();

            // Redirect Agensi ke Dashboard Warta
            return redirect()->route('dashboard.warta');
        }

        // =========================================================
        // STEP 3: KALAU DUA-DUA TAK JUMPA (WRONG PASSWORD/EMAIL)
        // =========================================================
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Check guard mana yang tengah login untuk logout yang betul
        if (Auth::guard('agensi')->check()) {
            Auth::guard('agensi')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}