@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', $customer->name)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-person-badge"></i> Customer Profile</div>
            <h1 class="hero-title">{{ $customer->name }}</h1>
            <p class="hero-copy">Review customer details, recent quotations, and order history from one consolidated
                relationship view.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}" class="btn btn-accent btn-sm">
                <i class="bi bi-plus-lg me-1"></i>New Quote
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Quotations</div>
                    <div class="summary-value">{{ $customer->quotations->count() }}</div>
                </div>
                <div class="summary-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            </div>
            <div class="summary-foot">Commercial proposals for this customer</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Orders</div>
                    <div class="summary-value">{{ $customer->orders->count() }}</div>
                </div>
                <div class="summary-icon summary-icon-success"><i class="bi bi-clipboard-check-fill"></i></div>
            </div>
            <div class="summary-foot">Confirmed production jobs</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Company</div>
                    <div class="summary-value summary-value-compact">{{ $customer->company_name ?: 'Individual' }}</div>
                </div>
                <div class="summary-icon summary-icon-info"><i class="bi bi-buildings-fill"></i></div>
            </div>
            <div class="summary-foot">Associated business identity</div>
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">Contact</div>
                    <div class="summary-value summary-value-compact">{{ $customer->phone ?: 'Not set' }}</div>
                </div>
                <div class="summary-icon summary-icon-accent"><i class="bi bi-telephone-fill"></i></div>
            </div>
            <div class="summary-foot">Primary reachable number</div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Info Card -->
        <div class="col-12 col-lg-4">
            <div class="card record-sidebar">
                <div class="card-header"><i class="bi bi-person-fill me-2" style="color:var(--accent);"></i>Contact Details
                </div>
                <div class="card-body">
                    <div class="meta-list">
                        <div class="meta-item"><span class="meta-label">Name</span>
                            <div class="meta-value">{{ $customer->name }}</div>
                        </div>

                        @if($customer->company_name)
                            <div class="meta-item"><span class="meta-label">Company</span>
                                <div class="meta-value">{{ $customer->company_name }}</div>
                            </div>
                        @endif

                        @if($customer->phone)
                            <div class="meta-item"><span class="meta-label">Phone</span>
                                <div class="meta-value"><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></div>
                            </div>
                        @endif

                        @if($customer->email)
                            <div class="meta-item"><span class="meta-label">Email</span>
                                <div class="meta-value"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
                            </div>
                        @endif

                        @if($customer->address)
                            <div class="meta-item"><span class="meta-label">Address</span>
                                <div class="meta-value">{{ $customer->address }}</div>
                            </div>
                        @endif

                        @if($customer->notes)
                            <div class="meta-item"><span class="meta-label">Notes</span>
                                <div class="meta-value">{{ $customer->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity -->
        <div class="col-12 col-lg-8">
            <!-- Quotations -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-text me-2" style="color:var(--accent);"></i>Quotations
                        ({{ $customer->quotations->count() }})</span>
                    <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}"
                        class="btn btn-sm btn-outline-secondary">+ New</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->quotations->take(5) as $q)
                                <tr>
                                    <td><a href="{{ route('quotations.show', $q) }}"
                                            class="doc-number text-decoration-none">{{ $q->quotation_number }}</a></td>
                                    <td class="text-muted">{{ $q->date->format('d M Y') }}</td>
                                    <td>AED {{ number_format($q->grand_total, 2) }}</td>
                                    <td>{!! $q->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No quotations yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orders -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clipboard-check me-2" style="color:var(--accent);"></i>Orders
                        ({{ $customer->orders->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders->take(5) as $o)
                                <tr>
                                    <td><a href="{{ route('orders.show', $o) }}"
                                            class="doc-number text-decoration-none">{{ $o->order_number }}</a></td>
                                    <td class="text-muted">{{ $o->order_date->format('d M Y') }}</td>
                                    <td>AED {{ number_format($o->grand_total, 2) }}</td>
                                    <td>{!! $o->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection