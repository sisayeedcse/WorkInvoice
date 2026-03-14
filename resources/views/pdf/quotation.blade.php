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
            color: #001b44;
            font-size: 9pt;
        }

        .page {
            padding: 26px 26px 34px;
        }

        .top-table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-table td {
            vertical-align: top;
        }

        .logo-cell {
            width: 16%;
        }

        .company-cell {
            width: 58%;
            text-align: center;
        }

        .title-cell {
            width: 26%;
            text-align: right;
        }

        .logo {
            max-width: 70px;
            max-height: 70px;
            object-fit: contain;
        }

        .company-name {
            font-size: 17pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .owner-name {
            font-size: 11pt;
            margin-top: 2px;
        }

        .company-meta {
            font-size: 8.7pt;
            line-height: 1.35;
        }

        .title {
            font-size: 15pt;
            font-weight: bold;
            margin-top: 24px;
        }

        .divider {
            border-top: 1px solid #a8afb9;
            margin: 20px 0 14px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .meta-table td {
            vertical-align: top;
        }

        .to-cell {
            width: 58%;
        }

        .ref-cell {
            width: 42%;
        }

        .to-label {
            font-size: 12pt;
            font-weight: bold;
        }

        .to-name {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.35;
        }

        .ref-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .ref-inner td {
            font-size: 11pt;
            padding: 1px 0;
        }

        .ref-key {
            width: 58%;
            text-align: right;
            font-weight: bold;
            padding-right: 12px;
        }

        .ref-val {
            width: 42%;
            text-align: right;
        }

        .paragraph {
            font-size: 9.3pt;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table thead th {
            background: #eef1f4;
            border-top: 1px solid #a8afb9;
            border-bottom: 1px solid #a8afb9;
            padding: 7px 10px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }

        .items-table tbody td {
            padding: 9px 10px;
            font-size: 10pt;
            border-bottom: 1px dotted #b9bec6;
            vertical-align: top;
        }

        .description {
            font-weight: bold;
        }

        .sub-description {
            margin-top: 2px;
            font-size: 8pt;
            color: #526074;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-wrap {
            width: 100%;
            margin-top: 0;
        }

        .total-spacer {
            width: 49%;
            display: table-cell;
        }

        .total-box-cell {
            width: 51%;
            display: table-cell;
        }

        .total-box {
            width: 100%;
            border-collapse: collapse;
        }

        .total-box td {
            border-top: 1px solid #a8afb9;
            border-bottom: 1px solid #a8afb9;
            padding: 8px 10px;
            font-size: 10pt;
        }

        .total-box .label {
            text-transform: uppercase;
        }

        .total-box .value {
            text-align: right;
        }

        .closing {
            margin-top: 22px;
            font-size: 9.3pt;
        }

        .terms-block {
            margin-top: 26px;
            font-size: 8.8pt;
            color: #001b44;
        }

        .terms-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .terms-content {
            line-height: 1.5;
        }

        .terms-list {
            margin: 0;
            padding-left: 18px;
        }

        .terms-list li {
            text-align: left;
        }

        .company-signoff {
            margin-top: 46px;
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
        }

        .signature-block {
            margin-top: 56px;
            text-align: right;
        }

        .signature-space {
            height: 70px;
        }

        .signature-text {
            text-align: right;
            font-size: 9.5pt;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="page">
        <table class="top-table">
            <tr>
                <td class="logo-cell">
                    @if(!empty($company['logo_base64']))
                        <img src="{{ $company['logo_base64'] }}" alt="Logo" class="logo">
                    @endif
                </td>
                <td class="company-cell">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="owner-name">{{ $company['owner'] }}</div>
                    <div class="company-meta">
                        {{ $company['address'] }}<br>
                        {{ $company['phone'] }} &nbsp;&nbsp; {{ $company['email'] }}
                    </div>
                </td>
                <td class="title-cell">
                    <div class="title">Quotation</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="meta-table">
            <tr>
                <td class="to-cell">
                    <div class="to-label">To,</div>
                    <div class="to-name">
                        {{ $quotation->customer->company_name ?: $quotation->customer->name }}
                        @if($quotation->customer->company_name && $quotation->customer->name !== $quotation->customer->company_name)<br>{{ $quotation->customer->name }}@endif
                    </div>
                </td>
                <td class="ref-cell">
                    <table class="ref-inner">
                        <tr>
                            <td class="ref-key">Quotation#</td>
                            <td class="ref-val">{{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td class="ref-key">Date:</td>
                            <td class="ref-val">{{ $quotation->date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="paragraph">Dear Sir,</div>
        <div class="paragraph">Thank you for your valuable inquiry. We are pleased to quote as below:</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:6%;" class="text-center">#</th>
                    <th style="width:50%;">Description</th>
                    <th style="width:12%;" class="text-center">Qty</th>
                    <th style="width:16%;" class="text-right">Price</th>
                    <th style="width:16%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                            <div class="description">{{ $item->item_name }}</div>
                            @if($item->description)
                            <div class="sub-description">{{ $item->description }}</div>@endif
                        </td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                        <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right">{{ $company['currency_symbol'] }} {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="total-wrap">
            <tr>
                <td class="total-spacer"></td>
                <td class="total-box-cell">
                    <table class="total-box">
                        @if($quotation->discount > 0 || $quotation->tax > 0)
                            <tr>
                                <td class="label">Subtotal</td>
                                <td class="value">{{ $company['currency_symbol'] }}
                                    {{ number_format($quotation->subtotal, 2) }}</td>
                            </tr>
                        @endif
                        @if($quotation->discount > 0)
                            <tr>
                                <td class="label">Discount</td>
                                <td class="value">- {{ $company['currency_symbol'] }}
                                    {{ number_format($quotation->discount, 2) }}</td>
                            </tr>
                        @endif
                        @if($quotation->tax > 0)
                            <tr>
                                <td class="label">Tax ({{ $quotation->tax }}%)</td>
                                <td class="value">{{ $company['currency_symbol'] }}
                                    {{ number_format(($quotation->subtotal - $quotation->discount) * $quotation->tax / 100, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="label">Grand Total</td>
                            <td class="value">{{ $company['currency_symbol'] }}
                                {{ number_format($quotation->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="closing">We hope you find our offer to be in line with your requirement.</div>

        @php
            $termsText = trim((string) $quotation->terms);
            if ($termsText === '') {
                $termsText = '50% advance payment in cash.';
            }
        @endphp

        @if($termsText !== '')
            <div class="terms-block">
                <div class="terms-title">Terms &amp; Conditions:</div>
                <div class="terms-content" style="white-space: pre-line;">{{ $termsText }}</div>
            </div>
        @endif

        <div class="company-signoff">For, {{ strtoupper($company['name']) }}</div>

        <div class="signature-block">
            <div class="signature-space"></div>
            <div class="signature-text">Authorized Signature</div>
        </div>
    </div>

    </div>
</body>

</html>