<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 9pt;
            background: #ffffff;
        }

        .page {
            padding: 24px 28px 26px;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .header td {
            vertical-align: top;
        }

        .brand {
            width: 62%;
            padding-right: 16px;
        }

        .document {
            width: 38%;
            text-align: right;
        }

        .logo {
            display: block;
            max-width: 180px;
            max-height: 58px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .company-tagline {
            margin-top: 3px;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #b45309;
        }

        .company-meta {
            margin-top: 9px;
            font-size: 8.2pt;
            line-height: 1.6;
            color: #475569;
        }

        .doc-card {
            border: 1px solid #d6dce5;
            background: #f8fafc;
            padding: 14px 16px;
        }

        .doc-kicker {
            font-size: 7.6pt;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            color: #64748b;
        }

        .doc-title {
            margin-top: 4px;
            font-size: 17pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.8px;
        }

        .doc-number {
            margin-top: 8px;
            font-size: 10.8pt;
            font-weight: bold;
            color: #b45309;
        }

        .doc-status {
            margin-top: 10px;
            display: inline-block;
            padding: 4px 10px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #9a3412;
            font-size: 7.4pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.9px;
        }

        .hero-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border-top: 2px solid #0f172a;
            border-bottom: 1px solid #d6dce5;
        }

        .hero-strip td {
            padding: 10px 0;
            vertical-align: middle;
        }

        .hero-copy {
            font-size: 8.6pt;
            color: #475569;
            line-height: 1.55;
        }

        .hero-highlight {
            text-align: right;
            font-size: 8.3pt;
            color: #64748b;
        }

        .hero-highlight strong {
            display: block;
            margin-top: 2px;
            font-size: 12pt;
            color: #0f172a;
        }

        .info-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 16px;
        }

        .info-grid td {
            vertical-align: top;
        }

        .party-cell {
            width: 52%;
            padding-right: 12px;
        }

        .meta-cell {
            width: 48%;
        }

        .panel {
            border: 1px solid #d6dce5;
            background: #ffffff;
            padding: 12px 14px;
            min-height: 126px;
        }

        .panel-title {
            margin-bottom: 7px;
            font-size: 7.6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
        }

        .party-name {
            font-size: 10.6pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.45;
        }

        .party-meta {
            margin-top: 5px;
            font-size: 8.2pt;
            line-height: 1.6;
            color: #475569;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 0;
            font-size: 8.2pt;
            border-bottom: 1px solid #eef2f7;
        }

        .meta-table tr:last-child td {
            border-bottom: none;
        }

        .meta-key {
            width: 44%;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 7.2pt;
        }

        .meta-value {
            width: 56%;
            text-align: right;
            color: #0f172a;
            font-weight: bold;
        }

        .intro {
            margin-bottom: 14px;
            padding: 11px 12px;
            border-left: 3px solid #f59e0b;
            background: #fffbeb;
            font-size: 8.5pt;
            line-height: 1.65;
            color: #4b5563;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            padding: 9px 8px;
            background: #0f172a;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-size: 7.2pt;
            text-align: left;
            border: 1px solid #0f172a;
        }

        .items-table tbody td {
            padding: 9px 8px;
            border: 1px solid #d6dce5;
            font-size: 8.2pt;
            vertical-align: top;
            color: #334155;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .item-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 8.6pt;
        }

        .item-meta {
            margin-top: 3px;
            color: #64748b;
            font-size: 7.5pt;
            line-height: 1.45;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 14px;
        }

        .summary-grid td {
            vertical-align: top;
        }

        .content-cell {
            width: 56%;
            padding-right: 12px;
        }

        .totals-cell {
            width: 44%;
        }

        .content-block {
            border: 1px solid #d6dce5;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #ffffff;
        }

        .content-title {
            margin-bottom: 6px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .content-text {
            font-size: 8.1pt;
            line-height: 1.6;
            color: #475569;
            white-space: pre-line;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 10px;
            border: 1px solid #d6dce5;
            font-size: 8.4pt;
        }

        .totals-label {
            color: #475569;
        }

        .totals-value {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        .totals-grand td {
            background: #0f172a;
            color: #ffffff;
            font-size: 9.4pt;
        }

        .totals-grand .totals-value {
            color: #ffffff;
        }

        .totals-paid td {
            background: #f8fafc;
        }

        .totals-balance td {
            background: #fff7ed;
            font-weight: bold;
        }

        .signoff {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .signoff td {
            vertical-align: top;
        }

        .signoff-note {
            width: 58%;
            padding-right: 20px;
            font-size: 8.6pt;
            line-height: 1.65;
            color: #475569;
        }

        .signature-area {
            width: 42%;
            text-align: right;
        }

        .signature-company {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .signature-name {
            margin-top: 4px;
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
        }

        .signature-space {
            height: 56px;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            font-size: 7.8pt;
            color: #475569;
            line-height: 1.5;
        }

        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #d6dce5;
            text-align: center;
            font-size: 7.4pt;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    @php
        $customerName = $invoice->customer->company_name ?: $invoice->customer->name;
        $contactName = $invoice->customer->company_name && $invoice->customer->name !== $invoice->customer->company_name
            ? $invoice->customer->name
            : null;
        $taxAmount = ($invoice->subtotal - $invoice->discount) * $invoice->tax / 100;
        $termsText = trim((string) $invoice->terms);

        if ($termsText === '') {
            $termsText = 'Payment is due as per the terms stated on this invoice. Please mention the invoice number with your remittance.';
        }
    @endphp

    <div class="page">
        <table class="header">
            <tr>
                <td class="brand">
                    @if(!empty($company['logo_base64']))
                        <img src="{{ $company['logo_base64'] }}" alt="Logo" class="logo">
                    @endif
                    <div class="company-name">{{ $company['name'] }}</div>
                    @if(!empty($company['tagline']))
                        <div class="company-tagline">{{ $company['tagline'] }}</div>
                    @endif
                    <div class="company-meta">
                        @if(!empty($company['owner'])){{ $company['owner'] }}<br>@endif
                        @if(!empty($company['address'])){{ $company['address'] }}<br>@endif
                        @if(!empty($company['phone']))Tel: {{ $company['phone'] }}@endif
                        @if(!empty($company['email'])) &nbsp;|&nbsp; Email: {{ $company['email'] }}@endif
                    </div>
                </td>
                <td class="document">
                    <div class="doc-card">
                        <div class="doc-kicker">Billing Document</div>
                        <div class="doc-title">INVOICE</div>
                        <div class="doc-number">{{ $invoice->invoice_number }}</div>
                        <div class="doc-status">{{ strtoupper($invoice->status) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="hero-strip">
            <tr>
                <td>
                    <div class="hero-copy">
                        Please find below the invoice for the completed scope of work, supplied items, or approved order.
                        Kindly arrange payment within the stated due period and reference the invoice number in all correspondence.
                    </div>
                </td>
                <td class="hero-highlight">
                    Balance Due
                    <strong>{{ $company['currency_symbol'] }} {{ number_format($invoice->balance, 2) }}</strong>
                </td>
            </tr>
        </table>

        <table class="info-grid">
            <tr>
                <td class="party-cell">
                    <div class="panel">
                        <div class="panel-title">Bill To</div>
                        <div class="party-name">
                            {{ $customerName }}
                            @if($contactName)
                                <br>{{ $contactName }}
                            @endif
                        </div>
                        <div class="party-meta">
                            @if($invoice->customer->phone)Tel: {{ $invoice->customer->phone }}<br>@endif
                            @if($invoice->customer->email){{ $invoice->customer->email }}<br>@endif
                            @if($invoice->customer->address){{ $invoice->customer->address }}@endif
                        </div>
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="panel">
                        <div class="panel-title">Invoice Details</div>
                        <table class="meta-table">
                            <tr>
                                <td class="meta-key">Invoice No.</td>
                                <td class="meta-value">{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="meta-key">Invoice Date</td>
                                <td class="meta-value">{{ $invoice->date->format('d M Y') }}</td>
                            </tr>
                            @if($invoice->due_date)
                                <tr>
                                    <td class="meta-key">Due Date</td>
                                    <td class="meta-value">{{ $invoice->due_date->format('d M Y') }}</td>
                                </tr>
                            @endif
                            @if($invoice->order)
                                <tr>
                                    <td class="meta-key">Order Ref</td>
                                    <td class="meta-value">{{ $invoice->order->order_number }}</td>
                                </tr>
                            @endif
                            @if($invoice->quotation)
                                <tr>
                                    <td class="meta-key">Quotation Ref</td>
                                    <td class="meta-value">{{ $invoice->quotation->quotation_number }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="meta-key">Status</td>
                                <td class="meta-value">{{ ucfirst($invoice->status) }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="intro">
            Dear Customer,<br>
            Thank you for your business. This invoice summarizes the supplied items and approved charges. Please review
            the billing details below and settle the outstanding amount within the applicable credit period.
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th style="width: 40%;">Item Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-center">Unit</th>
                    <th style="width: 16%;" class="text-right">Unit Price</th>
                    <th style="width: 18%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->item_name }}</div>
                            @if($item->description)
                                <div class="item-meta">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                        <td class="text-center">{{ $item->unit ?: '-' }}</td>
                        <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-grid">
            <tr>
                <td class="content-cell">
                    @if($invoice->notes)
                        <div class="content-block">
                            <div class="content-title">Notes</div>
                            <div class="content-text">{{ $invoice->notes }}</div>
                        </div>
                    @endif

                    @if($termsText !== '')
                        <div class="content-block" style="margin-bottom: 0;">
                            <div class="content-title">Terms &amp; Conditions</div>
                            <div class="content-text">{{ $termsText }}</div>
                        </div>
                    @endif
                </td>
                <td class="totals-cell">
                    <table class="totals-table">
                        <tr>
                            <td class="totals-label">Subtotal</td>
                            <td class="totals-value">{{ $company['currency_symbol'] }} {{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                            <tr>
                                <td class="totals-label">Discount</td>
                                <td class="totals-value">- {{ $company['currency_symbol'] }} {{ number_format($invoice->discount, 2) }}</td>
                            </tr>
                        @endif
                        @if($invoice->tax > 0)
                            <tr>
                                <td class="totals-label">Tax ({{ $invoice->tax }}%)</td>
                                <td class="totals-value">{{ $company['currency_symbol'] }} {{ number_format($taxAmount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="totals-grand">
                            <td>Invoice Total</td>
                            <td class="totals-value">{{ $company['currency_symbol'] }} {{ number_format($invoice->grand_total, 2) }}</td>
                        </tr>
                        @if($invoice->paid_amount > 0)
                            <tr class="totals-paid">
                                <td class="totals-label">Amount Paid</td>
                                <td class="totals-value">{{ $company['currency_symbol'] }} {{ number_format($invoice->paid_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="totals-balance">
                            <td>Balance Due</td>
                            <td class="totals-value">{{ $company['currency_symbol'] }} {{ number_format($invoice->balance, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="signoff">
            <tr>
                <td class="signoff-note">
                    We appreciate your business. For any billing clarification, payment advice, or statement request,
                    please contact our accounts team and mention the invoice reference shown above.
                </td>
                <td class="signature-area">
                    <div class="signature-company">For {{ strtoupper($company['name']) }}</div>
                    <div class="signature-name">Authorized Signatory</div>
                    <div class="signature-space"></div>
                    <div class="signature-line">
                        @if(!empty($company['owner'])){{ $company['owner'] }}<br>@endif
                        {{ $company['name'] }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            {{ $company['name'] }}
            @if(!empty($company['address'])) &nbsp;|&nbsp; {{ $company['address'] }} @endif
            @if(!empty($company['phone'])) &nbsp;|&nbsp; {{ $company['phone'] }} @endif
            @if(!empty($company['email'])) &nbsp;|&nbsp; {{ $company['email'] }} @endif
        </div>
    </div>
</body>

</html>
