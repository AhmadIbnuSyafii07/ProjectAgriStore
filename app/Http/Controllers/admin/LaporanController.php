<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $this->tanggal($request->query('dari'), now()->startOfYear());
        $sampai = $this->tanggal($request->query('sampai'), now());

        $kategori = ['Pupuk', 'Alat Panen', 'Alat Semprot', 'Keselamatan Kerja'];

        // ---- Data dari tabel toko (milik admin) => sudah nyata ----
        $totalToko   = Toko::count();
        $vendorAktif = Toko::where('status', 'aktif')->count();

        // ---- Data yang bergantung role lain => dikosongkan dulu ----
        // Ganti nilai 0 / null di bawah dengan query transaksi & produk kalau modulnya sudah ada.
        $ringkasan = [
            'pendapatan'   => 0,     // total pendapatan platform (Rp)
            'pertumbuhan'  => 0,     // % dibanding bulan lalu
            'transaksi'    => 0,     // jumlah pesanan selesai
            'terlaris'     => null,  // nama produk terlaris
            'terjual'      => 0,     // jumlah terjual produk terlaris
            'vendor_aktif' => $vendorAktif,
            'total_toko'   => $totalToko,
        ];

        // Grafik per bulan (12 angka, Jan - Des)
        $grafik = [
            'bulan'      => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            'pendapatan' => array_fill(0, 12, 0),
            'transaksi'  => array_fill(0, 12, 0),
        ];

        // Donut: nilai penjualan per kategori
        $distribusi = array_map(fn ($k) => ['kategori' => $k, 'nilai' => 0], $kategori);

        // Rekap vendor: nama dari tabel toko, angka performa masih 0
        $vendor = Toko::where('status', 'aktif')
            ->orderBy('nama_toko')
            ->paginate(5)
            ->withQueryString()
            ->through(fn ($t) => (object) [
                'nama'    => $t->nama_toko,
                'wilayah' => $t->wilayah,
                'produk'  => 0,   // total produk vendor
                'pesanan' => 0,   // pesanan selesai
                'omzet'   => 0,   // total omzet (Rp)
                'status'  => $t->status,
            ]);

        $totalOmzet = 0;

        return view('admin.laporan.index', compact(
            'dari', 'sampai', 'kategori', 'ringkasan', 'grafik', 'distribusi', 'vendor', 'totalOmzet'
        ));
    }

    private function tanggal(?string $nilai, Carbon $default): Carbon
    {
        try {
            return $nilai ? Carbon::parse($nilai) : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
