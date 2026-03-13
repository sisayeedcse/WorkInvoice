@extends('layouts.app')
@section('title', 'Services & Items')
@section('page-title', 'Services & Items')

@section('content')
    <div class="page-header">
        <div>
            <h1>Services & Items Library</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Manage your frequently used services for quick selection</p>
        </div>
        <a href="{{ route('items.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i> Add Service
        </a>
    </div>

    <div class="card">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-tools fs-1 d-block mb-2 opacity-25"></i>
                                No services yet. <a href="{{ route('items.create') }}">Add your first service.</a>
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