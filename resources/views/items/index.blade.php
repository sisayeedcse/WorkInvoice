@extends('layouts.app')
@section('title', 'Services & Items')
@section('page-title', 'Services & Items')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-tools"></i> Service Library</div>
            <h1 class="hero-title">Services & Items</h1>
            <p class="hero-copy">Maintain a clean pricing library for faster quotations, orders, and invoices with consistent service definitions.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('items.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> Add Service
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Total Services</div>
                    <div class="summary-value">{{ $items->count() }}</div>
                </div>
                <div class="summary-icon"><i class="bi bi-tools"></i></div>
            </div>
            <div class="summary-foot">Items in the pricing library</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Categories</div>
                    <div class="summary-value">{{ $items->pluck('category')->filter()->unique()->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-info"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            </div>
            <div class="summary-foot">Distinct classification groups</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Average Price</div>
                    <div class="summary-value summary-accent">AED {{ number_format($items->avg('default_price') ?? 0, 0) }}</div>
                </div>
                <div class="summary-icon summary-icon-accent"><i class="bi bi-cash-stack"></i></div>
            </div>
            <div class="summary-foot">Default pricing benchmark</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Units Used</div>
                    <div class="summary-value">{{ $items->pluck('unit')->filter()->unique()->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-success"><i class="bi bi-rulers"></i></div>
            </div>
            <div class="summary-foot">Measurement types in use</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="itemsTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Default Price</th>
                        <th>Unit</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td><span class="badge bg-light text-muted">{{ $item->category ?: 'General' }}</span></td>
                            <td class="fw-semibold" style="color:var(--accent);">AED
                                {{ number_format($item->default_price, 2) }}</td>
                            <td class="text-muted">{{ $item->unit }}</td>
                            <td class="text-muted"
                                style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $item->description ?: '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-light me-1" title="Edit"><i
                                        class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('items.destroy', $item) }}" class="d-inline"
                                    onsubmit="return confirm('Delete {{ addslashes($item->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-tools"></i></div>
                                <div class="empty-state-copy">No services yet. <a href="{{ route('items.create') }}">Add your first service.</a></div>
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
        new DataTable('#itemsTable', {
            pageLength: 25,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search services...',
                lengthMenu: 'Show _MENU_ items',
                info: 'Showing _START_–_END_ of _TOTAL_ items',
                emptyTable: 'No services yet.',
                zeroRecords: 'No matching services found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush