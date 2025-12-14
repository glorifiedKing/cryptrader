<?php

use App\Actions\CancelOrderAction;
use App\Actions\CreateBuyOrderAction;
use App\Actions\CreateSellOrderAction;
use App\Actions\ExecuteTradeAction;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create buy order with sufficient balance', function () {
    $user = User::factory()->create(['balance' => 1000]);

    $action = new CreateBuyOrderAction();
    $order = $action->execute($user, 'BTC', 100, 5);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->side)->toBe(OrderSide::Buy)
        ->and($order->status)->toBe(OrderStatus::Open)
        ->and((float) $order->locked_value)->toBe(500.0);

    $user->refresh();
    expect((float) $user->balance)->toBe(500.0);
});

test('buy order fails with insufficient balance', function () {
    $user = User::factory()->create(['balance' => 100]);

    $action = new CreateBuyOrderAction();
    $action->execute($user, 'BTC', 100, 5);
})->throws(Exception::class, 'Insufficient balance');

test('user can create sell order with sufficient assets', function () {
    $user = User::factory()->create();
    $asset = Asset::create([
        'user_id' => $user->id,
        'symbol' => 'BTC',
        'amount' => 10,
        'locked_amount' => 0,
    ]);

    $action = new CreateSellOrderAction();
    $order = $action->execute($user, 'BTC', 100, 5);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->side)->toBe(OrderSide::Sell)
        ->and($order->status)->toBe(OrderStatus::Open);

    $asset->refresh();
    expect((float) $asset->amount)->toBe(5.0)
        ->and((float) $asset->locked_amount)->toBe(5.0);
});

test('sell order fails with insufficient assets', function () {
    $user = User::factory()->create();
    Asset::create([
        'user_id' => $user->id,
        'symbol' => 'BTC',
        'amount' => 1,
        'locked_amount' => 0,
    ]);

    $action = new CreateSellOrderAction();
    $action->execute($user, 'BTC', 100, 5);
})->throws(Exception::class, 'Insufficient asset balance');

test('orders can be matched and trade executed', function () {
    $buyer = User::factory()->create(['balance' => 1000]);
    $seller = User::factory()->create(['balance' => 0]);

    Asset::create([
        'user_id' => $seller->id,
        'symbol' => 'BTC',
        'amount' => 10,
        'locked_amount' => 0,
    ]);

    $buyAction = new CreateBuyOrderAction();
    $sellAction = new CreateSellOrderAction();
    $executeAction = new ExecuteTradeAction();

    $buyOrder = $buyAction->execute($buyer, 'BTC', 100, 5);
    $sellOrder = $sellAction->execute($seller, 'BTC', 90, 5);

    $trade = $executeAction->execute($buyOrder, $sellOrder);

    expect((float) $trade->total_value)->toBe(500.0)
        ->and((float) $trade->commission)->toBe(7.5);

    $buyer->refresh();
    $seller->refresh();

    expect((float) $buyer->balance)->toBe(500.0); // Already deducted when order created
    expect((float) $seller->balance)->toBe(492.5); // 500 - 7.5 commission

    $buyOrder->refresh();
    $sellOrder->refresh();

    expect($buyOrder->status)->toBe(OrderStatus::Filled)
        ->and($sellOrder->status)->toBe(OrderStatus::Filled);
});

test('commission is calculated correctly', function () {
    $buyer = User::factory()->create(['balance' => 1000]);
    $seller = User::factory()->create(['balance' => 0]);

    Asset::create([
        'user_id' => $seller->id,
        'symbol' => 'BTC',
        'amount' => 0.01,
        'locked_amount' => 0,
    ]);

    $buyAction = new CreateBuyOrderAction();
    $sellAction = new CreateSellOrderAction();
    $executeAction = new ExecuteTradeAction();

    $buyOrder = $buyAction->execute($buyer, 'BTC', 95000, 0.01);
    $sellOrder = $sellAction->execute($seller, 'BTC', 95000, 0.01);

    $trade = $executeAction->execute($buyOrder, $sellOrder);

    // 0.01 BTC * 95000 = 950 USD
    // Commission = 950 * 0.015 = 14.25
    expect((float) $trade->total_value)->toBe(950.0)
        ->and((float) $trade->commission)->toBe(14.25);

    $seller->refresh();
    expect((float) $seller->balance)->toBe(935.75); // 950 - 14.25
});

test('order can be cancelled and funds released', function () {
    $user = User::factory()->create(['balance' => 1000]);

    $buyAction = new CreateBuyOrderAction();
    $cancelAction = new CancelOrderAction();

    $order = $buyAction->execute($user, 'BTC', 100, 5);

    $user->refresh();
    expect((float) $user->balance)->toBe(500.0);

    $cancelAction->execute($order);

    $user->refresh();
    $order->refresh();

    expect((float) $user->balance)->toBe(1000.0)
        ->and($order->status)->toBe(OrderStatus::Cancelled);
});

test('profile endpoint returns user data', function () {
    $user = User::factory()->create(['balance' => 1000]);
    Asset::create([
        'user_id' => $user->id,
        'symbol' => 'BTC',
        'amount' => 1.5,
        'locked_amount' => 0.5,
    ]);

    $response = $this->actingAs($user)->getJson('/api/profile');

    $response->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'balance',
            'assets' => [
                '*' => ['symbol', 'amount', 'locked_amount'],
            ],
        ]);
});

test('orderbook endpoint returns open orders', function () {
    $user = User::factory()->create(['balance' => 1000]);

    Order::create([
        'user_id' => $user->id,
        'symbol' => 'BTC',
        'side' => OrderSide::Buy,
        'price' => 95000,
        'amount' => 0.01,
        'status' => OrderStatus::Open,
        'locked_value' => 950,
    ]);

    $response = $this->actingAs($user)->getJson('/api/orders?symbol=BTC');

    $response->assertOk()
        ->assertJsonCount(1);
});
