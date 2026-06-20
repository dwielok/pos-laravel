<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Register;
use App\Models\Sale;
use App\Services\PosService;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PosController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly PosService $posService,
        private readonly ReceiptService $receiptService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:pos.access'),
        ];
    }

    /**
     * Serves the POS register Blade shell. This page itself requires a
     * normal authenticated page load (can't be the FIRST load while
     * offline -- the browser needs to fetch it once over the network), but
     * once loaded, the Service Worker caches the shell so subsequent loads
     * (even offline, e.g. after a refresh) are served from cache. See
     * public/sw.js.
     */
    public function register(): View
    {
        $registers = Register::with('warehouse')->where('is_active', true)->get();

        return view('pos.register', compact('registers'));
    }

    /**
     * Online checkout endpoint -- used when the register has connectivity.
     * The client (register.js) tries this FIRST; if it fails due to
     * network error (not a validation/business error), the cart is queued
     * in IndexedDB instead and synced later via the offline-sync endpoint.
     */
    public function checkout(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->posService->checkout(
            cartItems: $request->validated('items'),
            payments: $request->validated('payments'),
            warehouseId: $request->validated('warehouse_id'),
            cashier: $request->user(),
            register: $request->attributes->get('register'),
            customerId: $request->validated('customer_id'),
            discountType: $request->validated('discount_type'),
            discountValue: $request->validated('discount_value'),
            clientUuid: $request->validated('client_uuid'),
        );

        return response()->json([
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'total_cents' => $sale->total_cents,
            'change_cents' => $sale->change_cents,
            'receipt_url' => route('pos.receipt.print', $sale),
        ], 201);
    }

    public function receiptPrint(Sale $sale): Response
    {
        $this->authorize('reprint', $sale);

        return response($this->receiptService->renderHtml($sale));
    }

    public function receiptPdf(Sale $sale): Response
    {
        $this->authorize('reprint', $sale);

        return $this->receiptService->renderPdf($sale)
            ->stream("receipt-{$sale->invoice_number}.pdf");
    }
}
