<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counter_order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('symbol', 10);
            $table->decimal('price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->decimal('total_value', 20, 8);
            $table->decimal('commission', 20, 8);
            $table->string('commission_paid_by', 10); // buyer or seller
            $table->timestamps();

            $table->index(['buyer_id', 'created_at']);
            $table->index(['seller_id', 'created_at']);
            $table->index('symbol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
