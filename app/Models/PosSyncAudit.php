<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSyncAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'register_id',
        'issue_type',
        'details',
        'reviewed',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'reviewed' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(Register::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeUnreviewed(Builder $query): Builder
    {
        return $query->where('reviewed', false);
    }

    public function markReviewed(User $user): void
    {
        $this->update([
            'reviewed' => true,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
    }
}
