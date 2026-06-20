<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PurchaseIn = 'purchase_in';
    case SaleOut = 'sale_out';
    case SaleCancelIn = 'sale_cancel_in';
    case RefundIn = 'refund_in';
    case AdjustmentIn = 'adjustment_in';
    case AdjustmentOut = 'adjustment_out';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';

    /**
     * Whether this movement type increases stock (true) or decreases it (false).
     * StockService uses this to enforce the correct sign on `quantity` so a
     * caller can never accidentally pass a positive quantity for an "out" type.
     */
    public function isInbound(): bool
    {
        return match ($this) {
            self::PurchaseIn, self::SaleCancelIn, self::RefundIn,
            self::AdjustmentIn, self::TransferIn => true,
            self::SaleOut, self::AdjustmentOut, self::TransferOut => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PurchaseIn => 'Purchase received',
            self::SaleOut => 'Sold',
            self::SaleCancelIn => 'Sale cancelled (restocked)',
            self::RefundIn => 'Refunded (restocked)',
            self::AdjustmentIn => 'Adjustment (increase)',
            self::AdjustmentOut => 'Adjustment (decrease)',
            self::TransferIn => 'Transfer in',
            self::TransferOut => 'Transfer out',
        };
    }
}
