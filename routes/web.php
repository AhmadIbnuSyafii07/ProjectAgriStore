<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TokoController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\LaporanController;

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

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk');

    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');

    Route::get('/konfigurasi', [KonfigurasiController::class, 'index'])->name('konfigurasi');
    Route::put('/konfigurasi', [KonfigurasiController::class, 'update'])->name('konfigurasi.update');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    
    // Placeholder menu lain (halaman dibuat nanti)
    foreach (['transaksi', 'laporan'] as $page) {
            Route::get("/$page", fn () => redirect()->route('admin.dashboard'))->name($page);
        }
    });

require __DIR__.'/seller.php';