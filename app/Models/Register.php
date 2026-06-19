<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Register extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'code',
        'registration_token',
        'is_active',
        'last_seen_at',
    ];

    protected $hidden = [
        'registration_token', // never serialize this into a JSON response by accident
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Register $register) {
            if (empty($register->registration_token)) {
                $register->registration_token = Str::random(64);
            }
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function touchLastSeen(): void
    {
        $this->forceFill(['last_seen_at' => now()])->save();
    }
}
