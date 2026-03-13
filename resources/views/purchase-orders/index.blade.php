@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
    <div class="page-header">
        <div>
            <h1>Purchase Orders</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Manage supplier and subcontract purchase orders</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['draft', 'sent', 'received', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('purchase-orders.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg me-1"></i> New PO
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table id="poTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td><a href="{{ route('purchase-orders.show', $po) }}"
                                    class="doc-number text-decoration-none">{{ $po->po_number }}</a></td>
                            <td class="text-muted">{{ $po->date->format('d M Y') }}</td>
                            <td class="fw-medium">{{ $po->supplier_name }}</td>
                            <td class="fw-semibold">AED {{ number_format($po->grand_total, 2) }}</td>
                            <td>{!! $po->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-light me-1"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('purchase-orders.pdf', $po) }}" class="btn btn-sm btn-light me-1"
                                    target="_blank"><i class="bi bi-file-pdf text-danger"></i></a>
                                <form method="POST" action="{{ route('purchase-orders.destroy', $po) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this PO?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>
                                No purchase orders yet.
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
        new DataTable('#poTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search purchase orders...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_–_END_ of _TOTAL_ purchase orders',
                emptyTable: 'No purchase orders yet.',
                zeroRecords: 'No matching purchase orders found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush