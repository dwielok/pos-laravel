<?php

namespace App\Services;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Renders a Sale as a printable receipt -- either as HTML for direct
 * browser printing (POS register, instant, no PDF generation overhead) or
 * as a downloadable PDF (re-print from sales history, emailing a copy).
 * Both render from the SAME Blade view so the layout never drifts between
 * the two formats.
 */
class ReceiptService
{
    public function renderHtml(Sale $sale): string
    {
        $sale->loadMissing(['items.product', 'payments', 'customer', 'cashier', 'warehouse']);

        return view('pos.receipt-print', [
            'sale' => $sale,
            'store' => app(SettingService::class)->storeInfo(),
        ])->render();
    }

    public function renderPdf(Sale $sale): \Barryvdh\DomPDF\PDF
    {
        $sale->loadMissing(['items.product', 'payments', 'customer', 'cashier', 'warehouse']);

        $pdf = Pdf::loadView('pos.receipt-pdf', [
            'sale' => $sale,
            'store' => app(SettingService::class)->storeInfo(),
        ]);

        // 80mm thermal-receipt-style width by default; settings could
        // later expose a "receipt paper size" option (58mm/80mm/A4) that
        // maps to a different paper array here.
        $pdf->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm width in points, tall enough for most receipts

        return $pdf;
    }
}
