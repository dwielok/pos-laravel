<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Typed get/set wrapper around the settings key/value table, with a request
 * -lifetime cache so reading settings (e.g. on every receipt render) isn't
 * a fresh query each call. Full read/write surface for the Settings module
 * (store info, tax, currency, receipt, backup) is built out in that phase;
 * this minimal version exists now because ReceiptService (POS module)
 * already needs storeInfo() to render a receipt header.
 */
class SettingService
{
    private const CACHE_KEY = 'settings.all';

    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $all = $this->allCached();

        return $all[$group][$key] ?? $default;
    }

    public function set(string $group, string $key, mixed $value, string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Store identity fields used on receipts and reports. Falls back to
     * config('app.name') and sensible blanks if the Settings module hasn't
     * been configured yet, so receipts never render with literal "null".
     */
    public function storeInfo(): array
    {
        return [
            'name' => $this->get('store', 'name', config('app.name', 'My Store')),
            'address' => $this->get('store', 'address', ''),
            'phone' => $this->get('store', 'phone', ''),
            'tax_id' => $this->get('store', 'tax_id', ''),
            'currency_symbol' => $this->get('currency', 'symbol', '$'),
            'receipt_footer' => $this->get('receipt', 'footer_text', 'Thank you for your purchase!'),
        ];
    }

    private function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $grouped = [];
            foreach (Setting::all() as $setting) {
                $grouped[$setting->group][$setting->key] = $setting->castValue();
            }

            return $grouped;
        });
    }
}
