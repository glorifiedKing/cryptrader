<?php

namespace App\Actions;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateSellOrderAction
{
    public function execute(User $user, string $symbol, float $price, float $amount): Order
    {
        return DB::transaction(function () use ($user, $symbol, $price, $amount) {
            // Lock asset for update to prevent race conditions
            $asset = Asset::where('user_id', $user->id)
                ->where('symbol', $symbol)
                ->lockForUpdate()
                ->first();

            if (!$asset || $asset->amount < $amount) {
                throw new \Exception('Insufficient asset balance');
            }

            // Move amount from available to locked
            $asset->decrement('amount', $amount);
            $asset->increment('locked_amount', $amount);


            return Order::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'side' => OrderSide::Sell,
                'price' => $price,
                'amount' => $amount,
                'status' => OrderStatus::Open,
                'locked_value' => $amount,
            ]);
        });
    }
}
