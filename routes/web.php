<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controller
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

// ===================== UTAMA =====================
Route::get('/', fn() => redirect()->route('dashboard'))->name('utama');

// ✅ Dashboard tunggal untuk semua role termasuk super_admin
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ===================== PROFIL =====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// ===================== LOGOUT =====================
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ===================== PERGERAKAN =====================
Route::middleware('auth')->group(function () {
    Route::get('/pergerakan/create', [PergerakanController::class, 'create'])->name('pergerakan.create');
    Route::post('/pergerakan', [PergerakanController::class, 'store'])->name('pergerakan.store');
    Route::resource('pergerakan', PergerakanController::class);
});

// ===================== MODUL UTAMA =====================
Route::middleware('auth')->group(function () {

    // ✅ Paparan dan cetakan laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    // ✅ Laporan Individu (resource-based)
    Route::resource('laporanpandanganundang', LaporanPandanganUndangController::class);
    Route::resource('laporankesmahkamah', LaporanKesMahkamahController::class);
    Route::resource('laporangubalanundang', LaporanGubalanUndangController::class);
    Route::resource('laporanpindaanundang', LaporanPindaanUndangController::class);
    Route::resource('laporansemakanundang', LaporanSemakanUndangController::class);
    Route::resource('laporanmesyuarat', LaporanMesyuaratController::class);
    Route::resource('kestatatertib', KestatatertibController::class);
    Route::resource('lainlaintugasan', LaporanLainLainController::class);

    // ✅ Laporan Bulanan (jika digunakan)
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporanbulanan.index');
});

// ===================== AUTH (LOGIN, REGISTER, etc.) =====================
require __DIR__.'/auth.php';
