<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; // Tambah ini untuk cek route
use App\Models\AgensiUser;

// Tak perlu import model satu-satu sebab kita panggil secara dinamik dalam dashboard()

class AgensiAuthController extends Controller
{
    // =================================================================
    // BAHAGIAN PENDAFTARAN & LOGIN (Kekal sama, tiada perubahan)
    // =================================================================

    public function paparBorangDaftar()
    {
        return view('agensi.agensi-register'); 
    }

    public function simpanPendaftaran(Request $request)
    {
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

    public function paparBorangLogin()
    {
        return view('agensi.agensi-login');
    }

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

    // =================================================================
    // BAHAGIAN DASHBOARD - DIBETULKAN (DINAMIK)
    // =================================================================

    public function dashboard()
    {
        $agensi_id = Auth::guard('agensi')->id();

        // Senarai Seksyen & Nama Paparan
        $senaraiSeksyen = [
            ['kod' => '12',   'nama' => '1. Seksyen 12 (Serah Balik Kurnia Semula)'],
            ['kod' => '62',   'nama' => '2. Seksyen 62 (Pewartaan Rizab)'],
            ['kod' => '64',   'nama' => '3. Seksyen 64 (Pembatalan Rizab)'],
            ['kod' => '97',   'nama' => '4. Seksyen 97 & 98 (Notis Tuntutan)'],
            ['kod' => '130',  'nama' => '5. Seksyen 130 (Pelucuthakan Tanah)'],
            ['kod' => '168',  'nama' => '6. Seksyen 168 (Gantian Hakmilik Hilang)'],
            ['kod' => '175a', 'nama' => '7. Seksyen 175A (Penyelesaian Pusaka)'],
            ['kod' => '175d', 'nama' => '8. Seksyen 175D (Perintah Pentadbir Tanah)'],
            ['kod' => '261',  'nama' => '9. Seksyen 261 (Lelongan Tanah)'],
            ['kod' => '263',  'nama' => '10. Seksyen 263 (Jualan Atas Permintaan Gadai)'],
            ['kod' => '326',  'nama' => '11. Seksyen 326 (Notis Memotong Kaveat)'],
        ];

        $stats = [];

        foreach ($senaraiSeksyen as $sek) {
            // 1. Tentukan Nama Model secara automatik
            $suffix = ctype_digit($sek['kod']) ? $sek['kod'] : strtoupper($sek['kod']); 
            $modelClass = "App\\Models\\PermohonanSeksyen" . $suffix;

            // 2. Tentukan Nama Route secara automatik
            $routeName = 'permohonan.seksyen' . strtolower($sek['kod']);

            // 3. Kira Data (Hanya jika Model wujud)
            if (class_exists($modelClass)) {
                $baru = $modelClass::where('agensi_id', $agensi_id)->whereIn('status', ['Baru', 'Semakan'])->count();
                $selesai = $modelClass::where('agensi_id', $agensi_id)->whereNotIn('status', ['Baru', 'Semakan'])->count();
                $total = $modelClass::where('agensi_id', $agensi_id)->count();
            } else {
                $baru = 0; $selesai = 0; $total = 0;
            }

            $stats[] = (object) [
                'tajuk' => $sek['nama'],
                'route' => $routeName,
                'baru' => $baru,
                'selesai' => $selesai,
                'total' => $total
            ];
        }

        // Hantar variable $stats ke View (Pastikan nama fail view betul)
        return view('dashboard.agensi', compact('stats'));
    }

    public function logout(Request $request)
    {
        Auth::guard('agensi')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); 
    }
}