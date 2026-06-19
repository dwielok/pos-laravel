<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A purchase moves through draft -> ordered -> partially_received ->
     * received -> cancelled. Stock is only ever incremented (via
     * stock_movements) when items are actually marked received, never at
     * draft/ordered — this matters for accurate "incoming stock" reporting
     * vs "stock on hand".
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number', 32)->unique(); // human-readable, e.g. PO-2026-00001
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // who created/recorded it

            $table->enum('status', [
                'draft',
                'ordered',
                'partially_received',
                'received',
                'cancelled',
            ])->default('draft')->index();

            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->unsignedBigInteger('subtotal_cents')->default(0);
            $table->unsignedBigInteger('discount_cents')->default(0);
            $table->unsignedBigInteger('tax_cents')->default(0);
            $table->unsignedBigInteger('total_cents')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
