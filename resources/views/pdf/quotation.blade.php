<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 9pt;
        color: #1e2530;
        background: #fff;
    }

    .page { padding: 28px 32px; }

    /* Header */
    .header {
        display: table;
        width: 100%;
        margin-bottom: 20px;
        border-bottom: 3px solid #1a2b4a;
        padding-bottom: 14px;
    }

    .header-left  { display: table-cell; vertical-align: top; width: 65%; }
    .header-right { display: table-cell; vertical-align: top; width: 35%; text-align: right; }

    .company-name {
        font-size: 15pt;
        font-weight: bold;
        color: #1a2b4a;
        line-height: 1.2;
    }

    .company-sub {
        font-size: 8.5pt;
        color: #f47c1c;
        font-weight: bold;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .company-info {
        font-size: 8pt;
        color: #6c757d;
        margin-top: 7px;
        line-height: 1.5;
    }

    .doc-title-box {
        background: #1a2b4a;
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        display: inline-block;
        text-align: center;
    }

    .doc-title {
        font-size: 14pt;
        font-weight: bold;
        letter-spacing: 1px;
        display: block;
    }

    .doc-number {
        font-size: 10pt;
        font-weight: bold;
        color: #f47c1c;
        display: block;
        margin-top: 2px;
    }

    /* Info row */
    .info-row {
        display: table;
        width: 100%;
        margin-bottom: 16px;
    }

    .info-left  { display: table-cell; width: 55%; vertical-align: top; }
    .info-right { display: table-cell; width: 45%; vertical-align: top; text-align: right; }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #1a2b4a;
        border-radius: 0 6px 6px 0;
        padding: 9px 12px;
        display: inline-block;
        min-width: 200px;
    }

    .info-box.accent { border-left-color: #f47c1c; }

    .info-label {
        font-size: 7.5pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: bold;
    }

    .info-value {
        font-size: 9.5pt;
        font-weight: bold;
        color: #1a2b4a;
        margin-top: 1px;
    }

    .info-sub {
        font-size: 8pt;
        color: #6c757d;
        line-height: 1.4;
    }

    /* Items table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
    }

    .items-table thead tr {
        background: #1a2b4a;
        color: #fff;
    }

    .items-table thead th {
        padding: 7px 10px;
        font-size: 8pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .items-table tbody tr:nth-child(even) { background: #f8fafc; }

    .items-table tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 8.5pt;
        vertical-align: top;
    }

    .items-table tbody .item-desc {
        font-size: 7.5pt;
        color: #6c757d;
        margin-top: 2px;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }

    /* Totals */
    .totals-table {
        width: 220px;
        margin-left: auto;
        margin-bottom: 14px;
        border-collapse: collapse;
    }

    .totals-table td {
        padding: 5px 10px;
        font-size: 8.5pt;
        border-bottom: 1px solid #e2e8f0;
    }

    .totals-table .grand-row {
        background: #1a2b4a;
        color: #fff;
        font-weight: bold;
        font-size: 10pt;
    }

    .totals-table .grand-row td { border: none; padding: 8px 10px; }

    .amount-accent { color: #f47c1c; font-weight: bold; }

    /* Notes & Terms */
    .notes-section {
        margin-bottom: 12px;
        font-size: 8.5pt;
    }

    .section-title {
        font-size: 8pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .notes-box {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 11px;
        background: #f8fafc;
        font-size: 8pt;
        color: #444;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    /* Signature */
    .signature-row {
        display: table;
        width: 100%;
        margin-top: 20px;
    }

    .sig-cell {
        display: table-cell;
        width: 45%;
        font-size: 8pt;
    }

    .sig-line {
        border-top: 1.5px solid #1a2b4a;
        margin-top: 30px;
        padding-top: 5px;
        color: #6c757d;
        font-size: 8pt;
    }

    /* Footer */
    .pdf-footer {
        margin-top: 18px;
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
        font-size: 7.5pt;
        color: #adb5bd;
        text-align: center;
    }
</style>
</head>
<body>
<div class="page">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @if(!empty($company['logo_base64']))
            <img src="{{ $company['logo_base64'] }}" alt="Logo"
                 style="max-height:52px;max-width:190px;object-fit:contain;display:block;margin-bottom:7px;">
            @endif
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-sub">{{ $company['tagline'] ?? 'Professional Steel Fabrication' }}</div>
            <div class="company-info">
                {{ $company['address'] }}<br>
                Tel: {{ $company['phone'] }} &nbsp;|&nbsp; Email: {{ $company['email'] }}
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title-box">
                <span class="doc-title">QUOTATION</span>
                <span class="doc-number">{{ $quotation->quotation_number }}</span>
            </div>
        </div>
    </div>

    <!-- Info Row -->
    <div class="info-row">
        <div class="info-left">
            <div class="info-box">
                <div class="info-label">Bill To</div>
                <div class="info-value">{{ $quotation->customer->name }}</div>
                <div class="info-sub">
                    @if($quotation->customer->company_name){{ $quotation->customer->company_name }}<br>@endif
                    @if($quotation->customer->phone)Tel: {{ $quotation->customer->phone }}<br>@endif
                    @if($quotation->customer->email){{ $quotation->customer->email }}<br>@endif
                    @if($quotation->customer->address){{ $quotation->customer->address }}@endif
                </div>
            </div>
        </div>
        <div class="info-right">
            <div class="info-box accent" style="text-align:left;">
                <table style="font-size:8pt;width:100%;">
                    <tr><td class="info-label" style="padding-right:14px;">Date:</td><td class="fw-bold">{{ $quotation->date->format('d M Y') }}</td></tr>
                    @if($quotation->valid_until)
                    <tr><td class="info-label" style="padding-right:14px;">Valid Until:</td><td class="fw-bold">{{ $quotation->valid_until->format('d M Y') }}</td></tr>
                    @endif
                    <tr><td class="info-label" style="padding-right:14px;">Status:</td><td class="fw-bold" style="color:#f47c1c;">{{ strtoupper($quotation->status) }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width:50px;">Qty</th>
                <th class="text-center" style="width:45px;">Unit</th>
                <th class="text-right" style="width:80px;">Unit Price</th>
                <th class="text-right" style="width:85px;">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quotation->items as $i => $item)
        <tr>
            <td class="text-center" style="color:#6c757d;">{{ $i + 1 }}</td>
            <td>
                <div class="fw-bold">{{ $item->item_name }}</div>
                @if($item->description)<div class="item-desc">{{ $item->description }}</div>@endif
            </td>
            <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
            <td class="text-center" style="color:#6c757d;">{{ $item->unit }}</td>
            <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->unit_price, 2) }}</td>
            <td class="text-right fw-bold">{{ $company['currency_symbol'] }} {{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($quotation->subtotal, 2) }}</td>
        </tr>
        @if($quotation->discount > 0)
        <tr>
            <td>Discount</td>
            <td class="text-right" style="color:green;">- {{ $company['currency_symbol'] }} {{ number_format($quotation->discount, 2) }}</td>
        </tr>
        @endif
        @if($quotation->tax > 0)
        <tr>
            <td>Tax ({{ $quotation->tax }}%)</td>
            <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format(($quotation->subtotal - $quotation->discount) * $quotation->tax / 100, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-row">
            <td>GRAND TOTAL</td>
            <td class="text-right amount-accent">{{ $company['currency_symbol'] }} {{ number_format($quotation->grand_total, 2) }}</td>
        </tr>
    </table>

    <!-- Notes -->
    @if($quotation->notes)
    <div class="notes-section">
        <div class="section-title">Notes</div>
        <div class="notes-box">{{ $quotation->notes }}</div>
    </div>
    @endif

    @if($quotation->terms)
    <div class="notes-section">
        <div class="section-title">Terms & Conditions</div>
        <div class="notes-box">{{ $quotation->terms }}</div>
    </div>
    @endif

    <!-- Signature -->
    <div class="signature-row">
        <div class="sig-cell">
            <div class="sig-line">
                Authorized Signature &amp; Stamp<br>
                <strong>{{ $company['owner'] }}</strong><br>
                {{ $company['name'] }}
            </div>
        </div>
        <div class="sig-cell" style="text-align:right;">
            <div class="sig-line" style="border-top-color:#e2e8f0;color:#6c757d;">
                Customer Acceptance Signature<br>
                Name: ___________________<br>
                Date: ___________________
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="pdf-footer">
        {{ $company['name'] }} &nbsp;|&nbsp; {{ $company['address'] }} &nbsp;|&nbsp; {{ $company['phone'] }} &nbsp;|&nbsp; {{ $company['email'] }}
    </div>

</div>
</body>
</html>
