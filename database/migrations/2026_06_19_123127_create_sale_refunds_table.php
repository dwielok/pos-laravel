<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A refund is its own record, never a mutation of the original sale.
     * sale_refund_items lets a refund cover a subset of line items/quantities
     * (partial refund) — the original sale_items.refunded_quantity is kept
     * in sync by SaleService within the same transaction.
     */
    public function up(): void
    {
        Schema::create('sale_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // who processed the refund
            $table->string('reason');
            $table->unsignedBigInteger('amount_cents');
            $table->enum('refund_method', ['cash', 'card', 'store_credit', 'other']);
            $table->timestamps();
        });

        Schema::create('sale_refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_refund_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_item_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->unsignedBigInteger('amount_cents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_refund_items');
        Schema::dropIfExists('sale_refunds');
    }
};
