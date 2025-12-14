<?php

namespace App\Actions;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function execute(Order $order): void
    {
        if ($order->status !== OrderStatus::Open) {
            throw new \Exception('Only open orders can be cancelled');
        }

        DB::transaction(function () use ($order) {
            if ($order->side === OrderSide::Buy) {
                // Release locked USD back to user balance
                $order->user()->lockForUpdate()->first()
                    ->increment('balance', $order->locked_value);
            } else {
                // Release locked assets back to available amount
                $asset = Asset::where('user_id', $order->user_id)
                    ->where('symbol', $order->symbol)
                    ->lockForUpdate()
                    ->first();

                if ($asset) {
                    $asset->decrement('locked_amount', $order->locked_value);
                    $asset->increment('amount', $order->locked_value);
                }
            }

            // Update order status
            $order->update(['status' => OrderStatus::Cancelled]);
        });
    }
}
