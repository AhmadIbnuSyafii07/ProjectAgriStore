<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class KonfigurasiController extends Controller
{
    /** Pengaturan berbentuk saklar (aktif/nonaktif) */
    private const SAKLAR = [
        'mode_pemeliharaan',
        'bayar_va', 'bayar_ewallet', 'bayar_transfer',
        'fitur_kalkulator', 'fitur_stok_kritis',
        'aman_captcha', 'aman_ip', 'aman_backup',
    ];

    public function index()
    {
        $p = Pengaturan::semua();
        $versi = 'v1.0.0';

        // Master data: sengaja dikosongkan dulu, nanti terhubung ke penjual.
        // Ganti dengan query tabel kategori / satuan kalau sudah ada.
        $kategori = collect();
        $satuan = collect();

        return view('admin.konfigurasi.index', compact('p', 'versi', 'kategori', 'satuan'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_platform'    => ['required', 'string', 'max:100'],
            'email_dukungan'   => ['nullable', 'email', 'max:100'],
            'telepon'          => ['nullable', 'string', 'max:30'],
            'alamat'           => ['nullable', 'string', 'max:255'],
            'min_transaksi'    => ['required', 'integer', 'min:0', 'max:1000000000'],
            'biaya_penanganan' => ['required', 'numeric', 'min:0', 'max:100'],
            'batas_konfirmasi' => ['required', 'integer', 'min:1', 'max:720'],
        ], [
            'required' => ':attribute wajib diisi.',
            'email'    => 'Format email tidak valid.',
            'integer'  => ':attribute harus berupa angka bulat.',
            'numeric'  => ':attribute harus berupa angka.',
            'min'      => ':attribute tidak boleh kurang dari :min.',
            'max'      => ':attribute melebihi batas yang diizinkan.',
        ], [
            'nama_platform'    => 'Nama platform',
            'email_dukungan'   => 'Email dukungan',
            'min_transaksi'    => 'Minimal transaksi',
            'biaya_penanganan' => 'Biaya penanganan',
            'batas_konfirmasi' => 'Batas waktu konfirmasi',
        ]);

        // Saklar yang tidak dicentang tidak dikirim browser, jadi dibaca sebagai "mati".
        foreach (self::SAKLAR as $kunci) {
            $data[$kunci] = $request->boolean($kunci) ? '1' : '0';
        }

        Pengaturan::simpan($data);

        return redirect()->route('admin.konfigurasi')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
