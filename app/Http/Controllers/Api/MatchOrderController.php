<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\MatchOrderJob;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class MatchOrderController extends Controller
{
    public function match(Order $order): JsonResponse
    {

        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        MatchOrderJob::dispatch($order->id);

        return response()->json([
            'message' => 'Order matching initiated',
        ]);
    }
}
