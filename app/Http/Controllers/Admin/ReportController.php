<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InventoryReportExport;
use App\Exports\ProfitReportExport;
use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    public function sales(Request $request): View
    {
        $this->authorizeAbility('reports.sales');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->salesReport($from, $to, $warehouseId);
        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.reports.sales', compact('report', 'warehouses', 'warehouseId', 'from', 'to'));
    }

    public function profit(Request $request): View
    {
        $this->authorizeAbility('reports.profit');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->profitReport($from, $to, $warehouseId);
        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.reports.profit', compact('report', 'warehouses', 'warehouseId', 'from', 'to'));
    }

    public function inventory(Request $request): View
    {
        $this->authorizeAbility('reports.inventory');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->inventoryReport($from, $to, $warehouseId);
        $warehouses = Warehouse::active()->orderBy('name')->get();

        return view('admin.reports.inventory', compact('report', 'warehouses', 'warehouseId', 'from', 'to'));
    }

    public function exportSales(Request $request, string $format): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeAbility('reports.sales');
        $this->authorizeAbility('reports.export');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->salesReport($from, $to, $warehouseId);
        $filename = "sales-report-{$from->toDateString()}-to-{$to->toDateString()}";

        return match ($format) {
            'csv' => Excel::download(new SalesReportExport($report), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV),
            'xlsx' => Excel::download(new SalesReportExport($report), "{$filename}.xlsx"),
            'pdf' => Pdf::loadView('admin.reports.pdf.sales', compact('report'))->download("{$filename}.pdf"),
            default => abort(404),
        };
    }

    public function exportProfit(Request $request, string $format): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeAbility('reports.profit');
        $this->authorizeAbility('reports.export');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->profitReport($from, $to, $warehouseId);
        $filename = "profit-report-{$from->toDateString()}-to-{$to->toDateString()}";

        return match ($format) {
            'csv' => Excel::download(new ProfitReportExport($report), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV),
            'xlsx' => Excel::download(new ProfitReportExport($report), "{$filename}.xlsx"),
            'pdf' => Pdf::loadView('admin.reports.pdf.profit', compact('report'))->download("{$filename}.pdf"),
            default => abort(404),
        };
    }

    public function exportInventory(Request $request, string $format): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeAbility('reports.inventory');
        $this->authorizeAbility('reports.export');
        [$from, $to, $warehouseId] = $this->parseFilters($request);

        $report = $this->reportService->inventoryReport($from, $to, $warehouseId);
        $filename = "inventory-report-{$from->toDateString()}";

        return match ($format) {
            'csv' => Excel::download(new InventoryReportExport($report), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV),
            'xlsx' => Excel::download(new InventoryReportExport($report), "{$filename}.xlsx"),
            'pdf' => Pdf::loadView('admin.reports.pdf.inventory', compact('report'))->download("{$filename}.pdf"),
            default => abort(404),
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: ?int}
     */
    private function parseFilters(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to')) : Carbon::now();
        $warehouseId = $request->integer('warehouse_id') ?: null;

        return [$from, $to, $warehouseId];
    }

    /**
     * Reports have no Eloquent model to authorize against (they're
     * aggregate queries, not a single resource), so abilities are checked
     * directly via Gate/permission name rather than a Policy class -- same
     * pattern used for Settings (see SettingPolicy docblock from Phase 2).
     */
    private function authorizeAbility(string $ability): void
    {
        if (!auth()->user()->can($ability)) {
            abort(403);
        }
    }
}
