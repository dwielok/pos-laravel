<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Separate table (not columns on sales) because POS requires multiple
     * payment methods per transaction — e.g. part cash, part card. A sale
     * with one payment is just the common case of this table having one row.
     */
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['cash', 'card', 'bank_transfer', 'e_wallet', 'store_credit', 'other']);
            $table->unsignedBigInteger('amount_cents');
            $table->string('reference_number')->nullable(); // card auth code, transfer ref, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
