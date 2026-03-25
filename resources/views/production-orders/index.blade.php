@extends('layouts.app')
@section('title', 'Production')
@section('page-title', 'Production')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-gear-wide-connected"></i> Production</div>
            <h1 class="hero-title">Production Orders</h1>
            <p class="hero-copy">Track manufacturing progress and raw material usage for finished products.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('production-orders.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg"></i> New
                Production Order</a>
        </div>
    </div>

    <div class="toolbar-card mb-3">
        <div class="toolbar-copy">Filter by production status.</div>
        <div class="toolbar-actions">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if(request('status'))<a href="{{ route('production-orders.index') }}"
                class="btn btn-sm btn-outline-secondary">Clear</a>@endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="productionTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Production No</th>
                        <th>Date</th>
                        <th>Finished Product</th>
                        <th>Target Qty</th>
                        <th>Produced Qty</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $production)
                        <tr>
                            <td><a href="{{ route('production-orders.show', $production) }}"
                                    class="doc-number text-decoration-none">{{ $production->production_number }}</a></td>
                            <td>{{ $production->production_date?->format('d M Y') }}</td>
                            <td>{{ $production->finishedItem?->name }}</td>
                            <td>{{ number_format($production->quantity_to_produce, 3) }}</td>
                            <td>{{ number_format($production->quantity_produced, 3) }}</td>
                            <td>{!! $production->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('production-orders.show', $production) }}"
                                    class="btn btn-sm btn-light me-1"><i class="bi bi-eye"></i></a>
                                @if($production->status === 'pending')
                                    <form method="POST" action="{{ route('production-orders.start', $production) }}"
                                        class="d-inline me-1">
                                        @csrf
                                        <button class="btn btn-sm btn-light text-primary" title="Start"><i
                                                class="bi bi-play-fill"></i></button>
                                    </form>
                                @endif
                                @if(in_array($production->status, ['pending', 'in_progress']))
                                    <form method="POST" action="{{ route('production-orders.complete', $production) }}"
                                        class="d-inline" onsubmit="return confirm('Complete production and update stock?')">
                                        @csrf
                                        <button class="btn btn-sm btn-light text-success" title="Complete"><i
                                                class="bi bi-check2-circle"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-copy">No production orders found.</div>
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
        new DataTable('#productionTable', {
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: [6] }],
            language: { search: '', searchPlaceholder: 'Search production orders...' },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush