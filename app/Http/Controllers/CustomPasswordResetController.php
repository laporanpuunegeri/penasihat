<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomPasswordResetController extends Controller
{
    /**
     * Semak email & nombor telefon, kemudian redirect.
     */
    public function verifyUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required'
        ]);

        $user = User::where('email', $request->email)
                    ->where('no_telefon', $request->phone)
                    ->first();

        if (!$user) {
            // Jika tak padan, terus ke login page
            return redirect()->route('login')
                             ->with('error', 'Maklumat tidak sepadan atau tiada dalam rekod.');
        }

        // Jika padan, pergi ke borang tetapan semula
        return redirect()->route('custom.password.form', ['email' => $user->email]);
    }

    /**
     * Papar borang untuk reset password (selepas pengesahan).
     */
    public function showResetForm($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Pengguna tidak ditemui.');
        }

        return view('auth.custom-reset-password', ['email' => $email]);
    }

    /**
     * Simpan kata laluan baharu.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Pengguna tidak dijumpai.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Kata laluan berjaya ditukar. Sila log masuk semula.');
    }
}
