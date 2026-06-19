<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A manual stock correction (damage, loss, recount, theft, found stock).
     * Each adjustment is a header; adjustment_items holds the per-product lines.
     * Always requires a reason — this is the table an auditor will ask about.
     */
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 32)->unique();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('reason', [
                'stock_count',
                'damaged',
                'expired',
                'theft_loss',
                'found',
                'other',
            ]);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('system_quantity'); // what stock_levels said before adjustment
            $table->integer('counted_quantity'); // what was physically counted/corrected to
            // difference = counted_quantity - system_quantity, persisted to avoid recompute
            $table->integer('difference');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
    }
};
