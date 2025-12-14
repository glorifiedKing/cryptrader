<?php

namespace Database\Seeders;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class TradingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users with varying balances
        $users = [];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Trader $i",
                'email' => "trader$i@cryptrader.com",
                'password' => bcrypt('password'),
                'balance' => rand(1000, 10000),
            ]);

            // Give each user some BTC and ETH
            Asset::create([
                'user_id' => $user->id,
                'symbol' => 'BTC',
                'amount' => rand(10, 200) / 100, // 0.1 to 2.0 BTC
                'locked_amount' => 0,
            ]);

            Asset::create([
                'user_id' => $user->id,
                'symbol' => 'ETH',
                'amount' => rand(100, 2000) / 100, // 1 to 20 ETH
                'locked_amount' => 0,
            ]);

            $users[] = $user;
        }


        $btcPrices = [94000, 94500, 95000, 95500, 96000];

        foreach ($btcPrices as $index => $price) {

            Order::create([
                'user_id' => $users[$index]->id,
                'symbol' => 'BTC',
                'side' => OrderSide::Buy,
                'price' => $price,
                'amount' => rand(1, 10) / 100, // 0.01 to 0.1 BTC
                'status' => OrderStatus::Open,
                'locked_value' => 0, // Would be calculated in real scenario
            ]);


            Order::create([
                'user_id' => $users[($index + 1) % 5]->id,
                'symbol' => 'BTC',
                'side' => OrderSide::Sell,
                'price' => $price + 1000,
                'amount' => rand(1, 10) / 100,
                'status' => OrderStatus::Open,
                'locked_value' => 0,
            ]);
        }


        $ethPrices = [3500, 3550, 3600, 3650, 3700];

        foreach ($ethPrices as $index => $price) {

            Order::create([
                'user_id' => $users[$index]->id,
                'symbol' => 'ETH',
                'side' => OrderSide::Buy,
                'price' => $price,
                'amount' => rand(10, 100) / 10, // 1 to 10 ETH
                'status' => OrderStatus::Open,
                'locked_value' => 0,
            ]);


            Order::create([
                'user_id' => $users[($index + 2) % 5]->id,
                'symbol' => 'ETH',
                'side' => OrderSide::Sell,
                'price' => $price + 50,
                'amount' => rand(10, 100) / 10,
                'status' => OrderStatus::Open,
                'locked_value' => 0,
            ]);
        }

        $this->command->info('Trading demo data created successfully!');
        $this->command->info('Demo users created with email: trader1@cryptrader.com to trader5@cryptrader.com');
        $this->command->info('Password for all demo users: password');
    }
}
