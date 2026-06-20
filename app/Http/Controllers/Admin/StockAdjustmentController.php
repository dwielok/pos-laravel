<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustment\StoreStockAdjustmentRequest;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StockAdjustmentController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly StockAdjustmentService $stockAdjustmentService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:stock-adjustments.view', only: ['index', 'show']),
            new Middleware('permission:stock-adjustments.create', only: ['create', 'store']),
            new Middleware('permission:stock-adjustments.approve', only: ['approve']),
        ];
    }

    public function index(): View
    {
        $adjustments = StockAdjustment::with(['warehouse', 'user'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock-adjustments.index', compact('adjustments'));
    }

    public function create(): View
    {
        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.stock-adjustments.create', compact('warehouses'));
    }

    public function store(StoreStockAdjustmentRequest $request): RedirectResponse
    {
        $adjustment = $this->stockAdjustmentService->create(
            $request->safe()->only(['warehouse_id', 'reason', 'notes']),
            $request->validated('items'),
            $request->user(),
        );

        return redirect()
            ->route('admin.stock-adjustments.show', $adjustment)
            ->with('success', "Adjustment {$adjustment->adjustment_number} saved as draft. Approve it to apply stock changes.");
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        $stockAdjustment->load(['warehouse', 'user', 'approver', 'items.product.unit']);

        return view('admin.stock-adjustments.show', ['adjustment' => $stockAdjustment]);
    }

    public function approve(StockAdjustment $stockAdjustment): RedirectResponse
    {
        $this->stockAdjustmentService->approve($stockAdjustment, request()->user());

        return redirect()
            ->route('admin.stock-adjustments.show', $stockAdjustment)
            ->with('success', 'Adjustment approved and stock levels updated.');
    }
}
