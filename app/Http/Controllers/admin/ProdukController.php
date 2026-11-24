<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProdukController extends Controller
{
    public function index()
    {
        // Sengaja dikosongkan dulu: data produk nanti berasal dari CRUD penjual (seller).
        // Kalau tabel produk sudah ada, ganti dua variabel di bawah dengan query-nya.
        $produk = collect();

        $ringkasan = [
            'total'   => 0,   // semua produk
            'aktif'   => 0,   // status aktif
            'menipis' => 0,   // stok menipis
            'draft'   => 0,   // draft / nonaktif
        ];

        $kategori = ['Pupuk', 'Alat Panen', 'Alat Semprot', 'Keselamatan Kerja'];

        return view('admin.produk.index', compact('produk', 'ringkasan', 'kategori'));
    }
}