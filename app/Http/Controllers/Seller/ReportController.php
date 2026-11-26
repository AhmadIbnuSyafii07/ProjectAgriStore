<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = ($request->date('from') ?? now()->startOfMonth())->copy()->startOfDay();
        $to   = ($request->date('to') ?? now())->copy()->endOfDay();

        $orders = Order::with(['buyer', 'items.product'])
            ->where('vendor_id', auth()->id())
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->latest()->get();

        if ($request->export === 'csv') {
            return $this->csv($orders, $from, $to);
        }

        $summary = [
            'revenue' => $orders->sum('grand_total'),
            'orders'  => $orders->count(),
            'items'   => $orders->sum(fn ($o) => $o->items->sum('quantity')),
        ];

        $top = $orders->flatMap->items
            ->groupBy(fn ($i) => $i->product?->product_name ?? '-')
            ->map(fn ($rows) => $rows->sum('quantity'))
            ->sortDesc()->take(5);

        return view('seller.reports.index', compact('orders', 'summary', 'top', 'from', 'to'));
    }

    private function csv($orders, $from, $to)
    {
        $name = 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['No Pesanan', 'Tanggal', 'Pembeli', 'Produk', 'Jumlah Item', 'Total (Rp)']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->order_number,
                    $o->created_at->format('d/m/Y'),
                    $o->buyer?->name,
                    $o->product_summary,
                    $o->items->sum('quantity'),
                    (int) $o->grand_total,
                ]);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
