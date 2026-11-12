<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';

    protected $fillable = ['nama_toko', 'nama_pemilik', 'email', 'wilayah', 'berkas', 'status'];

    public const STATUS = [
        'menunggu' => 'Menunggu Verifikasi',
        'aktif'    => 'Aktif',
        'ditolak'  => 'Ditolak',
    ];

    public function scopeCari($query, ?string $kata)
    {
        return $query->when($kata, function ($q) use ($kata) {
            $q->where(function ($w) use ($kata) {
                $w->where('nama_toko', 'like', "%{$kata}%")
                  ->orWhere('nama_pemilik', 'like', "%{$kata}%")
                  ->orWhere('email', 'like', "%{$kata}%")
                  ->orWhere('wilayah', 'like', "%{$kata}%");
            });
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
