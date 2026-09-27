<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cotação e dividendo são iguais pra todo mundo que tem o mesmo ticker
        // — não faz sentido amarrar isso a uma linha de Stock específica (que é
        // por usuário). Troca stock_id por ticker nas duas tabelas, preservando
        // os dados existentes via backfill antes de derrubar a coluna antiga.
        Schema::table('stock_markets', function (Blueprint $table) {
            $table->string('ticker')->nullable()->after('id');
        });

        Schema::table('stock_dividends', function (Blueprint $table) {
            $table->string('ticker')->nullable()->after('id');
        });

        DB::statement('UPDATE stock_markets sm JOIN stocks s ON s.id = sm.stock_id SET sm.ticker = s.ticker');
        DB::statement('UPDATE stock_dividends sd JOIN stocks s ON s.id = sd.stock_id SET sd.ticker = s.ticker');

        Schema::table('stock_markets', function (Blueprint $table) {
            $table->dropForeign(['stock_id']);
            $table->dropColumn('stock_id');
            $table->string('ticker')->nullable(false)->change();
        });

        Schema::table('stock_dividends', function (Blueprint $table) {
            $table->dropForeign(['stock_id']);
            $table->dropUnique(['stock_id', 'payment_date']);
            $table->dropColumn('stock_id');
            $table->string('ticker')->nullable(false)->change();
            $table->unique(['ticker', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_markets', function (Blueprint $table) {
            $table->foreignId('stock_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('stock_dividends', function (Blueprint $table) {
            $table->foreignId('stock_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('UPDATE stock_markets sm JOIN stocks s ON s.ticker = sm.ticker SET sm.stock_id = s.id');
        DB::statement('UPDATE stock_dividends sd JOIN stocks s ON s.ticker = sd.ticker SET sd.stock_id = s.id');

        Schema::table('stock_markets', function (Blueprint $table) {
            $table->dropColumn('ticker');
        });

        Schema::table('stock_dividends', function (Blueprint $table) {
            $table->dropUnique(['ticker', 'payment_date']);
            $table->dropColumn('ticker');
            $table->unique(['stock_id', 'payment_date']);
        });
    }
};
