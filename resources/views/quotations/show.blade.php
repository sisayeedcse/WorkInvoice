@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('page-title', $quotation->quotation_number)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-file-earmark-text"></i> Quotation Record</div>
            <h1 class="hero-title">{{ $quotation->quotation_number }}</h1>
            <p class="hero-copy">Review pricing, customer context, and conversion readiness from a single premium proposal
                view.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-pdf me-1"></i> Download PDF
            </a>
            <button class="btn btn-outline-dark btn-sm print-hide" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            @if($quotation->status !== 'converted')
                <form method="POST" action="{{ route('quotations.convert-to-order', $quotation) }}" class="d-inline"
                    onsubmit="return confirm('Convert this quotation to an order?')">
                    @csrf
                    <button class="btn btn-success btn-sm"><i class="bi bi-arrow-right-circle me-1"></i> Convert to
                        Order</button>
                </form>
            @endif
            <form method="POST" action="{{ route('quotations.duplicate', $quotation) }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-copy me-1"></i> Duplicate</button>
            </form>
            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left -->
        <div class="col-12 col-lg-4">
            <div class="card mb-3 record-sidebar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="doc-number" style="font-size:18px;">{{ $quotation->quotation_number }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $quotation->date->format('d M Y') }}</div>
                        </div>
                        {!! $quotation->status_badge !!}
                    </div>

                    <!-- Status Update -->
                    <form method="POST" action="{{ route('quotations.update-status', $quotation) }}"
                        class="d-flex gap-2 mb-3">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['draft', 'sent', 'accepted', 'rejected'] as $s)
                                <option value="{{ $s }}" {{ $quotation->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                    </form>

                    <div class="meta-list mt-3">
                        <div class="meta-item"><span class="meta-label">Customer</span>
                            <div class="meta-value">{{ $quotation->customer->name }}</div>
                        </div>
                        @if($quotation->customer->company_name)
                            <div class="meta-item"><span class="meta-label">Company</span>
                                <div class="meta-value">{{ $quotation->customer->company_name }}</div>
                        </div>@endif
                        @if($quotation->customer->phone)
                            <div class="meta-item"><span class="meta-label">Phone</span>
                                <div class="meta-value"><a
                                        href="tel:{{ $quotation->customer->phone }}">{{ $quotation->customer->phone }}</a></div>
                        </div>@endif
                        @if($quotation->valid_until)
                            <div class="meta-item"><span class="meta-label">Valid Until</span>
                                <div class="meta-value">{{ $quotation->valid_until->format('d M Y') }}</div>
                        </div>@endif
                        @if($quotation->prepared_by)
                            <div class="meta-item"><span class="meta-label">Prepared By</span>
                                <div class="meta-value">{{ $quotation->prepared_by }}</div>
                        </div>@endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Summary</div>
                <div class="card-body" style="font-size:13.5px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>AED {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    @if($quotation->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span>
                            <span>- AED {{ number_format($quotation->discount, 2) }}</span>
                        </div>
                    @endif
                    @if($quotation->tax > 0)
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Tax ({{ $quotation->tax }}%)</span>
                            <span>AED
                                {{ number_format(($quotation->subtotal - $quotation->discount) * $quotation->tax / 100, 2) }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                        <span>Grand Total</span>
                        <span style="color:var(--accent);">AED {{ number_format($quotation->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Items + Notes -->
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
                            @foreach($quotation->items as $i => $item)
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

            @if($quotation->notes)
                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-sticky me-2" style="color:var(--accent);"></i>Notes</div>
                    <div class="card-body" style="font-size:13.5px;">{{ $quotation->notes }}</div>
                </div>
            @endif

            @if($quotation->terms)
                <div class="card">
                    <div class="card-header"><i class="bi bi-file-text me-2" style="color:var(--accent);"></i>Terms & Conditions
                    </div>
                    <div class="card-body" style="font-size:13px;white-space:pre-line;">{{ $quotation->terms }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection