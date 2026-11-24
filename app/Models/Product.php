<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** Stok <= angka ini dianggap "stok kritis" */
    public const LOW_STOCK = 10;

    protected $fillable = [
        'vendor_id', 'category_id', 'product_name', 'description',
        'price', 'stock', 'weight_gram', 'image', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean', 'price' => 'decimal:2'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getIsLowAttribute(): bool
    {
        return $this->stock <= self::LOW_STOCK;
    }
}
