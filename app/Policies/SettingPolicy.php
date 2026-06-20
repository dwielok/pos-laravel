<?php

namespace App\Policies;

use App\Models\User;

/**
 * Settings has no Eloquent model instance to authorize against (it's a
 * key/value store), so this policy is invoked via Gate::allows() with a
 * string ability rather than the implicit Model-based resolution. See
 * AuthServiceProvider/route middleware usage: ->middleware('can:settings.store')
 * style checks against the permission directly are equally valid here --
 * this class exists mainly to group the settings-related abilities in one
 * place for readability and to allow future cross-cutting checks (e.g.
 * "store settings AND backup" requiring two permissions at once).
 */
class SettingPolicy
{
    public function viewStore(User $user): bool
    {
        return $user->can('settings.store');
    }

    public function updateStore(User $user): bool
    {
        return $user->can('settings.store');
    }

    public function updateTax(User $user): bool
    {
        return $user->can('settings.tax');
    }

    public function updateCurrency(User $user): bool
    {
        return $user->can('settings.currency');
    }

    public function updateReceipt(User $user): bool
    {
        return $user->can('settings.receipt');
    }

    public function backup(User $user): bool
    {
        return $user->can('settings.backup');
    }

    public function restore(User $user): bool
    {
        return $user->can('settings.restore');
    }
}
