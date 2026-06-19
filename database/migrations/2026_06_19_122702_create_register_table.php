<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A "register" is a physical or logical POS terminal/device. It is the
     * unit of offline identity: when a cashier's browser goes offline, the
     * IndexedDB cart on that device is tied to one register_id, which is
     * tied to exactly one warehouse. That's how a synced offline sale knows
     * which warehouse's stock to decrement, even though it was created with
     * no network connection.
     *
     * registration_token is a long-lived secret issued once when the register
     * is set up (admin action) and stored in the browser's IndexedDB/localStorage
     * on that device. It authenticates POS API calls from that device alongside
     * the cashier's normal session, so a stolen session cookie alone can't sync
     * sales against a register it was never paired with.
     */
    public function up(): void
    {
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30)->unique(); // e.g. "WH01-REG02"
            $table->string('registration_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable(); // updated on every successful sync/heartbeat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registers');
    }
};
