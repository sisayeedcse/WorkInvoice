@extends('layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
    <div class="page-header">
        <div>
            <h1>Orders</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Track all workshop orders and job statuses</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['pending', 'approved', 'in_progress', 'completed', 'delivered', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('orders.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
            <a href="{{ route('orders.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg me-1"></i> New Order
            </a>
        </div>
    </div>

    <div class="card">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2 opacity-25"></i>
                                No orders yet.
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