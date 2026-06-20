<?php

use App\Http\Controllers\Pos\PosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| POS (cashier) Routes
|--------------------------------------------------------------------------
| Serves the Blade shell that boots the offline-capable POS screen
| (Service Worker registration, IndexedDB init, jQuery cart UI). The
| shell itself is session-authenticated like any other page; once loaded,
| the page's own JS talks to routes/api.php for catalog snapshots, search,
| and sale sync -- those additionally require a paired register token
| (see CheckRegisterSession middleware), which the page prompts for on
| first load if not already stored in the browser.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'can:pos.access'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {
        Route::get('/', [PosController::class, 'register'])->name('register');

        // Online checkout requires BOTH session auth and a valid register
        // token -- a cashier can't ring up a sale on a device that was
        // never paired to a register, even if they're logged in normally.
        Route::middleware('register.session')->group(function () {
            Route::post('checkout', [PosController::class, 'checkout'])->name('checkout');
        });

        // Receipt re-print only needs normal session auth + the SalePolicy
        // 'reprint' check (see PosController) -- no register pairing
        // required, since this can reasonably be printed from any device
        // (e.g. the back office reprinting a receipt for a customer).
        Route::get('receipts/{sale}/print', [PosController::class, 'receiptPrint'])->name('receipt.print');
        Route::get('receipts/{sale}/pdf', [PosController::class, 'receiptPdf'])->name('receipt.pdf');
    });
