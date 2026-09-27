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
        Schema::table('purchase_lots', function (Blueprint $table) {
            $table->foreignId('portfolio_id')->nullable()->after('stock_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_imported')->default(false)->after('purchased_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_lots', function (Blueprint $table) {
            //
            $table->dropForeign(['portfolio_id']);
            $table->dropColumn(['portfolio_id', 'is_imported']);
        });
    }
};
