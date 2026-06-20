<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Typed get/set wrapper around the settings key/value table, with a cache
 * layer so reading settings (e.g. on every receipt render, every tax
 * calculation) isn't a fresh query each call. The cache is cleared on
 * every write, so a setting change takes effect on the very next request
 * -- no manual cache-busting needed elsewhere in the app.
 *
 * Groups: store, tax, currency, receipt. Each has a dedicated
 * setMany()-style helper below so SettingController's update methods stay
 * one-liners and the "what fields exist in this group" knowledge lives
 * here, not scattered across controller validation arrays.
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
     * @param array<string, array{value: mixed, type?: string}> $fields
     */
    public function setMany(string $group, array $fields): void
    {
        foreach ($fields as $key => $field) {
            $value = is_array($field) ? $field['value'] : $field;
            $type = is_array($field) ? ($field['type'] ?? 'string') : 'string';
            $this->set($group, $key, $value, $type);
        }
    }

    public function storeInfo(): array
    {
        return [
            'name' => $this->get('store', 'name', config('app.name', 'My Store')),
            'address' => $this->get('store', 'address', ''),
            'phone' => $this->get('store', 'phone', ''),
            'email' => $this->get('store', 'email', ''),
            'tax_id' => $this->get('store', 'tax_id', ''),
            'logo_path' => $this->get('store', 'logo_path', null),
            'currency_symbol' => $this->get('currency', 'symbol', '$'),
            'currency_code' => $this->get('currency', 'code', 'USD'),
            'currency_position' => $this->get('currency', 'position', 'before'), // before|after the amount
            'default_tax_rate_percent' => (float) $this->get('tax', 'default_rate_percent', 0),
            'tax_label' => $this->get('tax', 'label', 'Tax'),
            'prices_include_tax' => (bool) $this->get('tax', 'prices_include_tax', false),
            'receipt_footer' => $this->get('receipt', 'footer_text', 'Thank you for your purchase!'),
            'receipt_show_logo' => (bool) $this->get('receipt', 'show_logo', true),
            'receipt_paper_size' => $this->get('receipt', 'paper_size', '80mm'),
        ];
    }

    public function updateStoreInfo(array $data): void
    {
        $this->setMany('store', [
            'name' => $data['name'],
            'address' => $data['address'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'tax_id' => $data['tax_id'] ?? '',
        ]);
    }

    public function updateTaxSettings(array $data): void
    {
        $this->setMany('tax', [
            'default_rate_percent' => $data['default_rate_percent'],
            'label' => $data['label'] ?? 'Tax',
            'prices_include_tax' => ['value' => $data['prices_include_tax'] ?? false, 'type' => 'bool'],
        ]);
    }

    public function updateCurrencySettings(array $data): void
    {
        $this->setMany('currency', [
            'symbol' => $data['symbol'],
            'code' => $data['code'],
            'position' => $data['position'],
        ]);
    }

    public function updateReceiptSettings(array $data): void
    {
        $this->setMany('receipt', [
            'footer_text' => $data['footer_text'] ?? '',
            'show_logo' => ['value' => $data['show_logo'] ?? false, 'type' => 'bool'],
            'paper_size' => $data['paper_size'],
        ]);
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
