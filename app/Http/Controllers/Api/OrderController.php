<?php

namespace App\Http\Controllers\Api;

use App\Actions\CancelOrderAction;
use App\Actions\CreateBuyOrderAction;
use App\Actions\CreateSellOrderAction;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Jobs\MatchOrderJob;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('user:id,name')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        // Filter by symbol
        if ($request->has('symbol')) {
            $query->forSymbol($request->symbol);
        }

        // Filter by side (buy/sell)
        if ($request->has('side')) {
            $side = OrderSide::tryFrom($request->side);
            if ($side) {
                $query->where('side', $side);
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $status = OrderStatus::tryFrom((int) $request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'user_name' => $order->user->name,
                'symbol' => $order->symbol,
                'side' => $order->side->value,
                'price' => $order->price,
                'amount' => $order->amount,
                'status' => $order->status->value,
                'created_at' => $order->created_at->toIso8601String(),
            ];
        });

        return response()->json($orders);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            if ($validated['side'] === 'buy') {
                $action = app(CreateBuyOrderAction::class);
            } else {
                $action = app(CreateSellOrderAction::class);
            }

            $order = $action->execute(
                $request->user(),
                $validated['symbol'],
                $validated['price'],
                $validated['amount']
            );


            MatchOrderJob::dispatch($order->id);

            return response()->json([
                'id' => $order->id,
                'symbol' => $order->symbol,
                'side' => $order->side->value,
                'price' => $order->price,
                'amount' => $order->amount,
                'status' => $order->status->value,
                'created_at' => $order->created_at->toIso8601String(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function cancel(Order $order, CancelOrderAction $cancelAction): JsonResponse
    {

        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $cancelAction->execute($order);

            return response()->json([
                'message' => 'Order cancelled successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
