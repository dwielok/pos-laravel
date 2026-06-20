<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $target): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $target): bool
    {
        return $user->can('users.update');
    }

    public function delete(User $user, User $target): bool
    {
        // Never allow self-deactivation through this policy path -- a single
        // admin locking themselves out is a recoverable-only-via-DB-access
        // mistake worth blocking entirely rather than just discouraging.
        if ($user->id === $target->id) {
            return false;
        }

        return $user->can('users.delete');
    }
}
