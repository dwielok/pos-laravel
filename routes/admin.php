<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
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

        // --- Dashboard --------------------------------------------------
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

        // --- Reporting Module ----------------------------------------------
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
        Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('reports/sales/export/{format}', [ReportController::class, 'exportSales'])->name('reports.sales.export');
        Route::get('reports/profit/export/{format}', [ReportController::class, 'exportProfit'])->name('reports.profit.export');
        Route::get('reports/inventory/export/{format}', [ReportController::class, 'exportInventory'])->name('reports.inventory.export');

        // --- Settings Module -------------------------------------------------
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings/store', [SettingController::class, 'updateStore'])->name('settings.store.update');
        Route::put('settings/tax', [SettingController::class, 'updateTax'])->name('settings.tax.update');
        Route::put('settings/currency', [SettingController::class, 'updateCurrency'])->name('settings.currency.update');
        Route::put('settings/receipt', [SettingController::class, 'updateReceipt'])->name('settings.receipt.update');
        Route::post('settings/backups', [SettingController::class, 'createBackup'])->name('settings.backups.create');
        Route::get('settings/backups/{filename}/download', [SettingController::class, 'downloadBackup'])->name('settings.backups.download');
        Route::post('settings/backups/{filename}/restore', [SettingController::class, 'restoreBackup'])->name('settings.backups.restore');
        Route::delete('settings/backups/{filename}', [SettingController::class, 'deleteBackup'])->name('settings.backups.delete');

        // --- Users & RBAC ------------------------------------------------------
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // --- Activity Log --------------------------------------------------
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });
