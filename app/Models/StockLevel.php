<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    use HasFactory;

    public $timestamps = false; // only updated_at is used, maintained manually by StockService

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Stock at or below the product's min_stock_level. Uses an explicit join
     * rather than whereHas(), since whereColumn() cannot compare a column on
     * this table against a column on a related table through a correlated
     * subquery — it needs both columns visible in the same query.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query
            ->join('products', 'products.id', '=', 'stock_levels.product_id')
            ->where('products.track_stock', true)
            ->whereColumn('stock_levels.quantity', '<=', 'products.min_stock_level')
            ->select('stock_levels.*');
    }

    public function scopeForWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('warehouse_id', $warehouseId);
    }
}
