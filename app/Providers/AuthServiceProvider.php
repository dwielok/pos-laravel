<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        Purchase::class => PurchasePolicy::class,
        Sale::class => SalePolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Settings has no model instance to bind a Policy to via the
        // standard Model-resolution path -- expose its abilities as plain
        // Gates instead so `@can('settings.backup')` and route middleware
        // `can:settings.backup` both work uniformly with model-backed checks.
        Gate::define('settings.store', fn(User $user) => $user->can('settings.store'));
        Gate::define('settings.tax', fn(User $user) => $user->can('settings.tax'));
        Gate::define('settings.currency', fn(User $user) => $user->can('settings.currency'));
        Gate::define('settings.receipt', fn(User $user) => $user->can('settings.receipt'));
        Gate::define('settings.backup', fn(User $user) => $user->can('settings.backup'));
        Gate::define('settings.restore', fn(User $user) => $user->can('settings.restore'));

        // Super-admin bypass: a user with the 'admin' role passes every
        // ability check without each Policy needing its own "is admin?"
        // escape hatch. Scoped narrowly to the 'admin' role (not "any role"),
        // and still goes through Spatie's permission system underneath.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
