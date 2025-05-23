<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanBulananController;
use App\Http\Controllers\LaporanPandanganUndangController;
use App\Http\Controllers\LaporanKesMahkamahController;
use App\Http\Controllers\LaporanGubalanUndangController;
use App\Http\Controllers\LaporanPindaanUndangController;
use App\Http\Controllers\LaporanSemakanUndangController;
use App\Http\Controllers\LaporanMesyuaratController;
use App\Http\Controllers\KestatatertibController;
use App\Http\Controllers\LaporanLainLainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PergerakanController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

// ===================== UTAMA =====================
Route::get('/', fn() => redirect()->route('dashboard'))->name('utama');

// ===================== RESET PASSWORD (Manual Triggered) =====================
Route::middleware('guest')->group(function () {
    Route::get('/reset-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/reset-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
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

// ===================== MODUL LAPORAN =====================
Route::middleware('auth')->group(function () {

    // Ringkasan laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    // Laporan individu
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

    // Laporan Bulanan (optional)
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporanbulanan.index');
});

// ===================== AUTH ROUTES (Laravel Breeze/Auth scaffolding) =====================
require __DIR__.'/auth.php';
