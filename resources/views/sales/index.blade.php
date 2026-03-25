@extends('layouts.app')
@section('title', 'Sales')
@section('page-title', 'Sales')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-receipt"></i> POS Sales</div>
            <h1 class="hero-title">Sales</h1>
            <p class="hero-copy">Track all daily retail sales, totals, and receipt print status.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('pos.index') }}" class="btn btn-accent"><i class="bi bi-cart-plus"></i> Open POS</a>
        </div>
    </div>

    <div class="toolbar-card mb-3">
        <div class="toolbar-copy">Filter by date and payment method.</div>
        <div class="toolbar-actions">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                <select name="payment_method" class="form-select form-select-sm" style="width:auto;">
                    <option value="">All Payments</option>
                    @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'online' => 'Online', 'cheque' => 'Cheque', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" {{ request('payment_method') === $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Apply</button>
                @if(request()->filled('date_from') || request()->filled('date_to') || request()->filled('payment_method'))
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="salesTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Sale No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Receipt</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td><a class="doc-number text-decoration-none"
                                    href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                            <td>{{ $sale->sale_date?->format('d M Y') }}</td>
                            <td>{{ $sale->display_customer }}</td>
                            <td>{{ number_format($sale->items->sum('quantity'), 3) }}</td>
                            <td class="fw-semibold">AED {{ number_format($sale->grand_total, 2) }}</td>
                            <td>{!! $sale->payment_method_badge !!}</td>
                            <td>{!! $sale->receipt_printed ? '<span class="badge bg-success">Printed</span>' : '<span class="badge bg-secondary">Not Printed</span>' !!}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-light me-1"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-light me-1"
                                    target="_blank"><i class="bi bi-printer"></i></a>
                                <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="d-inline"
                                    onsubmit="return confirm('Void this sale and restore stock?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-copy">No sales found.</div>
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
        new DataTable('#salesTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: [7] }],
            language: { search: '', searchPlaceholder: 'Search sales...' },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush