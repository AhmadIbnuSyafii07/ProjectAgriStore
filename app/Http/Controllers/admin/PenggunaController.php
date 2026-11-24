<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PenggunaController extends Controller
{
    public function index()
    {
        // Sengaja dikosongkan dulu: data pengguna nanti terhubung ke role lain
        // (pembeli, penjual/vendor, super admin). Ganti variabel di bawah dengan query-nya.
        $pengguna = collect();

        $ringkasan = [
            'total'        => 0,  // semua pengguna (pembeli & vendor)
            'aktif'        => 0,  // aktif hari ini
            'baru'         => 0,  // bergabung 7 hari terakhir
            'ditangguhkan' => 0,  // akun disuspend
        ];

        $peran = ['Pembeli', 'Penjual/Vendor', 'Super Admin'];

        return view('admin.pengguna.index', compact('pengguna', 'ringkasan', 'peran'));
    }
}