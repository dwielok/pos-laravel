<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin (back-office) Routes
|--------------------------------------------------------------------------
| Session-authenticated Blade routes for everything that is NOT the POS
| register screen. Grouped by module; each group's authorization is
| enforced at the controller level (Policies / 'can:' middleware), not
| just by being inside this route file -- route grouping here is for
| URL/name organization only, not a security boundary by itself.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', function () {
            return "Hello world";
        })->name("dashboard");

        // --- Product Module ---------------------------------------------
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('units', UnitController::class)->only(['index', 'store', 'update', 'destroy']);

        // Dashboard, Inventory, Customers, Sales, Reports, Settings, Users
        // route groups are appended here in subsequent phases.
    });
