@extends('layouts.app')
@section('title', $sale->sale_number)
@section('page-title', $sale->sale_number)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-receipt"></i> Sale Details</div>
            <h1 class="hero-title">{{ $sale->sale_number }}</h1>
            <p class="hero-copy">Review sold items, totals, and payment details.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-danger btn-sm" target="_blank"><i
                    class="bi bi-printer me-1"></i>Print Receipt</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Date</div>
                        <div class="summary-value">{{ $sale->sale_date?->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Customer</div>
                        <div class="summary-value">{{ $sale->display_customer }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Items Sold</div>
                        <div class="summary-value">{{ number_format($sale->items->sum('quantity'), 3) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Grand Total</div>
                        <div class="summary-value">AED {{ number_format($sale->grand_total, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <span><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Sale Items</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Unit</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-center">{{ number_format($item->quantity, 3) }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-end">AED {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-semibold">AED {{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Subtotal</th>
                        <th class="text-end">AED {{ number_format($sale->subtotal, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Discount</th>
                        <th class="text-end">AED {{ number_format($sale->discount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Tax</th>
                        <th class="text-end">AED {{ number_format($sale->tax, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Grand Total</th>
                        <th class="text-end">AED {{ number_format($sale->grand_total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection