<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada untuk debug route

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PergerakanController;
use App\Http\Controllers\PentadbiranController;
use App\Http\Controllers\KewanganController;
use App\Http\Controllers\AgensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanPPUUNController;
use App\Http\Controllers\DbusController;
use App\Http\Controllers\DbusPecahanController;
use App\Http\Controllers\GuamanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KestatatertibController;
use App\Http\Controllers\LaporanLainLainController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LampiranKesMahkamahController;

// Controllers Laporan (Untuk Grafik & CRUD)
use App\Http\Controllers\LaporanPandanganUndangController;
use App\Http\Controllers\LaporanKesMahkamahController;
use App\Http\Controllers\LaporanGubalanUndangController;
use App\Http\Controllers\LaporanPindaanUndangController;
use App\Http\Controllers\LaporanSemakanUndangController;
use App\Http\Controllers\LaporanMesyuaratController;

// Auth & Custom Passwords
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CustomPasswordResetController;

// Dashboard Controllers (Bahagian)
use App\Http\Controllers\DashboardBahagian\GuamanDashboardController;
use App\Http\Controllers\DashboardBahagian\KewanganPentadbiranDashboardController;
use App\Http\Controllers\DashboardBahagian\PenasihatDashboardController;

// =========================================================================
// ROUTE UTAMA & GUEST
// =========================================================================

Route::get('/', fn() => redirect()->route('dashboard'))->name('utama');

Route::middleware('guest')->group(function () {
    Route::get('/reset-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/reset-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/custom-forgot-password', fn() => view('auth.check-user'))->name('custom.password.request');
    Route::post('/custom-verify', [CustomPasswordResetController::class, 'verifyUser'])->name('custom.password.verify');
    Route::get('/custom-reset-password/{email}', [CustomPasswordResetController::class, 'showResetForm'])->name('custom.password.form');
    Route::post('/custom-reset-password', [CustomPasswordResetController::class, 'updatePassword'])->name('custom.password.update');
});

// =========================================================================
// ROUTE AUTH (LOGGED IN USERS)
// =========================================================================

Route::middleware('auth')->group(function () {

    // --- DASHBOARD REDIRECT LOGIC ---
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $bahagian = strtoupper(trim($user->bahagian ?? ''));
        switch ($bahagian) {
            case 'BAHAGIAN PENTADBIRAN': return redirect()->route('dashboard.pentadbiran');
            case 'BAHAGIAN KEWANGAN': return redirect()->route('dashboard.kewangan');
            case 'BAHAGIAN PENTADBIRAN & KEWANGAN':
            case 'BAHAGIAN PENTADBIRAN DAN KEWANGAN': return redirect()->route('dashboard.pentadbirandankewangan');
            case 'BAHAGIAN GUAMAN': return redirect()->route('dashboard.guaman');
            case 'BAHAGIAN PENASIHAT': return redirect()->route('dashboard.penasihat');
            case 'BAHAGIAN PENDAKWAAN': return redirect()->route('dashboard.pendakwaan');
            case 'BAHAGIAN SEMAKAN': return redirect()->route('dashboard.semakan');
            case 'BAHAGIAN SYARIAH': return redirect()->route('dashboard.syariah');
            default: return view('dashboard.index', ['title' => 'Dashboard Utama']);
        }
    })->name('dashboard');

    // --- GROUP DASHBOARD KHAS ---
    Route::prefix('dashboard')->group(function () {
        Route::get('/pentadbiran', [KewanganPentadbiranDashboardController::class, 'dashboard'])->name('dashboard.pentadbiran');
        Route::get('/kewangan', [KewanganPentadbiranDashboardController::class, 'dashboard'])->name('dashboard.kewangan');
        Route::get('/pentadbiran-kewangan', [KewanganPentadbiranDashboardController::class, 'dashboard'])->name('dashboard.pentadbirandankewangan');
        Route::get('/guaman', [GuamanDashboardController::class, 'dashboard'])->name('dashboard.guaman');

        // Dashboard Penasihat (8 Graf Utama)
        Route::get('/penasihat', [PenasihatDashboardController::class, 'index'])->name('dashboard.penasihat');

        Route::view('/pendakwaan', 'dashboard.pendakwaan')->name('dashboard.pendakwaan');

        // 🔥 UBAH SINI: Route Semakan & Syariah GUNA CONTROLLER YANG SAMA (PenasihatDashboardController) 🔥
        Route::get('/semakan', [PenasihatDashboardController::class, 'index'])->name('dashboard.semakan');
        Route::get('/syariah', [PenasihatDashboardController::class, 'index'])->name('dashboard.syariah');
    });

    // =========================================================================
    // 🔥 ROUTE KHAS UNTUK DRILL-DOWN GRAF (PECAHAN BULAN) 🔥
    // =========================================================================
    Route::get('laporanpandanganundang/pecahan', [LaporanPandanganUndangController::class, 'pecahanBulan'])->name('laporanpandanganundang.pecahan');
    Route::get('laporankesmahkamah/pecahan', [LaporanKesMahkamahController::class, 'pecahanBulan'])->name('laporankesmahkamah.pecahan');
    Route::get('laporangubalanundang/pecahan', [LaporanGubalanUndangController::class, 'pecahanBulan'])->name('laporangubalanundang.pecahan');
    Route::get('laporanpindaanundang/pecahan', [LaporanPindaanUndangController::class, 'pecahanBulan'])->name('laporanpindaanundang.pecahan');
    Route::get('laporansemakanundang/pecahan', [LaporanSemakanUndangController::class, 'pecahanBulan'])->name('laporansemakanundang.pecahan');
    Route::get('laporanmesyuarat/pecahan', [LaporanMesyuaratController::class, 'pecahanBulan'])->name('laporanmesyuarat.pecahan');
    Route::get('kestatatertib/pecahan', [KestatatertibController::class, 'pecahanBulan'])->name('kestatatertib.pecahan');
    Route::get('lainlaintugasan/pecahan', [LaporanLainLainController::class, 'pecahanBulan'])->name('lainlaintugasan.pecahan');


    // --- USER PROFILE & LOGOUT ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });
    Route::post('/logout', function(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // --- PERGERAKAN ---
    Route::prefix('pergerakan')->name('pergerakan.')->group(function () {
        Route::get('/', [PergerakanController::class, 'index'])->name('index');
        Route::get('/create', [PergerakanController::class, 'create'])->name('create');
        Route::post('/', [PergerakanController::class, 'store'])->name('store');
        Route::get('/borang', [PergerakanController::class, 'showBorang'])->name('borang');
        Route::get('/inbox', [PergerakanController::class, 'index'])->name('inbox');
        Route::get('/kalendar-individu-pdf', [PergerakanController::class, 'cetakKalendarIndividuPDF'])->name('cetak_kalendar_individu');
        Route::get('/kalendar-keseluruhan-pdf', [PergerakanController::class, 'cetakKalendarKeseluruhan'])->name('cetak_kalendar_keseluruhan');
        Route::delete('/{id}', [PergerakanController::class, 'destroy'])->name('destroy');
        Route::put('/{pergerakan}/cc-review', [PergerakanController::class, 'cc_review'])->name('cc_review');
        Route::get('/{id}/lulus-cc', [PergerakanController::class, 'lulusCc'])->name('lulus_cc');
        Route::get('/{id}/tolak-cc', [PergerakanController::class, 'tolakCc'])->name('tolak_cc');
        Route::get('/{id}/lulus-yb', [PergerakanController::class, 'lulusYb'])->name('lulus_yb');
        Route::get('/{id}/tolak-yb', [PergerakanController::class, 'tolakYb'])->name('tolak_yb');
        Route::get('/sokong/{id}', [PergerakanController::class, 'sokong'])->name('sokong');
        Route::get('/lulus/{id}', [PergerakanController::class, 'lulus'])->name('lulus');
        Route::get('/tolak/{id}', [PergerakanController::class, 'tolak'])->name('tolak');
        Route::get('/cetak/{id}', [PergerakanController::class, 'cetakBorang'])->name('cetak');
    });

    // =========================================================================
    // MODUL PENTADBIRAN
    // =========================================================================
    Route::prefix('pentadbiran')->name('pentadbiran.')->group(function () {
        Route::resource('/', PentadbiranController::class)->names(['index' => 'dashboard']);

        // WARAN
        Route::get('/waran', [PentadbiranController::class, 'indexWaran'])->name('waran.index');
        Route::get('/waran/edit', [PentadbiranController::class, 'editWaran'])->name('waran.edit');
        Route::post('/waran/update', [PentadbiranController::class, 'updateWaran'])->name('waran.update');

        // DBUS (OBB)
        Route::prefix('dbus')->name('dbus.')->group(function () {
            Route::get('/cetak-pdf', [DbusController::class, 'cetakPdf'])->name('cetak_pdf');

            // AJAX Update OA
            Route::post('/update-oa-am', [DbusController::class, 'updateOaAm'])->name('updateOaAm');

            // Index & Master
            Route::get('/', [DbusController::class, 'index'])->name('index');
            Route::get('/create', [DbusController::class, 'create'])->name('create');
            Route::post('/store', [DbusController::class, 'store'])->name('store');
            Route::get('/edit', [DbusController::class, 'edit'])->name('edit');

            // Pecahan Views (GET)
            Route::get('/pecahan/{kod}/{tahun}', [DbusPecahanController::class, 'editPegawai'])->name('pecahan');
            Route::get('/edit-ol14101/{kod}/{tahun}', [DbusPecahanController::class, 'editOt'])->name('edit_ol14101');
            Route::get('/edit-os15000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs15'])->name('edit_os15000');
            Route::get('/edit-os21000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs21000'])->name('edit_os21000');
            Route::get('/edit-os22000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs22000'])->name('edit_os22000');
            Route::get('/edit-os23000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs23000'])->name('edit_os23000');
            Route::get('/edit-os24000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs24000'])->name('edit_os24000');
            Route::get('/edit-os25000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs25000'])->name('edit_os25000');
            Route::get('/edit-os26000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs26000'])->name('edit_os26000');
            Route::get('/edit-os27000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs27000'])->name('edit_os27000');
            Route::get('/edit-os28000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs28000'])->name('edit_os28000');
            Route::get('/edit-os29000/{kod}/{tahun}', [DbusPecahanController::class, 'editOs29000'])->name('edit_os29000');

            // Pecahan Updates (POST)
            Route::post('/pecahan/store', [DbusPecahanController::class, 'storePegawai'])->name('pecahan.store');
            Route::post('/update-ol14101', [DbusPecahanController::class, 'updateOt'])->name('update_ol14101');
            Route::post('/update-os15000', [DbusPecahanController::class, 'updateOs15'])->name('update_os15000');
            Route::post('/update-os21000', [DbusPecahanController::class, 'updateOs21000'])->name('update_os21000');
            Route::post('/update-os22000', [DbusPecahanController::class, 'updateOs22000'])->name('update_os22000');
            Route::post('/update-os23000', [DbusPecahanController::class, 'updateOs23000'])->name('update_os23000');
            Route::post('/update-os24000', [DbusPecahanController::class, 'updateOs24000'])->name('update_os24000');
            Route::post('/update-os25000', [DbusPecahanController::class, 'updateOs25000'])->name('update_os25000');
            Route::post('/update-os26000', [DbusPecahanController::class, 'updateOs26000'])->name('update_os26000');
            Route::post('/update-os27000', [DbusPecahanController::class, 'updateOs27000'])->name('update_os27000');
            Route::post('/update-os28000', [DbusPecahanController::class, 'updateOs28000'])->name('update_os28000');
            Route::post('/update-os29000', [DbusPecahanController::class, 'updateOs29000'])->name('update_os29000');
        });

        // ROUTE LAPORAN PRESTASI
        Route::prefix('laporan-prestasi')->name('laporan_prestasi.')->group(function () {
            Route::get('/', [LaporanPPUUNController::class, 'index'])->name('index');
            Route::get('/cetak', [LaporanPPUUNController::class, 'cetak'])->name('cetak');
            Route::get('/daftar', [LaporanPPUUNController::class, 'create'])->name('create');
            Route::post('/simpan', [LaporanPPUUNController::class, 'store'])->name('store');
        });
    });

    // --- KEWANGAN ---
    Route::prefix('kewangan')->name('kewangan.')->group(function () {
        Route::get('/', [KewanganController::class, 'index'])->name('index');
        Route::get('/create', [KewanganController::class, 'create'])->name('create');
        Route::post('/', [KewanganController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KewanganController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KewanganController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [KewanganController::class, 'destroy'])->name('destroy');
        Route::get('/suku-tahun', [KewanganController::class, 'sukuTahun'])->name('suku_tahun');
        Route::get('/perbandingan', [KewanganController::class, 'perbandingan'])->name('perbandingan');
        Route::get('/cetak-pdf-bulanan', [KewanganController::class, 'cetakPdfBulanan'])->name('cetak_pdf_bulanan');
        Route::get('/cetak-pdf-suku', [KewanganController::class, 'cetakPdfSuku'])->name('cetak_pdf_suku');
    });

    // --- GUAMAN ---
    Route::prefix('guaman')->name('guaman.')->group(function () {
        Route::get('/', [GuamanController::class, 'index'])->name('index');
        Route::get('/create', [GuamanController::class, 'create'])->name('create');
        Route::post('/', [GuamanController::class, 'store'])->name('store');
        Route::get('/{guaman_case}/edit', [GuamanController::class, 'edit'])->name('edit');
        Route::put('/{guaman_case}', [GuamanController::class, 'update'])->name('update');
        Route::get('/cetak-laporan-pdf', [GuamanController::class, 'cetakLaporanPdf'])->name('cetak_laporan_pdf');
    });

    // --- LAPORAN & RESOURCE ---
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    Route::prefix('lampiran')->name('lampiran.')->group(function () {
        Route::get('/', [LampiranKesMahkamahController::class, 'index'])->name('index');
        Route::post('/', [LampiranKesMahkamahController::class, 'store'])->name('store');
    });

    // Resource Routes untuk Modul (CRUD Standard)
    Route::resources([
        'laporanpandanganundang' => LaporanPandanganUndangController::class,
        'laporankesmahkamah' => LaporanKesMahkamahController::class,
        'laporangubalanundang' => LaporanGubalanUndangController::class,
        'laporanpindaanundang' => LaporanPindaanUndangController::class,
        'laporansemakanundang' => LaporanSemakanUndangController::class,
        'laporanmesyuarat' => LaporanMesyuaratController::class,
        'kestatatertib' => KestatatertibController::class,
        'lainlaintugasan' => LaporanLainLainController::class,
    ]);

    // --- TETAPAN ---
    Route::prefix('tetapan')->group(function () {
        Route::get('/agensi', [AgensiController::class, 'index'])->name('agensi.index');
        Route::post('/agensi', [AgensiController::class, 'store'])->name('agensi.store');
        Route::delete('/agensi/{id}', [AgensiController::class, 'destroy'])->name('agensi.destroy');
        Route::prefix('pengguna')->name('tetapan.pengguna.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/register', [UserController::class, 'create'])->name('register');
    Route::post('/register', [UserController::class, 'store'])->name('register.store');
});

require __DIR__.'/auth.php';