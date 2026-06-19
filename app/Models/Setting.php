<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value', 'type'];

    /**
     * Cast the raw stored string to its declared PHP type. Used by
     * SettingService — prefer calling settings()->get('group.key') in
     * application code rather than querying this model directly, so caching
     * and type-casting stay centralized.
     */
    public function castValue(): mixed
    {
        return match ($this->type) {
            'int' => (int) $this->value,
            'float' => (float) $this->value,
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
