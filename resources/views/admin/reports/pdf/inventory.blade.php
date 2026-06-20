<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @include('admin.reports.pdf._layout-styles')
</head>

<body>
    <h1>Inventory Report</h1>
    <p class="subtitle">As of {{ $report['to']->format('M d, Y') }} &middot; Generated
        {{ now()->format('M d, Y g:i A') }}</p>

    <table class="summary-table">
        <tr>
            <td><span class="label">Products Tracked</span><span
                    class="value">{{ $report['totals']['product_count'] }}</span></td>
            <td><span class="label">Low Stock Items</span><span
                    class="value">{{ $report['totals']['low_stock_count'] }}</span></td>
            <td><span class="label">Value (at cost)</span><span
                    class="value">{{ number_format($report['totals']['total_value_at_cost'], 2) }}</span></td>
            <td><span class="label">Value (at price)</span><span
                    class="value">{{ number_format($report['totals']['total_value_at_price'], 2) }}</span></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th class="right">Quantity</th>
                <th class="right">Value (cost)</th>
                <th class="right">Value (price)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['stock_rows'] as $row)
                <tr>
                    <td>{{ $row['name'] }}{{ $row['is_low_stock'] ? ' (LOW)' : '' }}</td>
                    <td>{{ $row['sku'] }}</td>
                    <td class="right">{{ $row['quantity'] }}</td>
                    <td class="right">{{ number_format($row['stock_value_at_cost'], 2) }}</td>
                    <td class="right">{{ number_format($row['stock_value_at_price'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-table">
                <td colspan="3" class="right">TOTAL</td>
                <td class="right">{{ number_format($report['totals']['total_value_at_cost'], 2) }}</td>
                <td class="right">{{ number_format($report['totals']['total_value_at_price'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
