@extends('layouts.app')
@section('title', $order->order_number)
@section('page-title', $order->order_number)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-clipboard-check"></i> Order Record</div>
            <h1 class="hero-title">{{ $order->order_number }}</h1>
            <p class="hero-copy">Track customer details, production status, delivery timing, and linked invoicing from one
                operational workspace.</p>
        </div>
        <div class="hero-actions">
            <button class="btn btn-outline-dark btn-sm print-hide" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            @if(!$order->invoice)
                <form method="POST" action="{{ route('orders.convert-to-invoice', $order) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm"><i class="bi bi-receipt me-1"></i> Create Invoice</button>
                </form>
            @else
                <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-receipt me-1"></i> View Invoice
                </a>
            @endif
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card mb-3 record-sidebar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="doc-number" style="font-size:17px;">{{ $order->order_number }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $order->order_date->format('d M Y') }}</div>
                        </div>
                        {!! $order->status_badge !!}
                    </div>

                    <!-- Status Update -->
                    <form method="POST" action="{{ route('orders.update-status', $order) }}" class="d-flex gap-2 mb-3">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['pending', 'approved', 'in_progress', 'completed', 'delivered', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                    </form>

                    <div class="meta-list mt-3">
                        <div class="meta-item"><span class="meta-label">Customer</span>
                            <div class="meta-value">{{ $order->customer->name }}</div>
                        </div>
                        @if($order->customer->phone)
                            <div class="meta-item"><span class="meta-label">Phone</span>
                                <div class="meta-value"><a
                                        href="tel:{{ $order->customer->phone }}">{{ $order->customer->phone }}</a></div>
                        </div>@endif
                        @if($order->delivery_date)
                            <div class="meta-item"><span class="meta-label">Delivery Date</span>
                                <div class="meta-value">{{ $order->delivery_date->format('d M Y') }}</div>
                        </div>@endif
                        @if($order->quotation)
                            <div class="meta-item"><span class="meta-label">Source Quotation</span>
                                <div class="meta-value"><a href="{{ route('quotations.show', $order->quotation) }}"
                                        class="doc-number">{{ $order->quotation->quotation_number }}</a></div>
                        </div>@endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Summary</div>
                <div class="card-body" style="font-size:13.5px;">
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>AED
                            {{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>- AED
                    {{ number_format($order->discount, 2) }}</span></div>@endif
                    @if($order->tax > 0)
                        <div class="d-flex justify-content-between mb-2 text-muted"><span>Tax
                                ({{ $order->tax }}%)</span><span>AED
                                {{ number_format(($order->subtotal - $order->discount) * $order->tax / 100, 2) }}</span></div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                        <span>Grand Total</span>
                        <span style="color:var(--accent);">AED {{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Line Items</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th>Service / Item</th>
                                <th>Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Unit</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $i => $item)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-medium">{{ $item->item_name }}</td>
                                    <td class="text-muted">{{ $item->description ?: '—' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center text-muted">{{ $item->unit }}</td>
                                    <td class="text-end">AED {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-semibold">AED {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-sticky me-2" style="color:var(--accent);"></i>Notes</div>
                    <div class="card-body" style="font-size:13.5px;">{{ $order->notes }}</div>
                </div>
            @endif

            @if($order->delivery_info)
                <div class="card">
                    <div class="card-header"><i class="bi bi-truck me-2" style="color:var(--accent);"></i>Delivery Information
                    </div>
                    <div class="card-body" style="font-size:13.5px;">{{ $order->delivery_info }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection