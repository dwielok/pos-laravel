<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case Draft = 'draft';
    case Ordered = 'ordered';
    case PartiallyReceived = 'partially_received';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Ordered => 'Ordered',
            self::PartiallyReceived => 'Partially Received',
            self::Received => 'Received',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => in_array($next, [self::Ordered, self::Cancelled], true),
            self::Ordered => in_array($next, [self::PartiallyReceived, self::Received, self::Cancelled], true),
            self::PartiallyReceived => in_array($next, [self::Received, self::Cancelled], true),
            self::Received, self::Cancelled => false,
        };
    }
}
