@extends('layouts.app')
@section('title', 'Add Customer')
@section('page-title', 'Add Customer')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li><li class="breadcrumb-item active">New Customer</li></ol></nav>
        <h1>Add New Customer</h1>
        <p class="text-muted mb-0" style="font-size:13px;">Create a complete customer profile for faster quotations, orders, and billing.</p>
    </div>
</div>

<div class="form-shell">
    <aside class="form-sidebar">
        <div class="surface-note">
            <strong>Best practice</strong><br>
            Capture the core contact details now so the customer can be selected instantly in quotations, orders, and invoices.
        </div>
        <div class="summary-card">
            <div class="summary-card-head">
                <div class="summary-card-body">
                    <div class="summary-label">What to include</div>
                    <div class="summary-value summary-value-compact">Core contact details</div>
                </div>
                <div class="summary-icon"><i class="bi bi-list-check"></i></div>
            </div>
            <div class="summary-foot">Name, company, phone, and address help your team identify the right customer quickly on every device.</div>
        </div>
    </aside>

    <div class="form-main">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-2" style="color:var(--accent);"></i>Customer Information</div>
            <div class="card-body">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Ahmed Al Hosani" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control"
                                   value="{{ old('company_name') }}" placeholder="e.g. Al Hosani Trading">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" placeholder="e.g. 0501234567">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" placeholder="e.g. ahmed@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"
                                      placeholder="Customer address...">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Any notes about this customer...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Save Customer</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
