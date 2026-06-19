<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'system_quantity',
        'counted_quantity',
        'difference',
    ];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'integer',
            'counted_quantity' => 'integer',
            'difference' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StockAdjustmentItem $item) {
            $item->difference = $item->counted_quantity - $item->system_quantity;
        });
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
