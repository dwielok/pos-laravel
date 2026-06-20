<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Events\SaleRefunded;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Handles the two ways a completed sale can be reversed: a full CANCEL
 * (the whole transaction was a mistake, void it entirely) and a partial or
 * full REFUND (the transaction was legitimate, but some/all of it is being
 * returned).
 *
 * Both are append-only operations -- neither ever deletes or rewrites the
 * original Sale/SaleItem rows. A cancel sets status and writes reversing
 * stock_movements; a refund creates a new SaleRefund record (with its own
 * line items) and writes reversing stock_movements scoped to only the
 * quantities actually being returned. This mirrors real accounting
 * practice and keeps the audit trail honest: "what was originally sold"
 * and "what actually came back" are always separately reconstructable.
 */
class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly StockService $stockService,
    ) {}

    public function paginate(array $filters, int $perPage = 20)
    {
        return $this->saleRepository->paginateWithFilters($filters, $perPage);
    }

    public function find(int $id): Sale
    {
        return $this->saleRepository->findOrFail($id);
    }

    /**
     * Voids an entire sale. Only valid while status is still 'completed'
     * (see SalePolicy::cancel) -- a sale that's already been partially
     * refunded should go through refund(), not cancel(), to keep the
     * "what came back and why" trail granular.
     */
    public function cancel(Sale $sale, User $user, string $reason): Sale
    {
        if ($sale->status !== SaleStatus::Completed) {
            throw new InvalidArgumentException("Only a completed sale can be cancelled (current status: {$sale->status->value}).");
        }

        return DB::transaction(function () use ($sale, $user, $reason) {
            $sale->loadMissing('items.product');

            foreach ($sale->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    $this->stockService->increment(
                        product: $item->product,
                        warehouseId: $sale->warehouse_id,
                        quantity: $item->quantity,
                        type: StockMovementType::SaleCancelIn,
                        user: $user,
                        note: "Sale {$sale->invoice_number} cancelled: {$reason}",
                        reference: $sale,
                    );
                }
            }

            $sale->update([
                'status' => SaleStatus::Cancelled,
                'notes' => trim(($sale->notes ? $sale->notes . ' | ' : '') . "Cancelled: {$reason}"),
            ]);

            return $sale->fresh(['items.product']);
        });
    }

    /**
     * Refunds specific quantities of specific line items (partial refund
     * is the general case; refunding every line's full quantity is simply
     * the boundary case of the same operation). Stock is restored only for
     * the quantities actually refunded, and only for products that track
     * stock. Sale.status becomes 'refunded' if every item is now fully
     * refunded, or 'partially_refunded' otherwise.
     *
     * @param array<int, int> $refundQuantities [sale_item_id => quantity_to_refund]
     */
    public function refund(
        Sale $sale,
        array $refundQuantities,
        string $reason,
        PaymentMethod|string $refundMethod,
        User $user,
    ): Sale {
        if (!$sale->isFullyRefundable() && $sale->status !== SaleStatus::PartiallyRefunded) {
            throw new InvalidArgumentException(
                "Sale status '{$sale->status->value}' does not allow refunds."
            );
        }

        $refundMethod = $refundMethod instanceof PaymentMethod ? $refundMethod->value : $refundMethod;

        return DB::transaction(function () use ($sale, $refundQuantities, $reason, $refundMethod, $user) {
            $sale->loadMissing('items.product');

            $refundAmountCents = 0;
            $refundLines = [];

            foreach ($sale->items as $item) {
                $requestedQty = $refundQuantities[$item->id] ?? 0;

                if ($requestedQty <= 0) {
                    continue;
                }

                $maxRefundable = $item->quantityRefundable();

                if ($requestedQty > $maxRefundable) {
                    throw new InvalidArgumentException(
                        "Cannot refund {$requestedQty} of '{$item->product_name_snapshot}' — only {$maxRefundable} refundable."
                    );
                }

                // Pro-rate the refund amount from this line's locked unit
                // price, not the current catalog price -- consistent with
                // the same price-lock principle the original sale used.
                $unitRefundAmount = $item->quantity > 0
                    ? intdiv($item->total_cents, $item->quantity)
                    : 0;
                $lineRefundAmount = $unitRefundAmount * $requestedQty;

                $refundLines[] = ['item' => $item, 'quantity' => $requestedQty, 'amount_cents' => $lineRefundAmount];
                $refundAmountCents += $lineRefundAmount;
            }

            if (empty($refundLines)) {
                throw new InvalidArgumentException('No valid quantities provided to refund.');
            }

            $refund = $sale->refunds()->create([
                'user_id' => $user->id,
                'reason' => $reason,
                'amount_cents' => $refundAmountCents,
                'refund_method' => $refundMethod,
            ]);

            foreach ($refundLines as $line) {
                /** @var SaleItem $item */
                $item = $line['item'];

                $refund->items()->create([
                    'sale_item_id' => $item->id,
                    'quantity' => $line['quantity'],
                    'amount_cents' => $line['amount_cents'],
                ]);

                $item->update(['refunded_quantity' => $item->refunded_quantity + $line['quantity']]);

                if ($item->product && $item->product->track_stock) {
                    $this->stockService->increment(
                        product: $item->product,
                        warehouseId: $sale->warehouse_id,
                        quantity: $line['quantity'],
                        type: StockMovementType::RefundIn,
                        user: $user,
                        note: "Refund against {$sale->invoice_number}: {$reason}",
                        reference: $refund,
                    );
                }
            }

            $sale->refresh()->loadMissing('items');
            $allFullyRefunded = $sale->items->every(fn(SaleItem $i) => $i->quantityRefundable() === 0);

            $sale->update([
                'status' => $allFullyRefunded ? SaleStatus::Refunded : SaleStatus::PartiallyRefunded,
            ]);

            event(new SaleRefunded($refund));

            return $sale->fresh(['items', 'refunds.items']);
        });
    }
}
