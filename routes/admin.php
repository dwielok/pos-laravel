<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\SupplierController;
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

        // --- Inventory Module ---------------------------------------------
        Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('purchases/{purchase}/mark-ordered', [PurchaseController::class, 'markOrdered'])
            ->name('purchases.mark-ordered');
        Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
            ->name('purchases.receive');
        Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
            ->name('purchases.cancel');

        Route::resource('stock-adjustments', StockAdjustmentController::class)
            ->only(['index', 'create', 'store', 'show']);
        Route::post('stock-adjustments/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve'])
            ->name('stock-adjustments.approve');

        Route::get('stock-movements', [StockMovementController::class, 'index'])
            ->name('stock-movements.index');
        Route::get('products/{product}/stock-movements', [StockMovementController::class, 'forProduct'])
            ->name('products.stock-movements');

        // --- POS Registers (device pairing for offline-capable terminals) --
        Route::get('registers', [RegisterController::class, 'index'])->name('registers.index');
        Route::post('registers', [RegisterController::class, 'store'])->name('registers.store');
        Route::post('registers/{register}/deactivate', [RegisterController::class, 'deactivate'])->name('registers.deactivate');
        Route::post('registers/{register}/regenerate-token', [RegisterController::class, 'regenerateToken'])->name('registers.regenerate-token');

        // --- Customer Module -------------------------------------------------
        Route::resource('customers', CustomerController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

        // --- Sales Module ------------------------------------------------------
        Route::resource('sales', SaleController::class)->only(['index', 'show']);
        Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
        Route::get('sales/{sale}/reprint', [SaleController::class, 'reprint'])->name('sales.reprint');
        Route::get('sales/{sale}/reprint-pdf', [SaleController::class, 'reprintPdf'])->name('sales.reprint-pdf');
    });
