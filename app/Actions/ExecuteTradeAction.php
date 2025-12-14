<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExecuteTradeAction
{
    public function execute(Order $buyOrder, Order $sellOrder): Trade
    {
        return DB::transaction(function () use ($buyOrder, $sellOrder) {
            // Lock both users and assets to prevent race conditions
            $buyer = User::where('id', $buyOrder->user_id)->lockForUpdate()->first();
            $seller = User::where('id', $sellOrder->user_id)->lockForUpdate()->first();

            // Lock seller's asset
            $sellerAsset = Asset::where('user_id', $seller->id)
                ->where('symbol', $buyOrder->symbol)
                ->lockForUpdate()
                ->first();

            // Get or create buyer's asset
            $buyerAsset = Asset::firstOrCreate(
                ['user_id' => $buyer->id, 'symbol' => $buyOrder->symbol],
                ['amount' => 0, 'locked_amount' => 0]
            );
            $buyerAsset = Asset::where('id', $buyerAsset->id)->lockForUpdate()->first();

            // Use the buy order's price for the trade
            $price = $buyOrder->price;
            $amount = $buyOrder->amount;
            $totalValue = $price * $amount;

            // Calculate commission
            $commissionRate = config('trading.commission_rate');
            $commission = $totalValue * $commissionRate;
            $commissionPaidBy = config('trading.commission_paid_by');

            // Transfer funds based on commission configuration
            if ($commissionPaidBy === 'buyer') {
                // Buyer already paid full amount, seller receives amount minus commission
                $seller->increment('balance', $totalValue - $commission);
            } elseif ($commissionPaidBy === 'seller') {
                // Seller receives full amount, but we deduct commission from asset transfer
                $seller->increment('balance', $totalValue);
                $amount = $amount * (1 - $commissionRate);
            } else {
                // Split commission
                $halfCommission = $commission / 2;
                $seller->increment('balance', $totalValue - $halfCommission);
                $amount = $amount * (1 - ($commissionRate / 2));
            }

            // Transfer assets
            $sellerAsset->decrement('locked_amount', $sellOrder->amount);
            $buyerAsset->increment('amount', $amount);

            // Update order statuses
            $buyOrder->update(['status' => OrderStatus::Filled]);
            $sellOrder->update(['status' => OrderStatus::Filled]);


            $trade = Trade::create([
                'order_id' => $buyOrder->id,
                'counter_order_id' => $sellOrder->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'symbol' => $buyOrder->symbol,
                'price' => $price,
                'amount' => $buyOrder->amount,
                'total_value' => $totalValue,
                'commission' => $commission,
                'commission_paid_by' => $commissionPaidBy,
            ]);

            // Broadcast event to both users
            broadcast(new OrderMatched($trade))->toOthers();

            return $trade;
        });
    }
}
