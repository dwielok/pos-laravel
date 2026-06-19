<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleRefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_refund_id',
        'sale_item_id',
        'quantity',
        'amount_cents',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'amount_cents' => 'integer',
        ];
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(SaleRefund::class, 'sale_refund_id');
    }

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }
}
