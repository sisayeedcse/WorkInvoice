<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 8px;
        }

        .center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 10px;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="center bold">{{ $company['name'] ?? config('app.name') }}</div>
    @if(!empty($company['address']))
    <div class="center small">{{ $company['address'] }}</div>@endif
    @if(!empty($company['phone']))
    <div class="center small">{{ $company['phone'] }}</div>@endif

    <div class="line"></div>

    <table>
        <tr>
            <td>Sale No:</td>
            <td class="right">{{ $sale->sale_number }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td class="right">{{ $sale->sale_date?->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td class="right">{{ $sale->display_customer }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach($sale->items as $item)
            <tr>
                <td colspan="2" class="bold">{{ $item->item_name }}</td>
            </tr>
            <tr>
                <td class="small">{{ number_format($item->quantity, 3) }} x {{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="right">{{ number_format($sale->discount, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="right">{{ number_format($sale->tax, 2) }}</td>
        </tr>
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold">{{ number_format($sale->grand_total, 2) }}</td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="center small">Thank you for your business</div>
</body>

</html>