<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\CancelSaleRequest;
use App\Http\Requests\SaleRefund\StoreSaleRefundRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Warehouse;
use App\Services\ReceiptService;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService,
        private readonly ReceiptService $receiptService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Sale::class);

        $filters = request()->only(['search', 'status', 'warehouse_id', 'customer_id', 'cashier_id', 'from', 'to', 'deviation_only']);

        // A cashier without 'sales.view-all' only ever sees their own sales,
        // enforced here at the query level -- SalePolicy::view() guards the
        // single-sale 'show' route, but the index LIST must also be scoped,
        // since a policy can't filter a collection for you.
        if (!auth()->user()->can('sales.view-all')) {
            $filters['cashier_id'] = auth()->id();
        }

        $sales = $this->saleService->paginate($filters, 20);
        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.sales.index', compact('sales', 'warehouses', 'filters'));
    }

    public function show(Sale $sale): View
    {
        $this->authorize('view', $sale);

        $sale->load(['items.product', 'payments', 'refunds.items', 'customer', 'cashier', 'warehouse', 'register', 'syncAudits']);

        return view('admin.sales.show', compact('sale'));
    }

    public function cancel(CancelSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->saleService->cancel($sale, $request->user(), $request->validated('reason'));

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('success', 'Sale cancelled and stock restored.');
    }

    public function refund(StoreSaleRefundRequest $request, Sale $sale): RedirectResponse
    {
        $quantities = array_filter(
            array_map('intval', $request->validated('quantities')),
            fn(int $qty) => $qty > 0
        );

        if (empty($quantities)) {
            return redirect()
                ->route('admin.sales.show', $sale)
                ->with('error', 'Enter at least one quantity greater than zero to refund.');
        }

        $this->saleService->refund(
            $sale,
            $quantities,
            $request->validated('reason'),
            $request->validated('refund_method'),
            $request->user(),
        );

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('success', 'Refund processed and stock restored.');
    }

    public function reprint(Sale $sale): Response
    {
        $this->authorize('reprint', $sale);

        return response($this->receiptService->renderHtml($sale));
    }

    public function reprintPdf(Sale $sale): Response
    {
        $this->authorize('reprint', $sale);

        return $this->receiptService->renderPdf($sale)->stream("receipt-{$sale->invoice_number}.pdf");
    }
}
