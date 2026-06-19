<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * unit_price_cents and unit_cost_cents are captured AT SALE TIME and
     * never recomputed from the live products table — this is the
     * "price-lock" behavior required for offline sales: the cashier saw a
     * price offline, that's the price the customer paid, full stop.
     *
     * unit_cost_cents is captured too (not just price) so profit reports
     * remain accurate even if the product's cost_price_cents changes later
     * — margin on a historical sale should never silently change because
     * someone updated today's cost price.
     *
     * current_price_at_sync_cents is nullable and only populated by
     * SaleSyncService for offline sales, purely for the deviation audit —
     * it is never used in any total calculation.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            $table->string('product_name_snapshot'); // product name at time of sale, for receipt reprint integrity
            $table->string('product_sku_snapshot', 64);

            $table->integer('quantity');
            $table->unsignedBigInteger('unit_price_cents'); // locked price, see class docblock
            $table->unsignedBigInteger('unit_cost_cents');  // locked cost, for profit reports

            $table->unsignedBigInteger('discount_cents')->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->nullable();
            $table->decimal('discount_value', 8, 2)->default(0);

            $table->unsignedBigInteger('tax_cents')->default(0);
            $table->decimal('tax_rate_percent', 5, 2)->default(0);

            $table->unsignedBigInteger('subtotal_cents'); // quantity * unit_price_cents, pre discount/tax
            $table->unsignedBigInteger('total_cents');     // subtotal - discount + tax

            // Quantity already refunded for this line (supports partial refunds
            // without needing a separate sale_refund_items table for the common
            // case — see sale_refunds for the refund header/audit record).
            $table->integer('refunded_quantity')->default(0);

            $table->unsignedBigInteger('current_price_at_sync_cents')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
