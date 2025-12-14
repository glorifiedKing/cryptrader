<?php

namespace App\Models;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'status',
        'locked_value',
    ];

    protected function casts(): array
    {
        return [
            'side' => OrderSide::class,
            'status' => OrderStatus::class,
            'price' => 'decimal:8',
            'amount' => 'decimal:8',
            'locked_value' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('status', OrderStatus::Open);
    }

    public function scopeForSymbol(Builder $query, string $symbol): void
    {
        $query->where('symbol', $symbol);
    }

    public function scopeBuys(Builder $query): void
    {
        $query->where('side', OrderSide::Buy);
    }

    public function scopeSells(Builder $query): void
    {
        $query->where('side', OrderSide::Sell);
    }
}
