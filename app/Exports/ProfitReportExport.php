<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $reportData) {}

    public function collection(): Collection
    {
        return collect($this->reportData['rows']);
    }

    public function headings(): array
    {
        return ['Product', 'Qty Sold', 'Revenue', 'Cost (COGS)', 'Profit', 'Margin %'];
    }

    public function map($row): array
    {
        return [
            $row['product_name'],
            $row['quantity_sold'],
            $row['revenue'],
            $row['cost'],
            $row['profit'],
            $row['margin_percent'] . '%',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
