<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @include('admin.reports.pdf._layout-styles')
</head>

<body>
    <h1>Sales Report</h1>
    <p class="subtitle">{{ $report['from']->format('M d, Y') }} &ndash; {{ $report['to']->format('M d, Y') }} &middot;
        Generated {{ now()->format('M d, Y g:i A') }}</p>

    <table class="summary-table">
        <tr>
            <td><span class="label">Transactions</span><span
                    class="value">{{ $report['totals']['transaction_count'] }}</span></td>
            <td><span class="label">Subtotal</span><span
                    class="value">{{ number_format($report['totals']['subtotal'], 2) }}</span></td>
            <td><span class="label">Discounts</span><span
                    class="value">{{ number_format($report['totals']['discount'], 2) }}</span></td>
            <td><span class="label">Total Revenue</span><span
                    class="value">{{ number_format($report['totals']['total'], 2) }}</span></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Cashier</th>
                <th>Warehouse</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td>{{ $row['invoice_number'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['customer'] }}</td>
                    <td>{{ $row['cashier'] }}</td>
                    <td>{{ $row['warehouse'] }}</td>
                    <td class="right">{{ number_format($row['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-table">
                <td colspan="5" class="right">TOTAL</td>
                <td class="right">{{ number_format($report['totals']['total'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
