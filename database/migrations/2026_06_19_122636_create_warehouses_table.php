<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A warehouse is any stock-holding location: a store branch, a back
     * room, a central warehouse. Every sale, purchase and stock movement
     * is scoped to one. This is the table that makes multi-warehouse
     * stock tracking possible — stock_levels is keyed on (product, warehouse).
     */
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique(); // short code, e.g. "WH-01", used on receipts/reports
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // used as fallback for legacy/admin-created records
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
