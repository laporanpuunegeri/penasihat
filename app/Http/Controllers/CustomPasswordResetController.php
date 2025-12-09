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
            'phone' => 'required|digits_between:10,15' // Tambah validation digits
        ]);

        $user = User::where('email', $request->email)
                    // --- PEMBETULAN: Tukar no_telefon kepada 'phone' (Assume DB name) ---
                    ->where('phone', $request->phone) 
                    ->first();

        if (!$user) {
            // JIKA GAGAL: Redirect balik ke borang verifikasi
            return redirect()->route('custom.password.request')
                             ->with('error', 'Maklumat Emel atau Nombor Telefon tidak sepadan. Sila cuba lagi.')
                             ->withInput(); // Kekalkan input lama
        }

        // JIKA BERJAYA: Pergi ke borang tetapan semula
        return redirect()->route('custom.password.form', ['email' => $user->email]);
    }

    /**
     * Papar borang untuk reset password (selepas pengesahan).
     */
    public function showResetForm($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Pautan tetapan semula tidak sah.');
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
            'password' => 'required|confirmed|min:8', // Tambah min:8 untuk security
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