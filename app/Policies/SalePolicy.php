<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

/**
 * Two-tier sales visibility: 'sales.view' lets a cashier see sales, but only
 * their OWN (enforced here, not at the permission-name level, since
 * permission names can't express "and it's mine"). 'sales.view-all' lifts
 * that restriction for managers/admins. Controllers must still scope index
 * queries by user_id for non-view-all users -- this policy guards the
 * show/cancel/refund actions on a specific Sale instance.
 */
class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    public function view(User $user, Sale $sale): bool
    {
        if ($user->can('sales.view-all')) {
            return true;
        }

        return $user->can('sales.view') && $sale->user_id === $user->id;
    }

    public function cancel(User $user, Sale $sale): bool
    {
        return $user->can('sales.cancel') && $sale->status->value === 'completed';
    }

    public function refund(User $user, Sale $sale): bool
    {
        return $user->can('sales.refund') && $sale->isFullyRefundable();
    }

    public function reprint(User $user, Sale $sale): bool
    {
        if (!$user->can('sales.reprint')) {
            return false;
        }

        return $user->can('sales.view-all') || $sale->user_id === $user->id;
    }
}
