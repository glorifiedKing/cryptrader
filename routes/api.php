<?php

use App\Http\Controllers\Api\MatchOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Broadcasting\BroadcastController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/match', [MatchOrderController::class, 'match']);
});

Route::any('broadcasting/auth', [BroadcastController::class, 'authenticate']);
