@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('page-title', $invoice->invoice_number)

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
            <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
        </ol></nav>
        <h1>{{ $invoice->invoice_number }}</h1>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Download PDF
        </a>
        <button class="btn btn-outline-dark btn-sm print-hide" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <!-- Invoice Summary -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="doc-number" style="font-size:16px;">{{ $invoice->invoice_number }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $invoice->date->format('d M Y') }}</div>
                    </div>
                    {!! $invoice->status_badge !!}
                </div>

                <form method="POST" action="{{ route('invoices.update-status', $invoice) }}" class="d-flex gap-2 mb-3">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select form-select-sm">
                        @foreach(['draft','sent','partial','paid','overdue','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $invoice->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                </form>

                <hr class="my-3">
                <div style="font-size:13px;">
                    <div class="text-muted mb-1">Customer</div>
                    <div class="fw-semibold">{{ $invoice->customer->name }}</div>
                    @if($invoice->customer->phone)<div><a href="tel:{{ $invoice->customer->phone }}" class="text-muted">{{ $invoice->customer->phone }}</a></div>@endif
                </div>

                @if($invoice->due_date)
                <hr class="my-3">
                <div style="font-size:12.5px;" class="text-muted">Due Date: <strong class="{{ now() > $invoice->due_date && $invoice->balance > 0 ? 'text-danger' : 'text-body' }}">{{ $invoice->due_date->format('d M Y') }}</strong></div>
                @endif
            </div>
        </div>

        <!-- Totals -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Payment Summary</div>
            <div class="card-body" style="font-size:13.5px;">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Invoice Total</span><span class="fw-semibold">AED {{ number_format($invoice->grand_total, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2 text-success"><span>Paid</span><span class="fw-semibold">AED {{ number_format($invoice->paid_amount, 2) }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                    <span>Balance Due</span>
                    <span class="{{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">AED {{ number_format($invoice->balance, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Add Payment -->
        @if($invoice->balance > 0)
        <div class="card">
            <div class="card-header"><i class="bi bi-cash-coin me-2" style="color:var(--accent);"></i>Record Payment</div>
            <div class="card-body">
                <form method="POST" action="{{ route('invoices.add-payment', $invoice) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Amount (AED) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01"
                               min="0.01" max="{{ $invoice->balance }}"
                               value="{{ number_format($invoice->balance, 2, '.', '') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Method</label>
                        <select name="method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online Payment</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference#</label>
                        <input type="text" name="reference" class="form-control" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i> Record Payment
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 col-lg-8">
        <!-- Items -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Line Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr class="table-light"><th>#</th><th>Item</th><th>Description</th><th class="text-center">Qty</th><th class="text-center">Unit</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @foreach($invoice->items as $i => $item)
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

        <!-- Payment History -->
        @if($invoice->payments->count())
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2" style="color:var(--accent);"></i>Payment History</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr class="table-light"><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
                    <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="fw-semibold text-success">AED {{ number_format($payment->amount, 2) }}</td>
                        <td><span class="badge bg-light text-dark">{{ $payment->method_label }}</span></td>
                        <td class="text-muted">{{ $payment->reference ?: '—' }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
