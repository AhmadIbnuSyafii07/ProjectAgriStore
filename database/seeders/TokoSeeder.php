<?php

namespace Database\Seeders;

use App\Models\Toko;
use Illuminate\Database\Seeder;

class TokoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Toko Sawit Makmur',      'Budi Santoso',   'admin@sawitmakmur.com', 'Pekanbaru', 'KTP, NIB (Lengkap)',            'menunggu', '2026-10-25'],
            ['CV Tani Subur',          'Hendra Wijaya',  'contact@tanisubur.id',  'Dumai',     'NIB, NPWP (Kurang Dokumen)',    'menunggu', '2026-10-26'],
            ['Agro Pupuk Nusantara',   'Siti Aminah',    'halo@agropupuk.co.id',  'Kampar',    'KTP, NIB, NPWP (Lengkap)',      'aktif',    '2026-10-24'],
            ['Distributor Pupuk Riau', 'Rudi Hartono',   'sales@pupukriau.com',   'Siak',      'KTP, NIB (Lengkap)',            'menunggu', '2026-10-27'],
            ['Sawit Jaya Abadi',       'Dedi Kurniawan', 'info@sawitjaya.id',     'Rokan Hulu', 'KTP (Kurang Dokumen)',         'ditolak',  '2026-10-20'],
            ['Tani Makmur Sejahtera',  'Rina Marlina',   'rina@tanimakmur.id',    'Pelalawan', 'KTP, NIB, NPWP (Lengkap)',      'aktif',    '2026-10-18'],
        ];

        foreach ($data as [$toko, $pemilik, $email, $wilayah, $berkas, $status, $tgl]) {
            Toko::insert([
                'nama_toko' => $toko, 'nama_pemilik' => $pemilik, 'email' => $email,
                'wilayah' => $wilayah, 'berkas' => $berkas, 'status' => $status,
                'created_at' => $tgl, 'updated_at' => $tgl,
            ]);
        }
    }
}
