<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder menu sidebar (halaman dibuat nanti)
    foreach (['toko', 'produk', 'pengguna', 'transaksi', 'konfigurasi', 'laporan'] as $page) {
        Route::get("/$page", fn () => redirect()->route('admin.dashboard'))->name($page);
    }
});
