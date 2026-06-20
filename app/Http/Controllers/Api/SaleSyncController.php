<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\SyncOfflineSaleRequest;
use App\Models\Register;
use App\Services\SaleSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleSyncController extends Controller
{
    public function __construct(
        private readonly SaleSyncService $saleSyncService,
    ) {}

    /**
     * Syncs ONE offline sale. The client's sync-queue.js calls this once
     * per queued sale (not batched) so a failure on sale #3 of 10 doesn't
     * block #1, #2, #4-10 from syncing -- each succeeds or fails
     * independently and the client can retry just the failed ones.
     */
    public function store(SyncOfflineSaleRequest $request): JsonResponse
    {
        /** @var Register $register */
        $register = $request->attributes->get('register');

        $result = $this->saleSyncService->sync($request->validated(), $register, $request->user());

        return response()->json([
            'sale_id' => $result['sale']->id,
            'invoice_number' => $result['sale']->invoice_number,
            'client_uuid' => $result['sale']->client_uuid,
            'was_duplicate' => $result['was_duplicate'],
            'has_price_deviation' => $result['sale']->has_price_deviation,
        ], $result['was_duplicate'] ? 200 : 201);
    }

    /**
     * Lightweight heartbeat the client can call on reconnect, before
     * attempting a full sync, just to confirm the register token is still
     * valid and update last_seen_at. Keeps sync-queue.js's retry logic
     * simple: "can I reach the server at all?" as a separate, cheaper
     * question from "did this specific sale sync?".
     */
    public function ping(Request $request): JsonResponse
    {
        /** @var Register $register */
        $register = $request->attributes->get('register');

        return response()->json([
            'ok' => true,
            'register_id' => $register->id,
            'warehouse_id' => $register->warehouse_id,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
