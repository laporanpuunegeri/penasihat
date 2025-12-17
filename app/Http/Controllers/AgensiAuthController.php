<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgensiUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AgensiAuthController extends Controller
{
    // 1. Papar Borang Daftar (Path Baru)
    public function paparBorangDaftar()
    {
        // Ubah 'auth.agensi-register' jadi 'agensi.agensi-register'
        return view('agensi.agensi-register'); 
    }

    // ... (Function simpanPendaftaran KEKAL SAMA) ...

    public function simpanPendaftaran(Request $request)
    {
        // ... coding simpan sama macam tadi ...
        $request->validate([
            'nama_pegawai' => 'required|string',
            'email' => 'required|email|unique:agensi_users,email',
            'password' => 'required|min:8|confirmed',
            'no_telefon' => 'required',
            'nama_agensi' => 'required',
            'negeri' => 'required',
        ]);

        AgensiUser::create([
            'nama_pegawai' => $request->nama_pegawai,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telefon' => $request->no_telefon,
            'nama_agensi' => $request->nama_agensi,
            'negeri' => $request->negeri,
            'status' => 'pending', 
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berjaya. Sila tunggu pengesahan Admin.');
    }

    // 2. Papar Borang Login (Path Baru)
    public function paparBorangLogin()
    {
        return view('agensi.agensi-login');
    }

    // ... (Function prosesLogin & keluar KEKAL SAMA) ...
    public function prosesLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('agensi')->attempt($credentials)) {
            $user = Auth::guard('agensi')->user();
            
            if ($user->status !== 'aktif') {
                Auth::guard('agensi')->logout();
                return back()->withErrors(['email' => 'Akaun anda masih dalam semakan atau digantung.']);
            }

            $request->session()->regenerate();
            return redirect()->route('dashboard.warta'); 
        }

        return back()->withErrors(['email' => 'Emel atau kata laluan salah.']);
    }

    public function keluar(Request $request)
    {
        Auth::guard('agensi')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}