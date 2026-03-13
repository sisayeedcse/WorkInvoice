@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', $customer->name)

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active">{{ $customer->name }}</li>
        </ol></nav>
        <h1>{{ $customer->name }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Quote
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Info Card -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-fill me-2" style="color:var(--accent);"></i>Contact Details</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:13.5px;">
                    <dt class="col-5 text-muted fw-normal">Name</dt>
                    <dd class="col-7 fw-semibold mb-2">{{ $customer->name }}</dd>

                    @if($customer->company_name)
                    <dt class="col-5 text-muted fw-normal">Company</dt>
                    <dd class="col-7 mb-2">{{ $customer->company_name }}</dd>
                    @endif

                    @if($customer->phone)
                    <dt class="col-5 text-muted fw-normal">Phone</dt>
                    <dd class="col-7 mb-2"><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></dd>
                    @endif

                    @if($customer->email)
                    <dt class="col-5 text-muted fw-normal">Email</dt>
                    <dd class="col-7 mb-2"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></dd>
                    @endif

                    @if($customer->address)
                    <dt class="col-5 text-muted fw-normal">Address</dt>
                    <dd class="col-7 mb-2">{{ $customer->address }}</dd>
                    @endif

                    @if($customer->notes)
                    <dt class="col-5 text-muted fw-normal">Notes</dt>
                    <dd class="col-7 mb-2">{{ $customer->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Activity -->
    <div class="col-12 col-lg-8">
        <!-- Quotations -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-2" style="color:var(--accent);"></i>Quotations ({{ $customer->quotations->count() }})</span>
                <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-outline-secondary">+ New</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Number</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($customer->quotations->take(5) as $q)
                    <tr>
                        <td><a href="{{ route('quotations.show', $q) }}" class="doc-number text-decoration-none">{{ $q->quotation_number }}</a></td>
                        <td class="text-muted">{{ $q->date->format('d M Y') }}</td>
                        <td>AED {{ number_format($q->grand_total, 2) }}</td>
                        <td>{!! $q->status_badge !!}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No quotations yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-check me-2" style="color:var(--accent);"></i>Orders ({{ $customer->orders->count() }})</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Number</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($customer->orders->take(5) as $o)
                    <tr>
                        <td><a href="{{ route('orders.show', $o) }}" class="doc-number text-decoration-none">{{ $o->order_number }}</a></td>
                        <td class="text-muted">{{ $o->order_date->format('d M Y') }}</td>
                        <td>AED {{ number_format($o->grand_total, 2) }}</td>
                        <td>{!! $o->status_badge !!}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
