<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
            self::PartiallyRefunded => 'Partially Refunded',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Completed => 'green',
            self::Cancelled => 'red',
            self::Refunded => 'amber',
            self::PartiallyRefunded => 'amber',
        };
    }
}
