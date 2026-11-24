<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const ORDER_STATUS = [
        'pending_payment' => 'Menunggu Pembayaran',
        'processing'      => 'Diproses',
        'shipped'         => 'Dikirim',
        'completed'       => 'Selesai',
        'cancelled'       => 'Dibatalkan',
    ];

    public const PAYMENT_STATUS = [
        'unpaid'   => 'Belum Bayar',
        'waiting'  => 'Menunggu Verifikasi',
        'paid'     => 'Valid',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'order_number', 'buyer_id', 'vendor_id', 'grand_total',
        'order_status', 'payment_status', 'tracking_number',
    ];

    protected $casts = ['grand_total' => 'decimal:2'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getOrderLabelAttribute(): string
    {
        return self::ORDER_STATUS[$this->order_status] ?? $this->order_status;
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_STATUS[$this->payment_status] ?? $this->payment_status;
    }

    /** Ringkasan produk untuk tabel: "Pupuk NPK +2 lainnya" */
    public function getProductSummaryAttribute(): string
    {
        $first = $this->items->first()?->product?->product_name ?? '-';
        $more  = $this->items->count() - 1;

        return $more > 0 ? "{$first} +{$more} lainnya" : $first;
    }
}
