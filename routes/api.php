<?php

use App\Http\Controllers\Api\CatalogSnapshotController;
use App\Http\Controllers\Api\ProductSearchController;
use App\Http\Controllers\Api\SaleSyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (consumed by the POS PWA's JavaScript)
|--------------------------------------------------------------------------
| JSON-only endpoints. Authenticated via the normal session (the POS shell
| page itself is session-gated) PLUS 'register.session', which validates
| the X-Register-Token header identifying which physical/logical register
| -- and therefore which warehouse -- a request belongs to. See
| app/Http/Middleware/CheckRegisterSession.php.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'register.session'])
    ->prefix('v1')
    ->group(function () {
        Route::get('products/search', [ProductSearchController::class, 'search'])->name('api.products.search');
        Route::get('products/barcode/{barcode}', [ProductSearchController::class, 'findByBarcode'])->name('api.products.barcode');

        Route::get('catalog/snapshot', CatalogSnapshotController::class)->name('api.catalog.snapshot');

        Route::post('sales/sync', [SaleSyncController::class, 'store'])->name('api.sales.sync');
        Route::get('ping', [SaleSyncController::class, 'ping'])->name('api.ping');
    });
