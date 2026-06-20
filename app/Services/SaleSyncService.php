<?php

namespace App\Services;

use App\Events\SaleCompleted;
use App\Models\Customer;
use App\Models\PosSyncAudit;
use App\Models\Product;
use App\Models\Register;
use App\Models\Sale;
use App\Models\User;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Ingests a sale that was created OFFLINE on a POS register's device (cart
 * built and "completed" entirely client-side via IndexedDB, no network)
 * once that device regains connectivity and syncs.
 *
 * Three hard requirements drive every decision in this class -- captured
 * here once rather than re-derived at each call site:
 *
 * 1. IDEMPOTENCY: the client generates `client_uuid` at checkout time,
 *    before any network call. A sync retry (flaky connection, app reload
 *    mid-sync) must be a safe no-op, never a duplicate sale.
 *
 * 2. PRICE LOCK: the cashier saw a price on the device while offline; that
 *    is the price the customer paid. We NEVER re-price against the current
 *    `products` table. If the synced price differs from the current
 *    catalog price, we record the deviation for back-office review but the
 *    sale is accepted exactly as the device reported it.
 *
 * 3. NEGATIVE STOCK ALLOWED: the sale already physically happened in the
 *    store. Rejecting it after the fact because the server's stock count
 *    disagrees would be worse than letting stock go negative -- so this is
 *    the one path in the system that calls
 *    StockService::decrementAllowingNegative() rather than decrement().
 *    Every resulting negative balance is exactly the signal that needs
 *    reconciling, which is why it's logged to pos_sync_audits rather than
 *    silently absorbed.
 */
class SaleSyncService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly StockService $stockService,
    ) {}

    /**
     * @param array{
     *   client_uuid: string,
     *   customer_id: ?int,
     *   warehouse_id: int,
     *   created_offline_at: string,
     *   discount_cents: int,
     *   discount_type: ?string,
     *   discount_value: float,
     *   tax_cents: int,
     *   tax_rate_percent: float,
     *   subtotal_cents: int,
     *   total_cents: int,
     *   paid_cents: int,
     *   change_cents: int,
     *   notes: ?string,
     *   items: array<int, array{
     *     product_id: int, quantity: int, unit_price_cents: int,
     *     discount_cents: int, discount_type: ?string, discount_value: float,
     *     tax_cents: int, tax_rate_percent: float, subtotal_cents: int, total_cents: int,
     *   }>,
     *   payments: array<int, array{method: string, amount_cents: int, reference_number: ?string}>,
     * } $payload
     *
     * @return array{sale: Sale, was_duplicate: bool}
     */
    public function sync(array $payload, Register $register, User $cashier): array
    {
        // Idempotency check FIRST, before opening a write transaction or
        // touching stock at all -- a replayed sync must be a pure read.
        $existing = $this->saleRepository->findByClientUuid($payload['client_uuid']);

        if ($existing) {
            $this->logAudit($existing, $register, 'duplicate_sync_ignored', [
                'client_uuid' => $payload['client_uuid'],
            ]);

            return ['sale' => $existing, 'was_duplicate' => true];
        }

        return DB::transaction(function () use ($payload, $register, $cashier) {
            $productIds = array_column($payload['items'], 'product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $sale = Sale::create([
                'invoice_number' => $this->saleRepository->nextInvoiceNumber(),
                'client_uuid' => $payload['client_uuid'],
                'customer_id' => $payload['customer_id'] ?? Customer::guest()->id,
                'warehouse_id' => $register->warehouse_id, // trust the REGISTER's warehouse, not client input
                'register_id' => $register->id,
                'user_id' => $cashier->id,
                'status' => 'completed',
                'subtotal_cents' => $payload['subtotal_cents'],
                'discount_cents' => $payload['discount_cents'] ?? 0,
                'discount_type' => $payload['discount_type'] ?? null,
                'discount_value' => $payload['discount_value'] ?? 0,
                'tax_cents' => $payload['tax_cents'] ?? 0,
                'tax_rate_percent' => $payload['tax_rate_percent'] ?? 0,
                'total_cents' => $payload['total_cents'],
                'paid_cents' => $payload['paid_cents'],
                'change_cents' => $payload['change_cents'] ?? 0,
                'was_created_offline' => true,
                'created_offline_at' => $payload['created_offline_at'],
                'synced_at' => now(),
                'notes' => $payload['notes'] ?? null,
            ]);

            $hasDeviation = false;

            foreach ($payload['items'] as $itemData) {
                $product = $products->get($itemData['product_id']);

                if (!$product) {
                    // Product was deleted server-side after the cashier's
                    // device cached the catalog. We still honor the sale --
                    // the customer paid for something real -- using the
                    // snapshot data the client sent, but flag it loudly.
                    Log::warning('Offline sale referenced a product that no longer exists', [
                        'client_uuid' => $payload['client_uuid'],
                        'product_id' => $itemData['product_id'],
                    ]);
                }

                $currentPriceCents = $product?->selling_price_cents;
                $lockedPriceCents = $itemData['unit_price_cents'];
                $deviates = $product && $currentPriceCents !== $lockedPriceCents;

                $saleItem = $sale->items()->create([
                    'product_id' => $itemData['product_id'],
                    'product_name_snapshot' => $itemData['product_name_snapshot'] ?? $product?->name ?? 'Unknown product',
                    'product_sku_snapshot' => $itemData['product_sku_snapshot'] ?? $product?->sku ?? '',
                    'quantity' => $itemData['quantity'],
                    // PRICE LOCK: always the value the device sent, never recomputed.
                    'unit_price_cents' => $lockedPriceCents,
                    'unit_cost_cents' => $itemData['unit_cost_cents'] ?? $product?->cost_price_cents ?? 0,
                    'discount_cents' => $itemData['discount_cents'] ?? 0,
                    'discount_type' => $itemData['discount_type'] ?? null,
                    'discount_value' => $itemData['discount_value'] ?? 0,
                    'tax_cents' => $itemData['tax_cents'] ?? 0,
                    'tax_rate_percent' => $itemData['tax_rate_percent'] ?? 0,
                    'subtotal_cents' => $itemData['subtotal_cents'],
                    'total_cents' => $itemData['total_cents'],
                    'current_price_at_sync_cents' => $currentPriceCents,
                ]);

                if ($deviates) {
                    $hasDeviation = true;
                    $this->logAudit($sale, $register, 'price_deviation', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'locked_price_cents' => $lockedPriceCents,
                        'current_price_cents' => $currentPriceCents,
                    ]);
                }

                if ($product && $product->track_stock) {
                    $stockBefore = $this->stockService->currentQuantity($product, $register->warehouse_id);

                    $this->stockService->decrementAllowingNegative(
                        product: $product,
                        warehouseId: $register->warehouse_id,
                        quantity: $itemData['quantity'],
                        user: $cashier,
                        note: "Synced offline sale {$sale->invoice_number} (register {$register->code})",
                        reference: $sale,
                    );

                    $stockAfter = $stockBefore - $itemData['quantity'];

                    if ($stockAfter < 0) {
                        $this->logAudit($sale, $register, 'negative_stock', [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'warehouse_id' => $register->warehouse_id,
                            'quantity_sold' => $itemData['quantity'],
                            'resulting_quantity' => $stockAfter,
                        ]);
                    }
                }
            }

            foreach ($payload['payments'] as $paymentData) {
                $sale->payments()->create([
                    'method' => $paymentData['method'],
                    'amount_cents' => $paymentData['amount_cents'],
                    'reference_number' => $paymentData['reference_number'] ?? null,
                ]);
            }

            if ($hasDeviation) {
                $sale->update(['has_price_deviation' => true]);
            }

            $sale->load(['items.product', 'payments', 'customer', 'cashier', 'warehouse']);

            event(new SaleCompleted($sale));

            return ['sale' => $sale, 'was_duplicate' => false];
        });
    }

    private function logAudit(Sale $sale, Register $register, string $issueType, array $details): void
    {
        PosSyncAudit::create([
            'sale_id' => $sale->id,
            'register_id' => $register->id,
            'issue_type' => $issueType,
            'details' => $details,
        ]);
    }
}
