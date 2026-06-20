<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Wraps the array data ReportService::salesReport() already computed --
 * this class does NO querying or calculation of its own, it only shapes
 * already-correct numbers into rows/headings for maatwebsite/excel. Same
 * pattern for ProfitReportExport and InventoryReportExport: one query
 * layer (ReportService), three thin presentation layers (Blade, this,
 * PDF) that can never disagree with each other on the underlying numbers.
 */
class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $reportData) {}

    public function collection(): Collection
    {
        return collect($this->reportData['rows']);
    }

    public function headings(): array
    {
        return ['Invoice #', 'Date', 'Customer', 'Cashier', 'Warehouse', 'Items', 'Subtotal', 'Discount', 'Tax', 'Total'];
    }

    public function map($row): array
    {
        return [
            $row['invoice_number'],
            $row['date'],
            $row['customer'],
            $row['cashier'],
            $row['warehouse'],
            $row['items_count'],
            $row['subtotal'],
            $row['discount'],
            $row['tax'],
            $row['total'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
