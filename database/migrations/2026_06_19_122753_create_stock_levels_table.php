<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * stock_levels is a CACHE, not a source of truth. The source of truth is
     * the append-only stock_movements ledger; this table holds the current
     * derived quantity per (product, warehouse) so we don't have to SUM()
     * the entire movement history on every POS search keystroke.
     *
     * StockService is the only thing allowed to write to this table, and it
     * always does so inside the same DB transaction as the stock_movements
     * insert that justifies the change. If the two ever drift, an artisan
     * command can rebuild stock_levels by replaying stock_movements.
     *
     * Quantity is allowed to go negative (signed integer, not unsigned) —
     * this is required by the offline-sync requirement: a sale that already
     * happened in the physical store must not be rejected just because two
     * registers oversold the same last unit before either synced.
     */
    public function up(): void
    {
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['product_id', 'warehouse_id']);
            $table->index(['warehouse_id', 'quantity']); // low-stock-per-warehouse queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
