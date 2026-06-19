<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // "Piece", "Kilogram", "Box of 12"
            $table->string('symbol', 10);   // "pcs", "kg", "box"
            // Conversion factor to the smallest reportable unit, e.g. "Box of 12"
            // has base_unit_id = pcs and conversion_factor = 12. Left nullable for
            // simple units that don't convert from anything.
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('conversion_factor', 12, 4)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
