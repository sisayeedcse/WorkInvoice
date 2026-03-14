@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-people-fill"></i> Client Directory</div>
            <h1 class="hero-title">Customers</h1>
            <p class="hero-copy">Keep contact details, companies, quotations, and orders connected in one structured
                customer workspace.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('customers.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> Add Customer
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Total Customers</div>
                    <div class="summary-value">{{ $customers->count() }}</div>
                </div>
                <div class="summary-icon"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="summary-foot">Registered customer records</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Companies</div>
                    <div class="summary-value">
                        {{ $customers->filter(fn($customer) => filled($customer->company_name))->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-info"><i class="bi bi-buildings-fill"></i></div>
            </div>
            <div class="summary-foot">Customers linked to business names</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Quotation Activity</div>
                    <div class="summary-value">{{ $customers->sum('quotations_count') }}</div>
                </div>
                <div class="summary-icon summary-icon-accent"><i class="bi bi-file-earmark-text-fill"></i></div>
            </div>
            <div class="summary-foot">Total quotations across accounts</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Order Activity</div>
                    <div class="summary-value">{{ $customers->sum('orders_count') }}</div>
                </div>
                <div class="summary-icon summary-icon-success"><i class="bi bi-clipboard-check-fill"></i></div>
            </div>
            <div class="summary-foot">Total orders linked to customers</div>
        </div>
    </div>

    <div class="toolbar-card">
        <div class="toolbar-copy">Search, review, and maintain customer records from a single responsive table.</div>
        <div class="toolbar-actions">
            <a href="{{ route('customers.create') }}" class="btn btn-sm btn-accent">
                <i class="bi bi-plus-lg"></i> New Customer
            </a>
        </div>
    </div>

    <div class="table-card">
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
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                                <div class="empty-state-copy">No customers found. <a href="{{ route('customers.create') }}">Add
                                        your first customer.</a></div>
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