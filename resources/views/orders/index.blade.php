@extends('layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-clipboard-check-fill"></i> Production Workload</div>
            <h1 class="hero-title">Orders</h1>
            <p class="hero-copy">Track approved jobs, delivery commitments, and workshop execution status from a more
                readable production board.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('orders.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> New Order
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Total Orders</div>
                    <div class="summary-value">{{ $orders->count() }}</div>
                </div>
                <div class="summary-icon"><i class="bi bi-clipboard-data"></i></div>
            </div>
            <div class="summary-foot">Orders in the system</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Active Jobs</div>
                    <div class="summary-value">
                        {{ $orders->whereIn('status', ['pending', 'approved', 'in_progress'])->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-info"><i class="bi bi-hammer"></i></div>
            </div>
            <div class="summary-foot">Awaiting or under production</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Completed</div>
                    <div class="summary-value summary-success">
                        {{ $orders->whereIn('status', ['completed', 'delivered'])->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-success"><i class="bi bi-patch-check-fill"></i></div>
            </div>
            <div class="summary-foot">Finished or delivered orders</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Order Value</div>
                    <div class="summary-value summary-accent">AED {{ number_format($orders->sum('grand_total'), 0) }}</div>
                </div>
                <div class="summary-icon summary-icon-accent"><i class="bi bi-cash-coin"></i></div>
            </div>
            <div class="summary-foot">Combined value of current list</div>
        </div>
    </div>

    <div class="toolbar-card">
        <div class="toolbar-copy">Filter order status and manage workshop workload from one responsive table.</div>
        <div class="toolbar-actions">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['pending', 'approved', 'in_progress', 'completed', 'delivered', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('orders.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="ordersTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}"
                                    class="doc-number text-decoration-none">{{ $order->order_number }}</a></td>
                            <td class="text-muted">{{ $order->order_date->format('d M Y') }}</td>
                            <td class="fw-medium">{{ $order->customer->name ?? '—' }}</td>
                            <td class="fw-semibold">AED {{ number_format($order->grand_total, 2) }}</td>
                            <td class="text-muted">{{ $order->delivery_date ? $order->delivery_date->format('d M Y') : '—' }}
                            </td>
                            <td>{!! $order->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light me-1" title="View"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-light me-1" title="Edit"><i
                                        class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('orders.destroy', $order) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this order?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-clipboard-x"></i></div>
                                <div class="empty-state-copy">No orders yet.</div>
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
        new DataTable('#ordersTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search orders...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_–_END_ of _TOTAL_ orders',
                emptyTable: 'No orders yet.',
                zeroRecords: 'No matching orders found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush