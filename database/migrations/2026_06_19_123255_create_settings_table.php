<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simple key/value settings store rather than a rigid columns-on-a-
     * single-row table. `group` lets the settings UI organize fields
     * (store, tax, currency, receipt, backup) without a migration for every
     * new setting. SettingService wraps this with typed get/set helpers and
     * an in-memory + cache layer so reading settings isn't a query per call.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index(); // store, tax, currency, receipt, backup
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string|int|bool|json|float
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
