<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 280px;
            margin: 0 auto;
            padding: 12px;
            color: #000;
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
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .item-name {
            width: 50%;
        }

        .small {
            font-size: 10px;
            color: #444;
        }

        @media print {
            body {
                width: 100%;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="center bold" style="font-size: 14px;">{{ $store['name'] }}</div>
    @if ($store['address'])
        <div class="center small">{{ $store['address'] }}</div>
    @endif
    @if ($store['phone'])
        <div class="center small">{{ $store['phone'] }}</div>
    @endif

    <hr>

    <div>Invoice: <span class="bold">{{ $sale->invoice_number }}</span></div>
    <div>Date: {{ $sale->created_at->format('M d, Y g:i A') }}</div>
    <div>Cashier: {{ $sale->cashier->name }}</div>
    @if (!$sale->customer->is_guest)
        <div>Customer: {{ $sale->customer->name }}</div>
    @endif
    @if ($sale->was_created_offline)
        <div class="small">* Recorded offline, synced {{ $sale->synced_at?->format('M d, g:i A') }}</div>
    @endif

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
