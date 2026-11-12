<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Data dummy dulu. Nanti ganti dengan query ke database.
        $stats = [
            ['label' => 'Total Toko Terdaftar', 'value' => '15',            'note' => '3 Menunggu',      'icon' => 'store'],
            ['label' => 'Total Produk',         'value' => '1,250',         'note' => 'Katalog Aktif',   'icon' => 'box'],
            ['label' => 'Total Pengguna',       'value' => '500',           'note' => 'Pembeli & Vendor', 'icon' => 'users'],
            ['label' => 'Total Transaksi',      'value' => 'Rp 150.000.000', 'note' => 'Volume Global',   'icon' => 'cash'],
        ];

        $pendingStores = [
            ['nama' => 'Toko Sawit Makmur',      'email' => 'admin@sawitmakmur.com', 'tanggal' => '25 Okt 2026'],
            ['nama' => 'CV Tani Subur',          'email' => 'contact@tanisubur.id',  'tanggal' => '26 Okt 2026'],
            ['nama' => 'Distributor Pupuk Riau', 'email' => 'sales@pupukriau.com',   'tanggal' => '27 Okt 2026'],
        ];

        // Tinggi batang dalam persen (Jan - Okt)
        $monthly = [
            'Jan' => 38, 'Feb' => 48, 'Mar' => 33, 'Apr' => 55, 'Mei' => 65,
            'Jun' => 58, 'Jul' => 72, 'Agt' => 80, 'Sep' => 70, 'Okt' => 100,
        ];

        $transactions = [
            ['invoice' => 'INV-20261027-01', 'pembeli' => 'Pak Roni',     'vendor' => 'Toko Sawit...',  'total' => 'Rp 1.450.000', 'status' => 'Paid'],
            ['invoice' => 'INV-20261027-02', 'pembeli' => 'Bpk. Ahmad',   'vendor' => 'CV Tani...',     'total' => 'Rp 350.000',   'status' => 'Shipped'],
            ['invoice' => 'INV-20261027-03', 'pembeli' => 'Kebun Jaya',   'vendor' => 'Distributor...', 'total' => 'Rp 8.200.000', 'status' => 'Processing'],
            ['invoice' => 'INV-20261026-08', 'pembeli' => 'H. Budi',      'vendor' => 'Toko Sawit...',  'total' => 'Rp 720.000',   'status' => 'Completed'],
            ['invoice' => 'INV-20261026-07', 'pembeli' => 'Petani A',     'vendor' => 'CV Tani...',     'total' => 'Rp 180.000',   'status' => 'Pending'],
        ];

        return view('admin.dashboard', compact('stats', 'pendingStores', 'monthly', 'transactions'));
    }
}
