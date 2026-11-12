<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: ganti data dummy ini dengan query Eloquent (Order, Product, dll).
        $stats = [
            'new_orders'      => 12,
            'active_products' => 55,
            'low_stock'       => 3,
            'month_revenue'   => 15000000,
        ];

        // Penjualan 7 hari (Sen-Min)
        $weekly = [
            'Sen' => 8, 'Sel' => 14, 'Rab' => 10, 'Kam' => 18,
            'Jum' => 12, 'Sab' => 15, 'Min' => 7,
        ];

        // Status pesanan (persen) -> total 100
        $statusBreakdown = [
            'processed' => 45,
            'shipped'   => 30,
            'pending'   => 25,
        ];

        $orders = [
            ['code' => '#SS-001', 'buyer' => 'Budi Santoso', 'product' => 'Pupuk NPK 13-6-27', 'total' => 'Rp 300.000', 'status' => 'processed'],
            ['code' => '#SS-002', 'buyer' => 'Siti Aminah',  'product' => 'Pupuk NPK 13-6-27', 'total' => 'Rp 250.000', 'status' => 'processed'],
            ['code' => '#SS-003', 'buyer' => 'Andi Pratama', 'product' => 'Egrek Sawit',       'total' => 'Rp 450.000', 'status' => 'shipped'],
            ['code' => '#SS-004', 'buyer' => 'Rina Marlina', 'product' => 'Pupuk NPK 13-6-37', 'total' => 'Rp 500.000', 'status' => 'pending'],
            ['code' => '#SS-005', 'buyer' => 'Joko Widodo',  'product' => 'Dodos Sawit',       'total' => 'Rp 180.000', 'status' => 'pending'],
        ];

        $lowStockItems = [
            ['name' => 'Pupuk Urea 50kg',  'stock' => 4,  'max' => 40],
            ['name' => 'Egrek Sawit',      'stock' => 6,  'max' => 30],
            ['name' => 'Sarung Tangan',    'stock' => 9,  'max' => 60],
        ];

        // Penjualan bulanan (Jan-Des), satuan juta rupiah
        $monthly = [
            'Jan' => 9,  'Feb' => 11, 'Mar' => 14, 'Apr' => 12, 'Mei' => 15, 'Jun' => 13,
            'Jul' => 16, 'Agu' => 14, 'Sep' => 18, 'Okt' => 17, 'Nov' => 15, 'Des' => 20,
        ];

        return view('seller.dashboard', compact(
            'stats', 'weekly', 'statusBreakdown', 'orders', 'lowStockItems', 'monthly'
        ));
    }
}
