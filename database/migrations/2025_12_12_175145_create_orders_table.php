<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 10);
            $table->string('side', 10); // buy or sell
            $table->decimal('price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->tinyInteger('status')->default(OrderStatus::Open->value);
            $table->decimal('locked_value', 20, 8)->default(0);
            $table->timestamps();

            $table->index(['symbol', 'status', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
