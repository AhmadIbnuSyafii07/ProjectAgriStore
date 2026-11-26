<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $vid  = auth()->id();
        $base = fn () => Order::where('vendor_id', $vid);

        $stats = [
            'new_orders'      => $base()->whereIn('order_status', ['pending_payment', 'processing'])->count(),
            'active_products' => Product::where('vendor_id', $vid)->where('is_active', true)->count(),
            'low_stock'       => Product::where('vendor_id', $vid)->where('is_active', true)
                                    ->where('stock', '<=', Product::LOW_STOCK)->count(),
            'month_revenue'   => $base()->where('payment_status', 'paid')
                                    ->whereYear('created_at', now()->year)
                                    ->whereMonth('created_at', now()->month)
                                    ->sum('grand_total'),
        ];

        // Penjualan 7 hari terakhir (jumlah pesanan per hari)
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $start    = now()->subDays(6)->startOfDay();
        $recent   = $base()->where('created_at', '>=', $start)->get(['id', 'created_at']);
        $weekly   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $weekly[$dayNames[$day->dayOfWeek]] = $recent->filter(fn ($o) => $o->created_at->isSameDay($day))->count();
        }

        // Status pesanan (donut)
        $counts = $base()->selectRaw('order_status, count(*) as total')->groupBy('order_status')->pluck('total', 'order_status');
        $status = [
            'processed' => (int) ($counts['processing'] ?? 0),
            'shipped'   => (int) ($counts['shipped'] ?? 0),
            'pending'   => (int) ($counts['pending_payment'] ?? 0),
        ];

        // Pendapatan per bulan tahun ini (juta rupiah), hanya pembayaran Valid
        $paid = $base()->where('payment_status', 'paid')->whereYear('created_at', now()->year)->get(['grand_total', 'created_at']);
        $monthNames = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthly = [];
        foreach ($monthNames as $n => $label) {
            $sum = $paid->filter(fn ($o) => $o->created_at->month === $n)->sum('grand_total');
            $monthly[$label] = round($sum / 1000000, 1);
        }

        $orders = $base()->with(['buyer', 'items.product'])->latest()->take(5)->get();

        $lowStockItems = Product::where('vendor_id', $vid)->where('is_active', true)
            ->where('stock', '<=', Product::LOW_STOCK)->orderBy('stock')->take(4)->get();

        return view('seller.dashboard', compact('stats', 'weekly', 'status', 'monthly', 'orders', 'lowStockItems'));
    }
}
