<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// ===================== CONTROLLERS =====================
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanPandanganUndangController;
use App\Http\Controllers\LaporanKesMahkamahController;
use App\Http\Controllers\LaporanGubalanUndangController;
use App\Http\Controllers\LaporanPindaanUndangController;
use App\Http\Controllers\LaporanSemakanUndangController;
use App\Http\Controllers\LaporanMesyuaratController;
use App\Http\Controllers\KestatatertibController;
use App\Http\Controllers\LaporanLainLainController;
use App\Http\Controllers\LaporanBulananController;
use App\Http\Controllers\PergerakanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\LampiranKesMahkamahController;
use App\Http\Controllers\CustomPasswordResetController;
use App\Http\Controllers\PentadbiranController;
use App\Http\Controllers\KewanganController;

// ===================== ROOT REDIRECT =====================
Route::get('/', fn() => redirect()->route('dashboard'))->name('utama');

// ===================== RESET PASSWORD (Default) =====================
Route::middleware('guest')->group(function () {
    Route::get('/reset-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/reset-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
});

// ===================== RESET PASSWORD (Custom) =====================
Route::middleware('guest')->group(function () {
    Route::get('/custom-forgot-password', function () {
        return view('auth.check-user');
    })->name('custom.password.request');

    Route::post('/custom-verify', [CustomPasswordResetController::class, 'verifyUser'])->name('custom.password.verify');
    Route::get('/custom-reset-password/{email}', [CustomPasswordResetController::class, 'showResetForm'])->name('custom.password.form');
    Route::post('/custom-reset-password', [CustomPasswordResetController::class, 'updatePassword'])->name('custom.password.update');
});

// ===================== DASHBOARD =====================
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ===================== PROFIL PENGGUNA =====================
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
});

// ===================== LOGOUT =====================
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ===================== PERGERAKAN PEGAWAI =====================
Route::middleware('auth')->prefix('pergerakan')->name('pergerakan.')->group(function () {
    Route::get('/create', [PergerakanController::class, 'create'])->name('create');
    Route::post('/', [PergerakanController::class, 'store'])->name('store');
    Route::resource('/', PergerakanController::class)->parameters(['' => 'pergerakan']);
});

// ===================== MODUL PENTADBIRAN =====================
Route::middleware('auth')->group(function () {
    Route::resource('/pentadbiran', PentadbiranController::class);
    // ⚠️ SAYA DAH PADAM 'Route::resource' KEWANGAN DARI SINI SEBAB IA BERGADUH DENGAN BAWAH
});

// ===================== MODUL KEWANGAN (MANUAL ROUTING) =====================
Route::middleware('auth')->prefix('kewangan')->name('kewangan.')->group(function () {
    
    // --- 1. HALAMAN KHAS (Letak di ATAS supaya tidak dianggap ID) ---
    Route::get('/suku-tahun', [KewanganController::class, 'sukuTahun'])->name('suku_tahun');
    Route::get('/perbandingan', [KewanganController::class, 'perbandingan'])->name('perbandingan');
    Route::get('/waran', [KewanganController::class, 'waran'])->name('waran');
    Route::get('/prestasi', [KewanganController::class, 'prestasi'])->name('prestasi');
    
    // --- 2. HALAMAN UTAMA ---
    Route::get('/', [KewanganController::class, 'index'])->name('index');

    // --- 3. CRUD (Create, Store, Edit, Update, Destroy) ---
    Route::get('/create', [KewanganController::class, 'create'])->name('create');
    Route::post('/', [KewanganController::class, 'store'])->name('store');
    
    // Route yang ada {id} mesti duduk paling BAWAH
    Route::get('/{id}/edit', [KewanganController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KewanganController::class, 'update'])->name('update');
    Route::delete('/{id}', [KewanganController::class, 'destroy'])->name('destroy');

// ROUTE UTK PDF (Letak di bahagian atas bersama route spesifik lain)
Route::get('/pdf/{type}', [KewanganController::class, 'exportPdf'])->name('export_pdf');
});
Route::get('/kewangan/cetak-pdf', [KewanganController::class, 'cetakPdf'])
    ->name('kewangan.cetak_pdf');

// ===================== MODUL LAPORAN =====================
Route::middleware('auth')->group(function () {
    // === RINGKASAN LAPORAN & PDF ===
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    // === LAMPIRAN AM ===
    Route::get('/laporan/lampiran', [LaporanController::class, 'lampiranForm'])->name('lampiran.form');
    Route::post('/laporan/lampiran-simpan', [LaporanController::class, 'simpanLampiran'])->name('lampiran.simpan');

    // === LAMPIRAN II ===
    Route::prefix('lampiran')->name('lampiran.')->middleware('auth')->group(function () {
        Route::get('/', [LampiranKesMahkamahController::class, 'index'])->name('index');
        Route::post('/simpan', [LampiranKesMahkamahController::class, 'store'])->name('store');
    });

    // === LAPORAN INDIVIDU ===
    Route::resources([
        'laporanpandanganundang' => LaporanPandanganUndangController::class,
        'laporankesmahkamah'     => LaporanKesMahkamahController::class,
        'laporangubalanundang'   => LaporanGubalanUndangController::class,
        'laporanpindaanundang'   => LaporanPindaanUndangController::class,
        'laporansemakanundang'   => LaporanSemakanUndangController::class,
        'laporanmesyuarat'       => LaporanMesyuaratController::class,
        'kestatatertib'          => KestatatertibController::class,
        'lainlaintugasan'        => LaporanLainLainController::class,
    ]);

    // === LAPORAN BULANAN ===
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporanbulanan.index');
});

require __DIR__.'/auth.php';