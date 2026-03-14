<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 9pt; }
    .page { padding: 26px 30px; }

    .header { width: 100%; border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 14px; }
    .header-left, .header-right { display: table-cell; vertical-align: top; }
    .header-left { width: 64%; }
    .header-right { width: 36%; text-align: right; }
    .company-name { font-size: 16pt; font-weight: bold; color: #111827; letter-spacing: 0.2px; }
    .company-sub { font-size: 8.2pt; color: #6b7280; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 2px; }
    .company-meta { margin-top: 7px; line-height: 1.55; color: #4b5563; font-size: 8.3pt; }

    .doc-box { display: inline-block; min-width: 220px; border: 1px solid #d1d5db; }
    .doc-box-title { background: #111827; color: #fff; padding: 8px 10px; font-size: 12pt; font-weight: bold; letter-spacing: 1px; text-align: center; }
    .doc-box-content { padding: 8px 10px; text-align: left; font-size: 8.5pt; }
    .doc-number { font-size: 11pt; font-weight: bold; color: #111827; }
    .meta-row { margin-top: 4px; color: #4b5563; }
    .meta-label { color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; font-size: 7.3pt; }

    .info-wrap { width: 100%; margin-bottom: 12px; }
    .info-left, .info-right { display: table-cell; vertical-align: top; }
    .info-left { width: 57%; padding-right: 8px; }
    .info-right { width: 43%; }
    .block { border: 1px solid #d1d5db; padding: 10px 11px; }
    .block-title { font-size: 7.4pt; color: #6b7280; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; font-weight: bold; }
    .client-name { font-size: 10pt; font-weight: bold; color: #111827; margin-bottom: 2px; }
    .client-meta { font-size: 8.2pt; line-height: 1.45; color: #4b5563; }
    .meta-table { width: 100%; border-collapse: collapse; }
    .meta-table td { padding: 3px 0; font-size: 8.3pt; }
    .meta-table .key { width: 48%; color: #6b7280; text-transform: uppercase; font-size: 7.2pt; letter-spacing: 0.4px; }
    .meta-table .val { width: 52%; text-align: right; font-weight: bold; color: #111827; }

    .items-table { width: 100%; border-collapse: collapse; margin-top: 2px; }
    .items-table th, .items-table td { border: 1px solid #d1d5db; padding: 7px 8px; }
    .items-table th { background: #f3f4f6; color: #111827; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.4px; text-align: left; }
    .items-table td { font-size: 8.3pt; vertical-align: top; }
    .item-desc { margin-top: 2px; color: #6b7280; font-size: 7.6pt; line-height: 1.35; }
    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }
    .fw-bold { font-weight: bold; }

    .summary-wrap { width: 100%; margin-top: 10px; }
    .summary-left, .summary-right { display: table-cell; vertical-align: top; }
    .summary-left { width: 62%; padding-right: 10px; }
    .summary-right { width: 38%; }
    .notes { border: 1px solid #d1d5db; padding: 8px 10px; margin-bottom: 8px; }
    .notes .title { font-size: 7.4pt; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; font-weight: bold; }
    .notes .content { font-size: 8pt; color: #374151; line-height: 1.45; white-space: pre-wrap; }

    .totals-table { width: 100%; border-collapse: collapse; }
    .totals-table td { border: 1px solid #d1d5db; padding: 6px 8px; font-size: 8.3pt; }
    .totals-table .label { color: #4b5563; }
    .totals-table .value { text-align: right; font-weight: bold; color: #111827; }
    .totals-table .grand td { background: #111827; color: #fff; font-size: 9.2pt; }
    .totals-table .grand .value { color: #fff; }
    .totals-table .paid td { background: #f9fafb; }
    .totals-table .balance td { background: #f3f4f6; font-weight: bold; }

    .signature-row { width: 100%; margin-top: 18px; }
    .sig-cell { display: table-cell; width: 48%; vertical-align: top; }
    .sig-gap { display: table-cell; width: 4%; }
    .sig-line { margin-top: 34px; border-top: 1px solid #9ca3af; padding-top: 4px; font-size: 8pt; color: #4b5563; line-height: 1.5; }

    .footer { margin-top: 16px; border-top: 1px solid #d1d5db; padding-top: 6px; text-align: center; color: #6b7280; font-size: 7.4pt; }
</style>
</head>
<body>
<div class="page">
    <table class="header">
        <tr>
            <td class="header-left">
                @if(!empty($company['logo_base64']))
                <img src="{{ $company['logo_base64'] }}" alt="Logo" style="max-height:54px;max-width:190px;object-fit:contain;display:block;margin-bottom:8px;">
                @endif
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-sub">{{ $company['tagline'] ?? 'Professional Steel Fabrication' }}</div>
                <div class="company-meta">
                    {{ $company['address'] }}<br>
                    Tel: {{ $company['phone'] }} &nbsp;|&nbsp; Email: {{ $company['email'] }}
                </div>
            </td>
            <td class="header-right">
                <div class="doc-box">
                    <div class="doc-box-title">INVOICE</div>
                    <div class="doc-box-content">
                        <div class="meta-label">Invoice No</div>
                        <div class="doc-number">{{ $invoice->invoice_number }}</div>
                        <div class="meta-row"><span class="meta-label">Status:</span> <strong>{{ strtoupper($invoice->status) }}</strong></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-wrap">
        <tr>
            <td class="info-left">
                <div class="block">
                    <div class="block-title">Invoice To</div>
                    <div class="client-name">{{ $invoice->customer->name }}</div>
                    <div class="client-meta">
                        @if($invoice->customer->company_name){{ $invoice->customer->company_name }}<br>@endif
                        @if($invoice->customer->phone)Tel: {{ $invoice->customer->phone }}<br>@endif
                        @if($invoice->customer->email){{ $invoice->customer->email }}<br>@endif
                        @if($invoice->customer->address){{ $invoice->customer->address }}@endif
                    </div>
                </div>
            </td>
            <td class="info-right">
                <div class="block">
                    <table class="meta-table">
                        <tr><td class="key">Invoice Date</td><td class="val">{{ $invoice->date->format('d M Y') }}</td></tr>
                        @if($invoice->due_date)
                        <tr><td class="key">Due Date</td><td class="val">{{ $invoice->due_date->format('d M Y') }}</td></tr>
                        @endif
                        @if($invoice->order)
                        <tr><td class="key">Order Ref</td><td class="val">{{ $invoice->order->order_number }}</td></tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:6%;" class="text-center">#</th>
                <th style="width:42%;">Description</th>
                <th style="width:11%;" class="text-center">Qty</th>
                <th style="width:11%;" class="text-center">Unit</th>
                <th style="width:15%;" class="text-right">Unit Price</th>
                <th style="width:15%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $item->item_name }}</div>
                    @if($item->description)<div class="item-desc">{{ $item->description }}</div>@endif
                </td>
                <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right fw-bold">{{ $company['currency_symbol'] }} {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-wrap">
        <tr>
            <td class="summary-left">
                @if($invoice->notes)
                <div class="notes">
                    <div class="title">Notes</div>
                    <div class="content">{{ $invoice->notes }}</div>
                </div>
                @endif

                @if($invoice->terms)
                <div class="notes">
                    <div class="title">Terms &amp; Conditions</div>
                    <div class="content">{{ $invoice->terms }}</div>
                </div>
                @endif
            </td>
            <td class="summary-right">
                <table class="totals-table">
                    <tr><td class="label">Subtotal</td><td class="value">{{ $company['currency_symbol'] }} {{ number_format($invoice->subtotal, 2) }}</td></tr>
                    @if($invoice->discount > 0)
                    <tr><td class="label">Discount</td><td class="value">- {{ $company['currency_symbol'] }} {{ number_format($invoice->discount, 2) }}</td></tr>
                    @endif
                    @if($invoice->tax > 0)
                    <tr><td class="label">Tax ({{ $invoice->tax }}%)</td><td class="value">{{ $company['currency_symbol'] }} {{ number_format(($invoice->subtotal - $invoice->discount) * $invoice->tax / 100, 2) }}</td></tr>
                    @endif
                    <tr class="grand"><td>Invoice Total</td><td class="value">{{ $company['currency_symbol'] }} {{ number_format($invoice->grand_total, 2) }}</td></tr>
                    @if($invoice->paid_amount > 0)
                    <tr class="paid"><td class="label">Amount Paid</td><td class="value">{{ $company['currency_symbol'] }} {{ number_format($invoice->paid_amount, 2) }}</td></tr>
                    @endif
                    @if($invoice->balance > 0)
                    <tr class="balance"><td>Balance Due</td><td class="value">{{ $company['currency_symbol'] }} {{ number_format($invoice->balance, 2) }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="signature-row">
        <tr>
            <td class="sig-cell">
                <div class="sig-line">
                    Authorized Signature<br>
                    <strong>{{ $company['owner'] }}</strong><br>
                    {{ $company['name'] }}
                </div>
            </td>
            <td class="sig-gap"></td>
            <td class="sig-cell" style="text-align:right;">
                <div class="sig-line">
                    Customer Signature<br>
                    Name: ___________________<br>
                    Date: ___________________
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ $company['name'] }} &nbsp;|&nbsp; {{ $company['address'] }} &nbsp;|&nbsp; {{ $company['phone'] }} &nbsp;|&nbsp; {{ $company['email'] }}
    </div>
</div>
</body>
</html>
