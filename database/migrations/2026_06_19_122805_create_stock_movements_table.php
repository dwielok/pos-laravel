<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Append-only ledger. Every single stock change in the system — a sale,
     * a purchase receipt, a manual adjustment, a refund putting stock back,
     * a transfer between warehouses — writes exactly one row here. Rows are
     * never updated or deleted.
     *
     * `reference_type` + `reference_id` is a polymorphic pointer back to
     * whatever caused the movement (Sale, Purchase, StockAdjustment), so
     * "why did stock change" always has a concrete, traceable answer.
     *
     * `quantity_before` / `quantity_after` are denormalized snapshots of the
     * stock_levels value at the moment of the movement — this means a stock
     * movement history report never has to replay the whole ledger to show
     * "what was the running balance", and it gives us a way to *detect*
     * drift between the cache and the ledger later if it ever happens.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();

            // positive = stock in, negative = stock out. Sign convention kept
            // consistent so SUM(quantity) always equals current stock level.
            $table->integer('quantity');
            $table->integer('quantity_before');
            $table->integer('quantity_after');

            $table->enum('type', [
                'purchase_in',       // goods received from supplier
                'sale_out',          // sold via POS
                'sale_cancel_in',    // sale cancelled, stock restored
                'refund_in',         // refunded item, stock restored
                'adjustment_in',     // manual correction, increases stock
                'adjustment_out',    // manual correction, decreases stock
                'transfer_in',       // received from another warehouse
                'transfer_out',      // sent to another warehouse
            ])->index();

            $table->nullableMorphs('reference'); // Sale, Purchase, StockAdjustment, StockTransfer
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // who caused it
            $table->text('note')->nullable();

            // Set when this movement originated from an offline-synced sale,
            // so reports can distinguish "stock went negative because of an
            // offline oversell" from a data-entry mistake.
            $table->boolean('is_from_offline_sync')->default(false);

            $table->timestamp('created_at');

            $table->index(['product_id', 'warehouse_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
