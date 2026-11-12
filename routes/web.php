<?php

<<<<<<< HEAD
=======
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TokoController;
>>>>>>> 6923da0b5b0ee7e00abd86398c00bd7738467be6
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Kelola Toko ----
    Route::get('/toko', [TokoController::class, 'index'])->name('toko');
    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::put('/toko/{toko}', [TokoController::class, 'update'])->name('toko.update');
    Route::patch('/toko/{toko}/setujui', [TokoController::class, 'setujui'])->name('toko.setujui');
    Route::patch('/toko/{toko}/tolak', [TokoController::class, 'tolak'])->name('toko.tolak');
    Route::delete('/toko/{toko}', [TokoController::class, 'destroy'])->name('toko.destroy');

    // Placeholder menu lain (halaman dibuat nanti)
    foreach (['produk', 'pengguna', 'transaksi', 'konfigurasi', 'laporan'] as $page) {
        Route::get("/$page", fn () => redirect()->route('admin.dashboard'))->name($page);
    }
});

// ===== Punya Della: dashboard penjual =====
Route::get('/seller/dashboard', function () {
    return view('seller.dashboard');
});
