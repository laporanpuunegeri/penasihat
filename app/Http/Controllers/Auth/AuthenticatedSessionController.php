<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        // ❗ Sekat login jika bukan dari MELAKA atau KEDAH (kecuali super_admin)
        if (
            $user->role !== 'super_admin' &&
            !in_array(strtoupper($user->negeri), ['MELAKA', 'KEDAH'])
        ) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Log masuk hanya dibenarkan untuk pengguna dari negeri MELAKA atau KEDAH sahaja.',
            ]);
        }

        // ✅ Arahkan ke dashboard jika sah
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
