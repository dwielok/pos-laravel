<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every offline sale that syncs goes through SaleSyncService, which
     * compares what the cashier's device saw (locked prices, the stock it
     * assumed was available) against the server's current truth. We never
     * block or alter the sale because of a mismatch (price-lock + accept
     * negative stock, per product requirements) — but every mismatch is
     * written here so the back office has a queue to review: "these 14
     * synced sales charged a price that no longer matches the catalog,"
     * "these 6 synced sales pushed product X to -3 in Warehouse B."
     */
    public function up(): void
    {
        Schema::create('pos_sync_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('register_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('issue_type', ['price_deviation', 'negative_stock', 'duplicate_sync_ignored']);
            $table->json('details'); // e.g. {"product_id":12,"locked_price":1500,"current_price":1800}
            $table->boolean('reviewed')->default(false)->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sync_audits');
    }
};
