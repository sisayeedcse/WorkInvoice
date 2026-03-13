@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
    <div class="page-header">
        <div>
            <h1>Customers</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Manage your customer database</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i> Add Customer
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table id="customersTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Quotes</th>
                        <th>Orders</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $i => $customer)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('customers.show', $customer) }}" class="fw-semibold text-decoration-none"
                                    style="color:var(--primary);">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $customer->company_name ?: '—' }}</td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td class="text-muted">{{ $customer->email ?: '—' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $customer->quotations_count }}</span></td>
                            <td><span class="badge bg-light text-dark">{{ $customer->orders_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-light me-1"
                                    title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-light me-1"
                                    title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline"
                                    onsubmit="return confirm('Delete {{ addslashes($customer->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                No customers found. <a href="{{ route('customers.create') }}">Add your first customer.</a>
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
        new DataTable('#customersTable', {
            pageLength: 25,
            order: [],
            columnDefs: [
                { orderable: false, targets: [0, 7] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search customers...',
                lengthMenu: 'Show _MENU_ customers',
                info: 'Showing _START_–_END_ of _TOTAL_ customers',
                emptyTable: 'No customers found.',
                zeroRecords: 'No matching customers found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush