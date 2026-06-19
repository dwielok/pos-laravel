<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // only created_at exists — rows are never updated, see migration docblock

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'quantity_before',
        'quantity_after',
        'type',
        'reference_type',
        'reference_id',
        'user_id',
        'note',
        'is_from_offline_sync',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'quantity_before' => 'integer',
            'quantity_after' => 'integer',
            'type' => StockMovementType::class,
            'is_from_offline_sync' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Belt-and-suspenders: even if something tries to call update(),
        // refuse. The ledger's integrity guarantee depends on rows never
        // changing after insert.
        static::updating(function () {
            throw new \LogicException('StockMovement rows are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \LogicException('StockMovement rows are immutable and cannot be deleted.');
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
