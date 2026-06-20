<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $reportData) {}

    public function collection(): Collection
    {
        return collect($this->reportData['stock_rows']);
    }

    public function headings(): array
    {
        return ['Product', 'SKU', 'Quantity', 'Low Stock?', 'Value (at cost)', 'Value (at price)'];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['sku'],
            $row['quantity'],
            $row['is_low_stock'] ? 'Yes' : 'No',
            $row['stock_value_at_cost'],
            $row['stock_value_at_price'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
