<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TokoController extends Controller
{
    /** Daftar toko + pencarian + filter + (opsional) data toko yang sedang diedit */
    public function index(Request $request)
    {
        $daftar = Toko::query()
            ->cari($request->q)
            ->when(
                array_key_exists((string) $request->status, Toko::STATUS),
                fn ($q) => $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $ringkasan = [
            'total'    => Toko::count(),
            'aktif'    => Toko::where('status', 'aktif')->count(),
            'menunggu' => Toko::where('status', 'menunggu')->count(),
            'ditolak'  => Toko::where('status', 'ditolak')->count(),
        ];

        // Klik tombol Edit => /admin/toko?edit=ID => form di kanan terisi otomatis
        $editToko = $request->filled('edit') ? Toko::find($request->edit) : null;

        return view('admin.toko.index', compact('daftar', 'ringkasan', 'editToko'));
    }

    /** Tambah toko */
    public function store(Request $request)
    {
        Toko::create($this->validasi($request));

        return redirect()->route('admin.toko')->with('success', 'Toko baru berhasil ditambahkan.');
    }

    /** Ubah data toko (termasuk ubah status lewat dropdown) */
    public function update(Request $request, Toko $toko)
    {
        $toko->update($this->validasi($request));

        return redirect()->route('admin.toko')->with('success', "Data toko {$toko->nama_toko} berhasil diperbarui.");
    }

    /** Setujui verifikasi => status aktif */
    public function setujui(Toko $toko)
    {
        $toko->update(['status' => 'aktif']);

        return back()->with('success', "Toko {$toko->nama_toko} disetujui dan sekarang aktif.");
    }

    /** Tolak verifikasi => status ditolak */
    public function tolak(Toko $toko)
    {
        $toko->update(['status' => 'ditolak']);

        return back()->with('success', "Verifikasi toko {$toko->nama_toko} ditolak.");
    }

    /** Hapus toko */
    public function destroy(Toko $toko)
    {
        $nama = $toko->nama_toko;
        $toko->delete();

        return redirect()->route('admin.toko')->with('success', "Toko {$nama} berhasil dihapus.");
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_toko'    => ['required', 'string', 'max:100'],
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'email'        => ['nullable', 'email', 'max:100'],
            'wilayah'      => ['required', 'string', 'max:100'],
            'berkas'       => ['nullable', 'string', 'max:150'],
            'status'       => ['required', Rule::in(array_keys(Toko::STATUS))],
        ], [
            'required' => ':attribute wajib diisi.',
            'email'    => 'Format email tidak valid.',
            'max'      => ':attribute maksimal :max karakter.',
            'in'       => 'Status tidak valid.',
        ], [
            'nama_toko'    => 'Nama toko',
            'nama_pemilik' => 'Nama pemilik',
            'wilayah'      => 'Wilayah',
            'status'       => 'Status',
        ]);
    }
}
