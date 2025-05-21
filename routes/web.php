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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/laporan', [LaporanController::class, 'index'])->middleware('auth')->name('laporan.index');
Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->middleware('auth')->name('laporanbulanan.index');

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
Route::get('/pergerakan/create', fn() => Auth::check()
    ? app(PergerakanController::class)->create()
    : redirect()->route('login')->with('error', 'Sila log masuk dahulu.'))->name('pergerakan.create');

Route::post('/pergerakan', fn(Request $request) => Auth::check()
    ? app(PergerakanController::class)->store($request)
    : redirect()->route('login')->with('error', 'Sila log masuk dahulu.'))->name('pergerakan.store');

Route::resource('pergerakan', PergerakanController::class)->middleware('auth');

// ===================== DASHBOARD PER PERANAN =====================
Route::middleware(['auth', 'role:boss'])->get('/boss/dashboard', [DashboardController::class, 'boss'])->name('boss.dashboard');
Route::middleware(['auth', 'role:pa'])->get('/pa/dashboard', [DashboardController::class, 'pa'])->name('pa.dashboard');
Route::middleware(['auth', 'role:yb'])->get('/yb/dashboard', [DashboardController::class, 'yb'])->name('yb.dashboard');
Route::middleware(['auth', 'role:user'])->get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

// ===================== MODUL UTAMA =====================
Route::middleware('auth')->group(function () {

    // ✅ Cetakan PDF rasmi
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    // ✅ Modul Tatatertib
    Route::resource('kestatatertib', KestatatertibController::class);

    // ✅ Modul lain-lain laporan
    Route::resource('laporanpandanganundang', LaporanPandanganUndangController::class);
    Route::resource('laporankesmahkamah', LaporanKesMahkamahController::class);
    Route::resource('laporangubalanundang', LaporanGubalanUndangController::class);
    Route::resource('laporanpindaanundang', LaporanPindaanUndangController::class);
    Route::resource('laporansemakanundang', LaporanSemakanUndangController::class);
    Route::resource('laporanmesyuarat', LaporanMesyuaratController::class);
    Route::resource('lainlaintugasan', LaporanLainLainController::class);

});

// ===================== AUTH (LOGIN, REGISTER, etc.) =====================
require __DIR__.'/auth.php';
