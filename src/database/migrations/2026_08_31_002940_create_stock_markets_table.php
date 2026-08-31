<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();

            $table->string('short_name')->nullable();
            $table->string('long_name')->nullable();
            $table->string('currency', 3)->nullable();

            $table->decimal('regular_market_price', 10, 2)->nullable();
            $table->decimal('regular_market_day_high', 10, 2)->nullable();
            $table->decimal('regular_market_day_low', 10, 2)->nullable();
            $table->string('regular_market_day_range')->nullable();
            $table->decimal('regular_market_change', 10, 2)->nullable();
            $table->decimal('regular_market_change_percent', 8, 4)->nullable();
            $table->timestamp('regular_market_time')->nullable();

            $table->bigInteger('market_cap')->nullable();
            $table->bigInteger('regular_market_volume')->nullable();
            $table->decimal('regular_market_previous_close', 10, 2)->nullable();
            $table->decimal('regular_market_open', 10, 2)->nullable();

            $table->string('fifty_two_week_range')->nullable();
            $table->decimal('fifty_two_week_low', 10, 2)->nullable();
            $table->decimal('fifty_two_week_high', 10, 2)->nullable();

            $table->string('logo_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_markets');
    }
};
