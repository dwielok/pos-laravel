<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $sale->invoice_number }}</title>
    <style>
        /* DomPDF supports a limited CSS subset: no flexbox, no grid, no
           CSS variables. Table-based layout and simple block/inline rules
           only -- kept consistent with that constraint throughout. */
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .small {
            font-size: 9px;
            color: #444;
        }

        .store-name {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="center bold store-name">{{ $store['name'] }}</div>
    @if ($store['address'])
        <div class="center small">{{ $store['address'] }}</div>
    @endif
    @if ($store['phone'])
        <div class="center small">{{ $store['phone'] }}</div>
    @endif

    <hr>

    <table>
        <tr>
            <td>Invoice</td>
            <td class="right bold">{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td class="right">{{ $sale->created_at->format('M d, Y g:i A') }}</td>
        </tr>
        <tr>
            <td>Cashier</td>
            <td class="right">{{ $sale->cashier->name }}</td>
        </tr>
        @if (!$sale->customer->is_guest)
            <tr>
                <td>Customer</td>
                <td class="right">{{ $sale->customer->name }}</td>
            </tr>
        @endif
    </table>

    <hr>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2">{{ $item->product_name_snapshot }}</td>
            </tr>
            <tr>
                <td class="small">{{ $item->quantity }} x
                    {{ $store['currency_symbol'] }}{{ $item->unitPrice()->formatted() }}</td>
                <td class="right">
                    {{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($item->total_cents)->formatted() }}
                </td>
            </tr>
        @endforeach
    </table>

    <hr>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">
                {{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($sale->subtotal_cents)->formatted() }}
            </td>
        </tr>
        @if ($sale->discount_cents > 0)
            <tr>
                <td>Discount</td>
                <td class="right">
                    -{{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($sale->discount_cents)->formatted() }}
                </td>
            </tr>
        @endif
        @if ($sale->tax_cents > 0)
            <tr>
                <td>Tax</td>
                <td class="right">
                    {{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($sale->tax_cents)->formatted() }}
                </td>
            </tr>
        @endif
        <tr class="bold">
            <td>TOTAL</td>
            <td class="right">{{ $store['currency_symbol'] }}{{ $sale->total()->formatted() }}</td>
        </tr>
        <tr>
            <td>Paid</td>
            <td class="right">
                {{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($sale->paid_cents)->formatted() }}
            </td>
        </tr>
        <tr>
            <td>Change</td>
            <td class="right">
                {{ $store['currency_symbol'] }}{{ \App\Support\Money::fromCents($sale->change_cents)->formatted() }}
            </td>
        </tr>
    </table>

    <hr>

    <div class="center small">{{ $store['receipt_footer'] }}</div>
</body>

</html>
