<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    /** Nilai awal. Dipakai selama pengaturan belum pernah disimpan. */
    public const DEFAULT = [
        'nama_platform'     => 'SawitSupply',
        'email_dukungan'    => 'support@sawitsupply.com',
        'telepon'           => '',
        'alamat'            => '',
        'min_transaksi'     => '10000',
        'biaya_penanganan'  => '2',
        'batas_konfirmasi'  => '24',
        'mode_pemeliharaan' => '0',
        'bayar_va'          => '1',
        'bayar_ewallet'     => '1',
        'bayar_transfer'    => '1',
        'fitur_kalkulator'  => '1',
        'fitur_stok_kritis' => '1',
        'aman_captcha'      => '0',
        'aman_ip'           => '0',
        'aman_backup'       => '1',
    ];

    /** Semua pengaturan (default + yang sudah tersimpan). */
    public static function semua(): array
    {
        $tersimpan = array_filter(static::pluck('nilai', 'kunci')->all(), fn ($v) => $v !== null);

        return array_merge(self::DEFAULT, $tersimpan);
    }

    /** Ambil satu pengaturan. Contoh: Pengaturan::ambil('nama_platform') */
    public static function ambil(string $kunci, $default = null)
    {
        return static::semua()[$kunci] ?? $default;
    }

    public static function simpan(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            static::updateOrCreate(['kunci' => $kunci], ['nilai' => (string) $nilai]);
        }
    }
}
