@extends('layouts.app')
@section('title', 'Quotations')
@section('page-title', 'Quotations')

@section('content')
    <div class="page-header">
        <div>
            <h1>Quotations</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Create and manage customer quotations</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['draft', 'sent', 'accepted', 'rejected', 'converted'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('quotations.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
            <a href="{{ route('quotations.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg me-1"></i> New Quotation
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table id="quotationsTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                        <tr>
                            <td><a href="{{ route('quotations.show', $quotation) }}"
                                    class="doc-number text-decoration-none">{{ $quotation->quotation_number }}</a></td>
                            <td class="text-muted">{{ $quotation->date->format('d M Y') }}</td>
                            <td class="fw-medium">{{ $quotation->customer->name ?? '—' }}</td>
                            <td class="text-muted">{{ $quotation->items()->count() }}</td>
                            <td class="fw-semibold">AED {{ number_format($quotation->grand_total, 2) }}</td>
                            <td>{!! $quotation->status_badge !!}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-sm btn-light"
                                        title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-sm btn-light"
                                        title="Download PDF" target="_blank"><i class="bi bi-file-pdf text-danger"></i></a>
                                    <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-sm btn-light"
                                        title="Edit"><i class="bi bi-pencil"></i></a>
                                    @if($quotation->status !== 'converted')
                                        <form method="POST" action="{{ route('quotations.duplicate', $quotation) }}"
                                            class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-light" title="Duplicate"><i
                                                    class="bi bi-copy"></i></button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="d-inline"
                                        onsubmit="return confirm('Delete this quotation?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-2 opacity-25"></i>
                                No quotations yet. <a href="{{ route('quotations.create') }}">Create your first quotation.</a>
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
        new DataTable('#quotationsTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [3, 6] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search quotations...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_–_END_ of _TOTAL_ quotations',
                emptyTable: 'No quotations yet.',
                zeroRecords: 'No matching quotations found.'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush