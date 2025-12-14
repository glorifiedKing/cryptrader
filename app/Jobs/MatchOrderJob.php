<?php

namespace App\Jobs;

use App\Actions\ExecuteTradeAction;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MatchOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $orderId
    ) {
    }

    public function handle(ExecuteTradeAction $executeTradeAction): void
    {
        $order = Order::find($this->orderId);

        if (!$order || $order->status !== OrderStatus::Open) {
            return;
        }

        // Find matching counter-order
        $counterOrder = $this->findCounterOrder($order);

        if ($counterOrder) {
            $executeTradeAction->execute(
                $order->side === OrderSide::Buy ? $order : $counterOrder,
                $order->side === OrderSide::Sell ? $order : $counterOrder
            );
        }
    }

    private function findCounterOrder(Order $order): ?Order
    {
        if ($order->side === OrderSide::Buy) {
            // Find first sell order where sell.price <= buy.price
            return Order::open()
                ->forSymbol($order->symbol)
                ->sells()
                ->where('price', '<=', $order->price)
                ->where('amount', '=', $order->amount) // Full match only
                ->orderBy('created_at', 'asc')
                ->first();
        } else {
            // Find first buy order where buy.price >= sell.price
            return Order::open()
                ->forSymbol($order->symbol)
                ->buys()
                ->where('price', '>=', $order->price)
                ->where('amount', '=', $order->amount) // Full match only
                ->orderBy('created_at', 'asc')
                ->first();
        }
    }
}
