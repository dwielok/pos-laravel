<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StockMovement;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Query layer for the three report types: sales, profit, inventory. Each
 * method returns plain arrays/collections of already-computed values
 * (never raw Eloquent collections of Sale models for the report tables) so
 * the SAME data structure can feed the Blade report view, the PDF export,
 * and the Excel/CSV export without three divergent implementations of
 * "what does a sales report row look like".
 *
 * Profit figures throughout use each sale_item's LOCKED unit_cost_cents
 * and unit_price_cents -- never the product's current cost/selling price
 * -- so a report run today for a date range from last month reflects what
 * actually happened then, not what today's catalog says it would be worth.
 */
class ReportService
{
    public function salesReport(Carbon $from, Carbon $to, ?int $warehouseId = null): array
    {
        $query = Sale::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to->copy()->endOfDay()])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId));

        $rows = (clone $query)
            ->with(['customer', 'cashier', 'warehouse'])
            ->orderBy('created_at')
            ->get()
            ->map(fn(Sale $sale) => [
                'invoice_number' => $sale->invoice_number,
                'date' => $sale->created_at->format('Y-m-d H:i'),
                'customer' => $sale->customer->is_guest ? 'Walk-in' : $sale->customer->name,
                'cashier' => $sale->cashier->name,
                'warehouse' => $sale->warehouse->name,
                'items_count' => $sale->items()->count(),
                'subtotal' => Money::fromAmount($sale->subtotal_cents)->units(),
                'discount' => Money::fromAmount($sale->discount_cents)->units(),
                'tax' => Money::fromAmount($sale->tax_cents)->units(),
                'total' => $sale->total()->units(),
            ]);

        $totals = [
            'transaction_count' => $rows->count(),
            'subtotal' => $rows->sum('subtotal'),
            'discount' => $rows->sum('discount'),
            'tax' => $rows->sum('tax'),
            'total' => $rows->sum('total'),
            'average_sale' => $rows->count() > 0 ? round($rows->sum('total') / $rows->count(), 2) : 0,
        ];

        return ['rows' => $rows, 'totals' => $totals, 'from' => $from, 'to' => $to];
    }

    /**
     * Profit = revenue - cost of goods sold, computed per line item from
     * locked prices, grouped by product. This is the report that answers
     * "what actually made money", which total-revenue alone cannot.
     */
    public function profitReport(Carbon $from, Carbon $to, ?int $warehouseId = null): array
    {
        $rows = DB::table('sale_items')
            ->selectRaw('sale_items.product_id, sale_items.product_name_snapshot as product_name')
            ->selectRaw('SUM(sale_items.quantity) as quantity_sold')
            ->selectRaw('SUM(sale_items.subtotal_cents) as revenue_cents')
            ->selectRaw('SUM(sale_items.quantity * sale_items.unit_cost_cents) as cost_cents')
            ->selectRaw('SUM(sale_items.subtotal_cents) - SUM(sale_items.quantity * sale_items.unit_cost_cents) as profit_cents')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$from, $to->copy()->endOfDay()])
            ->when($warehouseId, fn($q) => $q->where('sales.warehouse_id', $warehouseId))
            ->groupBy('sale_items.product_id', 'sale_items.product_name_snapshot')
            ->orderByDesc('profit_cents')
            ->get()
            ->map(fn($row) => [
                'product_name' => $row->product_name,
                'quantity_sold' => (int) $row->quantity_sold,
                'revenue' => round($row->revenue_cents / 100, 2),
                'cost' => round($row->cost_cents / 100, 2),
                'profit' => round($row->profit_cents / 100, 2),
                'margin_percent' => $row->revenue_cents > 0
                    ? round(($row->profit_cents / $row->revenue_cents) * 100, 1)
                    : 0,
            ]);

        $totals = [
            'revenue' => $rows->sum('revenue'),
            'cost' => $rows->sum('cost'),
            'profit' => $rows->sum('profit'),
            'margin_percent' => $rows->sum('revenue') > 0
                ? round(($rows->sum('profit') / $rows->sum('revenue')) * 100, 1)
                : 0,
        ];

        return ['rows' => $rows, 'totals' => $totals, 'from' => $from, 'to' => $to];
    }

    /**
     * Snapshot of current stock value/status across all (or one)
     * warehouse, plus the movement activity within the date range -- two
     * different things ("what do I have right now" vs "what moved")
     * intentionally returned together since they're usually read side by
     * side on the inventory report.
     */
    public function inventoryReport(Carbon $from, Carbon $to, ?int $warehouseId = null): array
    {
        $stockRows = DB::table('stock_levels')
            ->selectRaw('products.id as product_id, products.name, products.sku, products.min_stock_level')
            ->selectRaw('SUM(stock_levels.quantity) as quantity')
            ->selectRaw('products.cost_price_cents, products.selling_price_cents')
            ->join('products', 'products.id', '=', 'stock_levels.product_id')
            ->when($warehouseId, fn($q) => $q->where('stock_levels.warehouse_id', $warehouseId))
            ->where('products.track_stock', true)
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.min_stock_level', 'products.cost_price_cents', 'products.selling_price_cents')
            ->orderBy('products.name')
            ->get()
            ->map(fn($row) => [
                'product_id' => $row->product_id,
                'name' => $row->name,
                'sku' => $row->sku,
                'quantity' => (int) $row->quantity,
                'is_low_stock' => $row->quantity <= $row->min_stock_level,
                'stock_value_at_cost' => round(($row->quantity * $row->cost_price_cents) / 100, 2),
                'stock_value_at_price' => round(($row->quantity * $row->selling_price_cents) / 100, 2),
            ]);

        $movementSummary = StockMovement::query()
            ->selectRaw('type, SUM(ABS(quantity)) as total_quantity')
            ->whereBetween('created_at', [$from, $to->copy()->endOfDay()])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn($row) => [$row->type->value => (int) $row->total_quantity]);

        return [
            'stock_rows' => $stockRows,
            'totals' => [
                'product_count' => $stockRows->count(),
                'low_stock_count' => $stockRows->where('is_low_stock', true)->count(),
                'total_value_at_cost' => $stockRows->sum('stock_value_at_cost'),
                'total_value_at_price' => $stockRows->sum('stock_value_at_price'),
            ],
            'movement_summary' => $movementSummary,
            'from' => $from,
            'to' => $to,
        ];
    }
}
