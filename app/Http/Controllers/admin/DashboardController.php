<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Data dummy dulu. Nanti ganti dengan query ke database.
        $stats = [
            ['title' => 'Toko',      'value' => '15',        'label' => 'Total Toko Terdaftar', 'note' => '3 Menunggu',       'icon' => 'store', 'color' => 'cyan',   'spark' => [10, 14, 12, 20, 16, 26, 22, 30]],
            ['title' => 'Produk',    'value' => '1.250',     'label' => 'Total Produk',         'note' => 'Katalog Aktif',    'icon' => 'box',   'color' => 'green',  'spark' => [8, 12, 18, 14, 22, 20, 28, 32]],
            ['title' => 'Pengguna',  'value' => '500',       'label' => 'Total Pengguna',       'note' => 'Pembeli & Vendor', 'icon' => 'users', 'color' => 'blue',   'spark' => [12, 10, 18, 16, 24, 22, 26, 34]],
            ['title' => 'Transaksi', 'value' => 'Rp 150M',   'label' => 'Total Transaksi',      'note' => 'Volume Global',    'icon' => 'card',  'color' => 'orange', 'spark' => [6, 14, 10, 22, 18, 24, 30, 36]],
        ];
        foreach ($stats as &$s) {
            $s['path'] = $this->smoothPath($s['spark'], 100, 40, 36);
        }
        unset($s);

        $pendingStores = [
            ['nama' => 'Toko Sawit Makmur',      'email' => 'admin@sawitmakmur.com', 'tanggal' => '25 Okt 2026'],
            ['nama' => 'CV Tani Subur',          'email' => 'contact@tanisubur.id',  'tanggal' => '26 Okt 2026'],
            ['nama' => 'Distributor Pupuk Riau', 'email' => 'sales@pupukriau.com',   'tanggal' => '27 Okt 2026'],
        ];

        // Nilai grafik (Jan - Okt), skala 0-100
        $monthly = ['Jan' => 22, 'Feb' => 40, 'Mar' => 30, 'Apr' => 54, 'Mei' => 48, 'Jun' => 64, 'Jul' => 58, 'Agt' => 74, 'Sep' => 68, 'Okt' => 92];
        $chart = $this->smoothPath(array_values($monthly), 400, 200, 100);

        $transactions = [
            ['invoice' => 'INV-20261027-01', 'pembeli' => 'Pak Roni',   'vendor' => 'Toko Sawit...',  'total' => 'Rp 1.450.000', 'status' => 'Paid'],
            ['invoice' => 'INV-20261027-02', 'pembeli' => 'Bpk. Ahmad', 'vendor' => 'CV Tani...',     'total' => 'Rp 350.000',   'status' => 'Shipped'],
            ['invoice' => 'INV-20261027-03', 'pembeli' => 'Kebun Jaya', 'vendor' => 'Distributor...', 'total' => 'Rp 8.200.000', 'status' => 'Processing'],
            ['invoice' => 'INV-20261026-08', 'pembeli' => 'H. Budi',    'vendor' => 'Toko Sawit...',  'total' => 'Rp 720.000',   'status' => 'Completed'],
            ['invoice' => 'INV-20261026-07', 'pembeli' => 'Petani A',   'vendor' => 'CV Tani...',     'total' => 'Rp 180.000',   'status' => 'Pending'],
        ];

        return view('admin.dashboard', compact('stats', 'pendingStores', 'monthly', 'chart', 'transactions'));
    }

    /**
     * Ubah deretan angka jadi path SVG yang melengkung (untuk grafik area & sparkline).
     * $max = nilai tertinggi pada skala data, supaya tinggi grafik konsisten.
     */
    private function smoothPath(array $values, float $w, float $h, float $max): array
    {
        $n = count($values);
        $pts = [];
        foreach ($values as $i => $v) {
            $pts[] = [$i * $w / ($n - 1), $h - ($v / $max) * ($h - 8)];
        }

        $line = sprintf('M%.1f,%.1f', $pts[0][0], $pts[0][1]);
        for ($i = 0; $i < $n - 1; $i++) {
            $p0 = $pts[max($i - 1, 0)];
            $p1 = $pts[$i];
            $p2 = $pts[$i + 1];
            $p3 = $pts[min($i + 2, $n - 1)];
            $line .= sprintf(
                ' C%.1f,%.1f %.1f,%.1f %.1f,%.1f',
                $p1[0] + ($p2[0] - $p0[0]) / 6, $p1[1] + ($p2[1] - $p0[1]) / 6,
                $p2[0] - ($p3[0] - $p1[0]) / 6, $p2[1] - ($p3[1] - $p1[1]) / 6,
                $p2[0], $p2[1]
            );
        }

        return ['line' => $line, 'area' => sprintf('%s L%.1f,%.1f L0,%.1f Z', $line, $w, $h, $h)];
    }
}
