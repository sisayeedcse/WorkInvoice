@extends('layouts.app')
@section('title', $purchaseOrder->po_number)
@section('page-title', $purchaseOrder->po_number)

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
            <li class="breadcrumb-item active">{{ $purchaseOrder->po_number }}</li>
        </ol></nav>
        <h1>{{ $purchaseOrder->po_number }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Download PDF
        </a>
        <button class="btn btn-outline-dark btn-sm print-hide" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="doc-number" style="font-size:17px;">{{ $purchaseOrder->po_number }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $purchaseOrder->date->format('d M Y') }}</div>
                    </div>
                    {!! $purchaseOrder->status_badge !!}
                </div>

                <form method="POST" action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" class="d-flex gap-2 mb-3">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select form-select-sm">
                        @foreach(['draft','sent','received','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $purchaseOrder->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                </form>

                <hr class="my-3">
                <div style="font-size:13px;">
                    <div class="text-muted mb-1 fw-semibold">Supplier</div>
                    <div class="fw-semibold">{{ $purchaseOrder->supplier_name }}</div>
                    @if($purchaseOrder->supplier_phone)<div><a href="tel:{{ $purchaseOrder->supplier_phone }}" class="text-muted">{{ $purchaseOrder->supplier_phone }}</a></div>@endif
                    @if($purchaseOrder->supplier_email)<div class="text-muted">{{ $purchaseOrder->supplier_email }}</div>@endif
                    @if($purchaseOrder->supplier_address)<div class="text-muted mt-1">{{ $purchaseOrder->supplier_address }}</div>@endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Summary</div>
            <div class="card-body" style="font-size:13.5px;">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>AED {{ number_format($purchaseOrder->subtotal, 2) }}</span></div>
                @if($purchaseOrder->discount > 0)<div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>- AED {{ number_format($purchaseOrder->discount, 2) }}</span></div>@endif
                @if($purchaseOrder->tax > 0)<div class="d-flex justify-content-between mb-2 text-muted"><span>Tax ({{ $purchaseOrder->tax }}%)</span><span>AED {{ number_format(($purchaseOrder->subtotal - $purchaseOrder->discount) * $purchaseOrder->tax / 100, 2) }}</span></div>@endif
                <hr>
                <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                    <span>Grand Total</span>
                    <span style="color:var(--accent);">AED {{ number_format($purchaseOrder->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr class="table-light"><th>#</th><th>Item</th><th>Description</th><th class="text-center">Qty</th><th class="text-center">Unit</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @foreach($purchaseOrder->items as $i => $item)
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

        @if($purchaseOrder->notes || $purchaseOrder->terms)
        <div class="card">
            <div class="card-body">
                @if($purchaseOrder->notes)
                <div class="mb-3"><strong style="font-size:13px;">Notes</strong><div class="text-muted mt-1" style="font-size:13.5px;">{{ $purchaseOrder->notes }}</div></div>
                @endif
                @if($purchaseOrder->terms)
                <div><strong style="font-size:13px;">Terms & Conditions</strong><div class="text-muted mt-1" style="font-size:13px;white-space:pre-line;">{{ $purchaseOrder->terms }}</div></div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
