<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'counter_order_id',
        'buyer_id',
        'seller_id',
        'symbol',
        'price',
        'amount',
        'total_value',
        'commission',
        'commission_paid_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'amount' => 'decimal:8',
            'total_value' => 'decimal:8',
            'commission' => 'decimal:8',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function counterOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'counter_order_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
