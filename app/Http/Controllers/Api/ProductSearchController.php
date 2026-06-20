<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Live product search for the POS register screen WHILE ONLINE. When the
 * register has connectivity, this gives fresher results than the cached
 * IndexedDB catalog snapshot (e.g. a price changed 2 minutes ago). When
 * offline, register.js falls back to querying IndexedDB directly --
 * this endpoint is simply unreachable then, by design (no service worker
 * trickery to fake a response; the client already has a local cache it
 * can search against).
 */
class ProductSearchController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'min:1', 'max:100'],
        ]);

        /** @var Register $register */
        $register = $request->attributes->get('register');

        $products = $this->productService->searchForPos($request->string('q'), $register->warehouse_id, 20);

        return response()->json([
            'data' => $products->map(fn($product) => $this->toPosArray($product, $register->warehouse_id)),
        ]);
    }

    public function findByBarcode(Request $request, string $barcode): JsonResponse
    {
        /** @var Register $register */
        $register = $request->attributes->get('register');

        $product = $this->productService->findByBarcode($barcode);

        if (!$product || $product->status !== 'active') {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->load(['stockLevels' => fn($q) => $q->where('warehouse_id', $register->warehouse_id), 'unit']);

        return response()->json(['data' => $this->toPosArray($product, $register->warehouse_id)]);
    }

    private function toPosArray($product, int $warehouseId): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'unit_symbol' => $product->unit->symbol ?? '',
            'selling_price_cents' => $product->selling_price_cents,
            'tax_rate_percent' => $product->tax_rate_percent,
            'is_tax_inclusive_price' => $product->is_tax_inclusive_price,
            'track_stock' => $product->track_stock,
            'stock_quantity' => $product->stockInWarehouse($warehouseId),
            'image_url' => $product->image_path ? asset('storage/' . $product->image_path) : null,
        ];
    }
}
