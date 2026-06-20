<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StockMovementController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:stock-movements.view'),
        ];
    }

    public function index(): View
    {
        $filters = request()->only(['product_id', 'warehouse_id', 'type']);

        $movements = StockMovement::query()
            ->with(['product', 'warehouse', 'user', 'reference'])
            ->when($filters['product_id'] ?? null, fn($q, $v) => $q->where('product_id', $v))
            ->when($filters['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when($filters['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.stock-movements.index', compact('movements', 'warehouses', 'filters'));
    }

    /**
     * Per-product movement history, linked from the product edit page.
     */
    public function forProduct(Product $product): View
    {
        $movements = StockMovement::query()
            ->where('product_id', $product->id)
            ->with(['warehouse', 'user', 'reference'])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.stock-movements.product', compact('product', 'movements'));
    }
}
