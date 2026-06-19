<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The central transaction record. Several columns exist specifically
     * because of the offline-first POS requirement:
     *
     * - client_uuid: generated in the BROWSER at the moment of checkout
     *   (before any network call), unique-indexed. This is the idempotency
     *   key. If a device's sync retries (e.g. flaky connection drops the
     *   response after the server already committed), the sync endpoint
     *   upserts on this key — replay is always safe, a sale is never
     *   double-recorded.
     *
     * - register_id: which physical/logical terminal created this sale.
     *   Needed because in a multi-warehouse, many-register, offline-capable
     *   system, the warehouse a sale affects is determined by the device
     *   that rang it up, not by who's logged in (a cashier could log into
     *   any register).
     *
     * - synced_at / created_offline_at: created_offline_at is the
     *   client-reported timestamp of when the sale actually happened at the
     *   register (used for "what really happened when" reporting).
     *   synced_at is when the server actually received/committed it. The gap
     *   between them is exactly the offline window — worth surfacing in
     *   reports/audits.
     *
     * - is_price_locked + has_price_deviation: every sale captures its own
     *   line-item prices (see sale_items). For offline syncs we explicitly
     *   never re-price against the live catalog; has_price_deviation is set
     *   by SaleSyncService when the synced unit_price differs from the
     *   product's current selling_price_cents, purely as a flag for
     *   back-office review — it does NOT block or alter the sale.
     *
     * Sales are append-only: "cancel" and "refund" are status changes plus
     * new linked rows (sale_refunds, reversing stock_movements), never a
     * delete or destructive update of totals.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 32)->unique(); // human-readable, e.g. INV-2026-000123
            $table->uuid('client_uuid')->unique(); // idempotency key, generated client-side at checkout

            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('register_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // cashier

            $table->enum('status', [
                'completed',
                'cancelled',
                'refunded',
                'partially_refunded',
            ])->default('completed')->index();

            $table->unsignedBigInteger('subtotal_cents');
            $table->unsignedBigInteger('discount_cents')->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->nullable();
            $table->decimal('discount_value', 8, 2)->default(0); // raw input, e.g. "10" for 10% or 10.00 fixed
            $table->unsignedBigInteger('tax_cents')->default(0);
            $table->decimal('tax_rate_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('total_cents');
            $table->unsignedBigInteger('paid_cents')->default(0);
            $table->unsignedBigInteger('change_cents')->default(0);

            // Offline-sync specific
            $table->boolean('was_created_offline')->default(false);
            $table->timestamp('created_offline_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->boolean('has_price_deviation')->default(false)->index();

            $table->text('notes')->nullable();
            $table->timestamps(); // created_at here = server commit time (when row was first written)
            $table->softDeletes();

            $table->index(['warehouse_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
