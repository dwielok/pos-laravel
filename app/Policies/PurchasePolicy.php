<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('purchases.view');
    }

    public function view(User $user, Purchase $purchase): bool
    {
        return $user->can('purchases.view');
    }

    public function create(User $user): bool
    {
        return $user->can('purchases.create');
    }

    public function update(User $user, Purchase $purchase): bool
    {
        return $user->can('purchases.update') && in_array($purchase->status->value, ['draft', 'ordered'], true);
    }

    public function receive(User $user, Purchase $purchase): bool
    {
        return $user->can('purchases.receive')
            && in_array($purchase->status->value, ['ordered', 'partially_received'], true);
    }

    public function cancel(User $user, Purchase $purchase): bool
    {
        return $user->can('purchases.cancel')
            && !in_array($purchase->status->value, ['received', 'cancelled'], true);
    }
}
