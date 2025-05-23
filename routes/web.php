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
use App\Http\Controllers\ResetPasswordManualController;

/*
|--------------------------------------------------------------------------
| Laman Utama & Reset Manual
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'))->name('utama');

Route::get('/reset-password', [ResetPasswordManualController::class, 'showForm'])->name('reset.manual');
Route::post('/reset-password', [ResetPasswordManualController::class, 'updatePassword'])->name('reset.manual.update');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profil Pengguna
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Pergerakan Pegawai
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('pergerakan')->group(function () {
    Route::get('/create', [PergerakanController::class, 'create'])->name('pergerakan.create');
    Route::post('/', [PergerakanController::class, 'store'])->name('pergerakan.store');
    Route::resource('/', PergerakanController::class)->parameters(['' => 'pergerakan']);
});

/*
|--------------------------------------------------------------------------
| Modul Laporan
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Paparan Umum
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [PdfController::class, 'laporan'])->name('laporan.pdf');

    // Laporan Individu
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

    // Laporan Bulanan
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporanbulanan.index');
});

/*
|--------------------------------------------------------------------------
| Laravel Auth Routes (Login/Register/Forgot)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
