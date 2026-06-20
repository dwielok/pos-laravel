<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Register;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Bulk catalog + customer download, used by the POS PWA to (re)populate its
 * IndexedDB cache while online, so the register can keep operating once
 * connectivity drops. The client calls this on page load and periodically
 * while online (see sync-queue.js); it never needs to be called while
 * offline since the whole point is the data is already local by then.
 *
 * Deliberately returns a flat, denormalized shape (no nested relations to
 * walk) so the client-side IndexedDB writer can bulk-insert directly
 * without additional transformation.
 */
class CatalogSnapshotController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var Register $register */
        $register = $request->attributes->get('register');

        $products = Product::query()
            ->where('status', 'active')
            ->with(['unit', 'stockLevels' => fn($q) => $q->where('warehouse_id', $register->warehouse_id)])
            ->get()
            ->map(function (Product $product) use ($register) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'unit_symbol' => $product->unit->symbol ?? '',
                    'selling_price_cents' => $product->selling_price_cents,
                    'cost_price_cents' => $product->cost_price_cents,
                    'tax_rate_percent' => $product->tax_rate_percent,
                    'is_tax_inclusive_price' => $product->is_tax_inclusive_price,
                    'track_stock' => $product->track_stock,
                    'stock_quantity' => $product->stockInWarehouse($register->warehouse_id),
                    'image_url' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                ];
            });

        $customers = Customer::query()
            ->where('is_guest', false)
            ->orderByDesc('updated_at')
            ->limit(500) // cap the offline customer cache; full list remains searchable online
            ->get(['id', 'name', 'phone', 'email'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone, 'email' => $c->email]);

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'warehouse_id' => $register->warehouse_id,
            'products' => $products,
            'customers' => $customers,
            'guest_customer_id' => Customer::guest()->id,
        ]);
    }
}
