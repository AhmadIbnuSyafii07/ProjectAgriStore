<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'payment_method', 'proof_file', 'payment_date'];

    protected $casts = ['payment_date' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_file ? asset('storage/' . $this->proof_file) : null;
    }
}
