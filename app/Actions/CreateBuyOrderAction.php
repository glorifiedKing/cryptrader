<?php

namespace App\Actions;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateBuyOrderAction
{
    public function execute(User $user, string $symbol, float $price, float $amount): Order
    {
        return DB::transaction(function () use ($user, $symbol, $price, $amount) {
            // Lock user for update to prevent race conditions
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            $totalCost = $price * $amount;

            if ($user->balance < $totalCost) {
                throw new \Exception('Insufficient balance');
            }

            $user->decrement('balance', $totalCost);


            return Order::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'side' => OrderSide::Buy,
                'price' => $price,
                'amount' => $amount,
                'status' => OrderStatus::Open,
                'locked_value' => $totalCost,
            ]);
        });
    }
}
