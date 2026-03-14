@extends('layouts.app')
@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-receipt-cutoff"></i> Billing & Collections</div>
            <h1 class="hero-title">Invoices</h1>
            <p class="hero-copy">Monitor billing, payment collection, and outstanding balances with a more structured finance workspace.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('invoices.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> New Invoice
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Total Invoices</div>
                    <div class="summary-value">{{ $invoices->count() }}</div>
                </div>
                <div class="summary-icon"><i class="bi bi-receipt-cutoff"></i></div>
            </div>
            <div class="summary-foot">Billing documents in view</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Collected</div>
                    <div class="summary-value summary-success">AED {{ number_format($invoices->sum('paid_amount'), 0) }}</div>
                </div>
                <div class="summary-icon summary-icon-success"><i class="bi bi-wallet2"></i></div>
            </div>
            <div class="summary-foot">Payments already recorded</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Balance Due</div>
                    <div class="summary-value summary-danger">AED {{ number_format($invoices->sum('balance'), 0) }}</div>
                </div>
                <div class="summary-icon summary-icon-danger"><i class="bi bi-exclamation-diamond-fill"></i></div>
            </div>
            <div class="summary-foot">Open receivables still due</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Overdue</div>
                    <div class="summary-value">{{ $invoices->where('status', 'overdue')->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-info"><i class="bi bi-clock-history"></i></div>
            </div>
            <div class="summary-foot">Invoices marked overdue</div>
        </div>
    </div>

    <div class="toolbar-card">
        <div class="toolbar-copy">Filter invoice status and manage collection activity from a single responsive billing table.</div>
        <div class="toolbar-actions">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('invoices.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="invoicesTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}"
                                    class="doc-number text-decoration-none">{{ $invoice->invoice_number }}</a></td>
                            <td class="text-muted">{{ $invoice->date->format('d M Y') }}</td>
                            <td class="fw-medium">{{ $invoice->customer->name ?? '—' }}</td>
                            <td>AED {{ number_format($invoice->grand_total, 2) }}</td>
                            <td class="text-success fw-semibold">AED {{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="{{ $invoice->balance > 0 ? 'text-danger fw-semibold' : 'text-success' }}">AED
                                {{ number_format($invoice->balance, 2) }}</td>
                            <td>{!! $invoice->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light me-1"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-light me-1"
                                    target="_blank"><i class="bi bi-file-pdf text-danger"></i></a>
                                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this invoice?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-receipt"></i></div>
                                <div class="empty-state-copy">No invoices yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        new DataTable('#invoicesTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search invoices...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_–_END_ of _TOTAL_ invoices',
                emptyTable: 'No invoices yet.',
                zeroRecords: 'No matching invoices found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush