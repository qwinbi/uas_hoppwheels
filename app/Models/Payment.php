<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'va_number',
        'qris_image',
        'amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getQrisImageUrlAttribute()
    {
        return $this->qris_image ? asset('storage/' . $this->qris_image) : null;
    }
}