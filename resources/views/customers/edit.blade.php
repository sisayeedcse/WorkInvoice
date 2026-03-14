@extends('layouts.app')
@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1>Edit: {{ $customer->name }}</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Update customer details and keep the account record accurate.
            </p>
        </div>
    </div>

    <div class="form-shell">
        <aside class="form-sidebar">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Customer Record</div>
                        <div class="summary-value">{{ $customer->quotations_count ?? $customer->quotations()->count() }}
                        </div>
                    </div>
                    <div class="summary-icon"><i class="bi bi-journal-richtext"></i></div>
                </div>
                <div class="summary-foot">Linked quotations for this customer</div>
            </div>
            <div class="surface-note">
                <strong>Keep this current</strong><br>
                Updated phone, email, and company details flow into the rest of the customer lifecycle screens.
            </div>
        </aside>

        <div class="form-main">
            <div class="card">
                <div class="card-header"><i class="bi bi-pencil-square me-2" style="color:var(--accent);"></i>Customer
                    Information</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customers.update', $customer) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $customer->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ old('company_name', $customer->company_name) }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $customer->phone) }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $customer->email) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control"
                                    rows="2">{{ old('address', $customer->address) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control"
                                    rows="2">{{ old('notes', $customer->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update
                                Customer</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection