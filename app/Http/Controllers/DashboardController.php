<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Import Model lain jika diperlukan oleh method di bawah (pentadbiran/kewangan)
// use App\Models\Kewangan; 
// use App\Models\WaranPerjawatan; 

class DashboardController extends Controller
{
    /**
     * DASHBOARD UTAMA (Main entry point / Index).
     * Method ini adalah WAJIB dan berfungsi sebagai router ke dashboard spesifik.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $bahagian = trim($user->bahagian ?? '');

        // 2. Routing Logic
        switch (strtoupper($bahagian)) {
            case 'BAHAGIAN PENTADBIRAN':
                return redirect()->route('dashboard.pentadbiran');
            case 'BAHAGIAN KEWANGAN':
                return redirect()->route('dashboard.kewangan');
            case 'BAHAGIAN PENTADBIRAN & KEWANGAN':
            case 'BAHAGIAN PENTADBIRAN DAN KEWANGAN':
                return redirect()->route('dashboard.pentadbirandankewangan');
            case 'BAHAGIAN GUAMAN':
                return redirect()->route('dashboard.guaman');
            case 'BAHAGIAN PENASIHAT':
                return redirect()->route('dashboard.penasihat');
            case 'BAHAGIAN PENDAKWAAN':
                return redirect()->route('dashboard.pendakwaan');
            case 'BAHAGIAN SEMAKAN':
                return redirect()->route('dashboard.semakan');
            case 'BAHAGIAN SYARIAH':
                return redirect()->route('dashboard.syariah');
            
            default:
                // Fallback paling selamat
                return view('dashboard.index', ['title' => 'Dashboard Utama']);
        }
    }
    
    // --- METHOD LAIN (Diperlukan oleh Route) ---

    // NOTE: Gantikan placeholder di bawah dengan logik sebenar anda
    
    public function pentadbiran(Request $request)
    {
        // Logik Pentadbiran (Waran) perlu dipindahkan ke sini jika route masih memanggilnya
        return view('dashboard.pentadbiran', ['dataPentadbiran' => [], 'tahun' => date('Y')]);
    }

    public function kewangan(Request $request)
    {
        // Logik Kewangan perlu dipindahkan ke sini jika route masih memanggilnya
        return view('dashboard.kewangan', ['data_graf' => [], 'tahun' => date('Y')]);
    }
    
    public function pentadbirandankewangan(Request $request)
    {
        // Logik gabungan untuk route dashboard.pentadbirandankewangan
        return view('dashboard.pentadbirandankewangan', ['data_graf' => [], 'dataPentadbiran' => [], 'tahun' => date('Y')]);
    }
}