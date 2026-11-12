<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder menu sidebar (halaman dibuat nanti)
    foreach (['toko', 'produk', 'pengguna', 'transaksi', 'konfigurasi', 'laporan'] as $page) {
        Route::get("/$page", fn () => redirect()->route('admin.dashboard'))->name($page);
    }
});

// ===== Punya Della: dashboard penjual =====
Route::get('/seller/dashboard', function () {
    return view('seller.dashboard');
});
