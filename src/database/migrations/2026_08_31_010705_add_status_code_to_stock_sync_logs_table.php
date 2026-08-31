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
        Schema::table('stock_sync_logs', function (Blueprint $table) {
            // adicionando a colunas status para saber o retorno do HTTP
            $table->integer('status_code')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_sync_logs', function (Blueprint $table) {
            //
        });
    }
};
