<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @include('admin.reports.pdf._layout-styles')
</head>

<body>
    <h1>Profit Report</h1>
    <p class="subtitle">{{ $report['from']->format('M d, Y') }} &ndash; {{ $report['to']->format('M d, Y') }} &middot;
        Generated {{ now()->format('M d, Y g:i A') }}</p>

    <table class="summary-table">
        <tr>
            <td><span class="label">Revenue</span><span
                    class="value">{{ number_format($report['totals']['revenue'], 2) }}</span></td>
            <td><span class="label">Cost of Goods Sold</span><span
                    class="value">{{ number_format($report['totals']['cost'], 2) }}</span></td>
            <td><span class="label">Gross Profit</span><span
                    class="value">{{ number_format($report['totals']['profit'], 2) }}</span></td>
            <td><span class="label">Margin</span><span class="value">{{ $report['totals']['margin_percent'] }}%</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="right">Qty Sold</th>
                <th class="right">Revenue</th>
                <th class="right">Cost</th>
                <th class="right">Profit</th>
                <th class="right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td>{{ $row['product_name'] }}</td>
                    <td class="right">{{ $row['quantity_sold'] }}</td>
                    <td class="right">{{ number_format($row['revenue'], 2) }}</td>
                    <td class="right">{{ number_format($row['cost'], 2) }}</td>
                    <td class="right">{{ number_format($row['profit'], 2) }}</td>
                    <td class="right">{{ $row['margin_percent'] }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-table">
                <td colspan="4" class="right">TOTAL</td>
                <td class="right">{{ number_format($report['totals']['profit'], 2) }}</td>
                <td class="right">{{ $report['totals']['margin_percent'] }}%</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
