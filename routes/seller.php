<?php

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\ReportController;
use App\Http\Controllers\Seller\StockController;
use App\Models\User;
use Database\Seeders\SellerDemoSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
- Semua route modul Penjual. Dimuat dari routes/web.php dengan:
-     require __DIR__.'/seller.php';
 */
      
/*
- Middleware khusus penjual (ditaruh di sini sementara).
- Nanti bisa dipindah ke app/Http/Middleware/.
 */
if (! class_exists('EnsureUserIsVendor')) {
    class EnsureUserIsVendor
    {
        public function handle(Request $request, \Closure $next)
        {
            abort_unless(
                $request->user() && $request->user()->role === 'vendor',
                403,
                'Halaman ini khusus akun penjual.'
            );

            return $next($request);
        }
    }
}

Route::redirect('/seller', '/seller/dashboard');

/*
- ===== LOGIN SEMENTARA (HANYA UNTUK LOCAL) =====
- Aktif hanya jika APP_ENV=local DAN route 'login' belum ada.
- Buka /login -> otomatis menjalankan SellerDemoSeeder jika akun belum ada,
  lalu masuk sebagai penjual contoh.
- HAPUS blok ini setelah login asli tersedia.
 */
if (app()->environment('local') && ! Route::has('login')) {
    Route::get('/login', function () {
        $vendor = User::where('email', 'vendor@sawitsupply.test')->first();

        if (! $vendor && class_exists(SellerDemoSeeder::class)) {
            Artisan::call('db:seed', ['--class' => SellerDemoSeeder::class]);
            $vendor = User::where('email', 'vendor@sawitsupply.test')->first();
        }

        if (! $vendor) {
            $vendor = User::firstOrCreate(
                ['email' => 'vendor@sawitsupply.test'],
                [
                    'name' => 'Penjual Demo',
                    'password' => Hash::make('password'),
                    'role' => 'vendor',
                ]
            );
        }

        Auth::login($vendor);

        return redirect()->route('seller.dashboard');
    })->name('login');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
}

Route::middleware(['auth', EnsureUserIsVendor::class])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', ProductController::class)->except('show');
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
        Route::patch('orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');

        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::patch('stock/{product}', [StockController::class, 'update'])->name('stock.update');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });