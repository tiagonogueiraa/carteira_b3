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
        Schema::create('b3_tickers', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();
            $table->string('name')->nullable();
            $table->string('long_name')->nullable();
            $table->string('asset_type')->nullable();   // stock, fund, bdr
            $table->string('sub_type')->nullable();
            $table->string('sector')->nullable();
            $table->string('subsector')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b3_tickers');
    }
};
