<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Core product/catalog record. Note what is deliberately NOT here:
     * - no "stock_quantity" column — stock lives in stock_levels, per
     *   warehouse, derived from stock_movements. A column here would
     *   either be wrong (which warehouse?) or duplicated/denormalized
     *   in a way that invites drift.
     * - prices are integer cents (unsignedBigInteger), never decimal/float,
     *   to keep tax/discount arithmetic exact.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 64)->unique();
            $table->string('barcode', 64)->nullable()->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            $table->unsignedBigInteger('cost_price_cents')->default(0);
            $table->unsignedBigInteger('selling_price_cents');

            // Per-product tax override. Null = use store default tax rate (settings).
            $table->decimal('tax_rate_percent', 5, 2)->nullable();
            $table->boolean('is_tax_inclusive_price')->default(false);

            // Minimum stock threshold for low-stock alerts. This is a per-product
            // setting (not per product+warehouse) by design — simpler for a small
            // business to reason about ("alert me when ANY branch is low"), and
            // DashboardService can still break it down by warehouse if needed.
            $table->integer('min_stock_level')->default(0);

            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active')->index();
            $table->boolean('track_stock')->default(true); // false for services/non-stock items

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
